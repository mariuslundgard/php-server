<?php

/*
 * This file is part of the Server framework package for PHP.
 *
 * (c) Marius Lundgård <studio@mariuslundgard.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Server\Middleware;

use Server\Layer;
use Server\LayerInterface;
use Server\Request;
use Server\Response;
use Server\HttpParser;
use Server\Error;

/**
 * The cache layer.
 *
 * @author  Marius Lundgård <studio@mariuslundgard.com>
 */
class Cache extends Layer
{
    public function __construct(LayerInterface $next = null, array $config = [], array $env = array())
    {
        parent::__construct($next, $config + [
            'dirPath' => null,
            // 'dirPath' => '/tmp',
            'adapter' => 'file',
            'useResponseCache' => true,
            'pattern' => '{:scheme}://{:host}{:uri}',
            'keyPrefix' => '',
            'defaultTimeout' => 10 //24 * 60 * 60 // 1 day
        ], $env);

        $this->getDebugger([
            'color' => 'red',
        ]);
    }

    public function call(Request $req, Error $err = null)
    {
        if (! $this->config['useResponseCache'] || (property_exists($req, 'ignoreCache') && $req->ignoreCache)) {
            return $this->getNextResponse($req, $err);
        }

        if (! $this->config['dirPath']) {
            // return parent::call($req, new Error('Missing `dirPath` parameter for the cache layer'));
            throw new Error('Missing `dirPath` parameter for the cache layer');
        }

        if ($err /*|| ! $this->config['use'] */ || ! $this->requestIsCacheable($req)) {
            return $this->getNextResponse($req, $err);
        }

        $req->cache = $this;

        // skip when `use` is FALSE and on errors
        // if (! $this->config->get('use', true) && $err) {
            // return parent::call($req, $err);
        // }

        // var_dump($req->headers->get('Cache-Control'));
        // exit;

        // $connection = $req->headers['Connection'];
        // $maxAge = isset($req->headers['Cache-Control']['max-age'])
        //     ? $req->headers['Cache-Control']['max-age']
        //     : $this->config['defaultTimeout'];

        // ;
        return ($res = $this->getCachedResponse($req)) ? $res : parent::call($req);
    }

    public function getCachedResponse(Request $req)
    {
        // parse the `Cache-Control` header
        if ($header = $req->headers['Cache-Control']) {
            $req->headers['Cache-Control'] = HttpParser::parseDict($header);
        }

        $maxAge = intval($req->headers->get('Cache-Control.max-age', $this->config['defaultTimeout']));

        // $this->d('MAX AGE = '.$maxAge);

        // get dates
        if ($ifModifiedSince = $req->headers['If-Modified-Since']) {
            $this->d('IF MODIFIED SINCE `'.$ifModifiedSince.'`');
            $ifModifiedSince = strtotime($ifModifiedSince);
        }

        $res = $this->read($req);

        if (! $res) {
            if ($res = parent::call($req)) {

                if (400 <= $res->status) {
                    return $res;
                }

                $this->d('OK TO STORE CACHE');
                $this->write($req, $res, $maxAge);
            }
        }

        if ($lastModified = $res->headers['Last-Modified']) {
            $this->d('CONTENT LAST MODIFIED `'.$lastModified.'`');
            $lastModified = strtotime($lastModified);
        }

        // send a 304 response if nothing has changed
        // note: this will send an empty body!
        if ($lastModified && $ifModifiedSince) {
            if ($lastModified <= $ifModifiedSince) {
                $this->d('CONTENT NOT MODIFIED');
                $res->status = 304;
            } else {
                $this->d('CONTENT WAS MODIFIED');
            }
        }

        /*
        // set the `Cache-Control` header
        $res->headers['Cache-Control'] = [
            // 'no-cache',
            // 'no-store',
            // 'max-age' => 1, // 1 second
            'max-age' => 60 * 60 * 24, // 1 day
            // 'min-fresh' => 60,
            // 'no-transform',
            // 'only-if-cached',
            'public',
            // 'private',
            // 'no-cache' (=field_name),
            // 'no-transform',
            // 'must-revalidate',
            // 'proxy-revalidate',
            // 'max-age' => 60,
            // 's-maxage' => 60,
            // 'post-check' => 0,
            // 'pre-check' => 0,
            // 'no-cache'
        ];
        */

        return $res;
    }

    public function read(Request $req)
    {
        $this->d('ATTEMPT TO READ CACHE');

        $cachePathName = $this->getCachePath($req);

        if (file_exists($cachePathName)) {
            $this->d('FOUND CACHE');

            if ($res = $this->unpackResponse($req, file_get_contents($cachePathName))) {
                $this->setLastModifiedHeaderFromTimestamp($res, filemtime($cachePathName));

                return $res;
            }
        }

        $this->d('NO CACHE FOUND');

        return null;
    }

    public function write(Request $req, Response $res, $timeout)
    {
        $this->d('ATTEMPT TO WRITE CACHE');

        //
        $cachePathName = $this->getCachePath($req);

        // set the `Expires` header
        $res->headers['Expires'] = gmdate('D, d M Y H:i:s ', time() + $timeout) . 'GMT';

        // write to cache
        if (! @file_put_contents($cachePathName, $this->packResponse($res))) {
            throw new Error('Could not write cache: '.$cachePathName);
        }

        $this->setLastModifiedHeaderFromTimestamp($res, filemtime($cachePathName));

        // set the `Accept-Ranges` header
        // $res->headers['Accept-Ranges'] = 'bytes';

        // check protocol version
        if ('1.0' === $req->version) {

            // set the `Pragma` header
            $res->headers['Pragma'] = 'cache';
        }

        // set the `Vary` header
        // `Cookie`
        // `Accept`
        // $res->headers['Vary'] = 'Accept-Encoding';
    }

    public function setLastModifiedHeaderFromTimestamp(Response $res, $timestamp)
    {
        $res->headers['Last-Modified'] = gmdate('D, d M Y H:i:s ', $timestamp) . 'GMT';
    }

    public function requestIsCacheable(Request $req)
    {
        return in_array($req->method, ['GET', 'HEAD']);
    }

    public function getCacheKey(Request $req)
    {
        $id = str_insert($this->config['pattern'], $req->dump() + array(
            'host' => $this->master->env['HTTP_HOST'],
            'locale' => property_exists($req, 'locale') ? $req->locale : '',
        ));

        // return $this->config['keyPrefix'].md5($id);
        return $this->config['keyPrefix'].
            preg_replace(
                '/\-+/',
                '-',
                strtolower(str_replace('.', '-', str_replace(':', '-', str_replace('/', '-', $id))))
            );
    }

    public function getCachePath(Request $req)
    {
        return $this->config['dirPath'].'/'.$this->getCacheKey($req);
    }

    public function unpackResponse(Request $req, $data)
    {
        $this->d('UNPACK CACHED RESPONSE');

        $data = unserialize($data);

        if (! isset($data['headers']) || ! isset($data['body'])) {
            throw new Error('The cache data is corrupted');
        }

        // create an empty response
        $res = new Response($req);

        $res->headers->merge($data['headers']);
        $res->body = $data['body'];

        return $res;
    }

    public function packResponse(Response $res)
    {
        $this->d('PACK RESPONSE CACHE');

        return serialize([
            'headers' => $res->headers->get(),
            'body' => $res->body,
        ]);
    }
}
