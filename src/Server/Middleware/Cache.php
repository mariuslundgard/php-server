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
            'use' => true,
            'pattern' => '{:scheme}://{:host}{:uri}',
            'keyPrefix' => '',
            'defaultTimeout' => 10 //24 * 60 * 60 // 1 day
        ], $env);

        $this->getDebugger([
            'color' => 'red',
        ]);
    }

    public function read(Request $req)
    {
        $this->d('READ CACHE');

        $cachePathName = $this->getCachePath($req);

        if (file_exists($cachePathName)) {
            $this->d('FOUND CACHE');

            if ($res = $this->unpackResponse($req, file_get_contents($cachePathName))) {
                $this->setLastModifiedHeaderFromTimestamp($res, filemtime($cachePathName));

                return $res;
            }
        }

        return null;
    }

    public function write(Request $req, Response $res, $timeout)
    {
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

    public function call(Request $req, Error $err = null)
    {
        d('CACHE LAYER');

        if ($err) {
            return parent::call($req, $err);
        }

        if (! $this->config['dirPath']) {
            // return parent::call($req, new Error('Missing `dirPath` parameter for the cache layer'));
            throw new Error('Missing `dirPath` parameter for the cache layer');
        }

        // skip when `use` is FALSE and on errors
        if (! $this->config->get('use', true) && $err) {
            return parent::call($req, $err);
        }

        $res = null;

        // var_dump($req->headers->get('Cache-Control'));
        // exit;

        // $connection = $req->headers['Connection'];
        // $maxAge = isset($req->headers['Cache-Control']['max-age'])
        //     ? $req->headers['Cache-Control']['max-age']
        //     : $this->config['defaultTimeout'];

        if ($this->requestIsCacheable($req)) {

            // parse the `Cache-Control` header
            if ($header = $req->headers['Cache-Control']) {
                $req->headers['Cache-Control'] = HttpParser::parseDict($header);
            }

            $maxAge = intval($req->headers->get('Cache-Control.max-age', $this->config['defaultTimeout']));

            // if (0 === $maxAge) {
            //     $this->d('IGNORE CACHE');
            //     return parent::call($req);
            // }

            $this->d('MAX AGE = '.$maxAge);

            // get dates
            $ifModifiedSince = strtotime($req->headers['If-Modified-Since']);
            $this->d('IF MODIFIED SINCE `'.date('Y-m-d H:i:s', $ifModifiedSince).'`');
            // $this->d($res->headers->get());

            $res = $this->read($req);

            if (! $res) {
                $this->d('GENERATE CACHE');

                if ($res = parent::call($req)) {
                    $this->d('WRITE CACHE');

                    $this->write($req, $res, $maxAge);
                }
            }

            $this->d('RETURN CACHED RESPONSE');

            // var_dump($maxAge);
            // $this->d('-- REQUEST');
            // $this->d($req->headers->get());
            // $this->d('-- RESPONSE');
            // $this->d($res->headers->get());
            // $this->d(htmlspecialchars($res->body));
            // exit;
            $lastModified = strtotime($res->headers['Last-Modified']);
            $this->d('LAST MODIFIED `'.date('Y-m-d H:i:s', $lastModified).'`');

            // send a 304 response if nothing has changed
            // note: this will send an empty body!
            if ($lastModified && $ifModifiedSince) {
                if ($lastModified <= $ifModifiedSince) {
                    $this->d('====> CACHE IS NOT MODIFIED');
                    $res->status = 304;
                    // var_dump($res);
                    // exit;
                } else {
                    $this->d('CACHE IS MODIFIED');
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
        }

        return $res ? $res : parent::call($req, $err);
    }

    public function setLastModifiedHeaderFromTimestamp(Response $res, $timestamp)
    {
        $res->headers['Last-Modified'] = gmdate('D, d M Y H:i:s ', $timestamp) . 'GMT';
    }

    public function requestIsCacheable(Request $req)
    {
        return is_dir($this->config['dirPath']) && in_array($req->method, ['GET', 'HEAD']);
    }

    public function getCacheKey(Request $req)
    {
        $id = str_insert($this->config['pattern'], $req->dump() + array(
            'host' => $this->master->env['HTTP_HOST'],
        ));

        return $this->config['keyPrefix'].md5($id);
    }

    public function getCachePath(Request $req)
    {
        return $this->config['dirPath'].'/'.$this->getCacheKey($req);
    }

    public function unpackResponse(Request $req, $data)
    {
        $this->d('UNPACKING');

        $data = unserialize($data);

        if (! isset($data['headers']) || ! isset($data['body'])) {
            throw new Error('The cache data is corrupted');
        }

        $this->d('GOT CACHE');
        // exit;
        $this->d('CACHE EXISTS');

        // create an empty response
        $res = new Response($req);

        $res->headers->merge($data['headers']);
        $res->body = $data['body'];

        return $res;

        // return $res;
    }

    public function packResponse(Response $res)
    {
        return serialize([
            'headers' => $res->headers->get(),
            'body' => $res->body,
        ]);
    }
}
