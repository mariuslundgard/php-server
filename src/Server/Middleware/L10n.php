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
use Server\Error;

/**
 * The Localization Layer
 *
 * Takes configuration:
 * `domainPath`     -> The path to the locale files (the .po and .mo files)
 * `forceLocale`    -> If this is present, the locale will always be that.
 * `defaultLocale`  -> The fallback locale, when the locale is not supported
 * `defaultCharset` -> The default character set
 * `defaultDomain`  -> The default domain name
 */
class L10n extends Layer
{
    public function __construct(LayerInterface $next = null, array $config = array(), array $env = array())
    {
        parent::__construct($next, $config + [
            'domainPath' => null,
            'forceLocale' => null,
            'defaultLocale' => 'en_US',
            'defaultCharset' => 'UTF-8',
            'defaultDomain' => 'messages',
        ], $env);
    }

    public function call(Request $req, Error $err = null)
    {
        if (! function_exists('bindtextdomain')) {
            $this->d('Note: `gettext` is not installed on this server');

            return parent::call($req, $err);
        }

        // use force locale if it exists
        // or get preferred locale
        if ($forceLocale = $this->config['forceLocale']) {
            $locale = $forceLocale;
        } else {
            $locale = $this->getPreferredLocale($req);
        }

        // fallback to default locale
        if (! $locale) {
            $locale = $this->config['defaultLocale'];
        }

        // error: locale is NULL
        if (! $locale) {
            return parent::call($req, new Error('Missing either a `forceLocale` or `defaultLocale` parameter'));
        }

        // format for PHP
        $locale = str_replace('-', '_', $locale);

        // ln_LN formatting
        if (5 === strlen($locale)) {
            $locale = substr($locale, 0, 3).strtoupper(substr($locale, 3));
        }

        if ('nb' === $locale) {
            $locale = 'nb_NO';
        }

        $charset = $this->config->get('defaultCharset');

        $locale = $locale.'.'.$charset;

        $req->locale = $locale;

        // set text domain, codeset
        if ($domainPath = $this->config['domainPath']) {

            // get domain name
            $domain = $this->config->get('defaultDomain', 'messages');

            // set locale
            putenv('LANG=' . $locale);

            if ($locale !== setlocale(LC_MESSAGES, $locale)) {
                $this->d('Note: the locale is not supported by the system: '.$locale);
            }

            bindtextdomain($domain, $domainPath);
            textdomain($domain);
            bind_textdomain_codeset($domain, $charset);
        } else {
            throw new Error('Missing the `domainPath` parameter');
        }

        return parent::call($req, $err);
    }

    // public function acceptsLocale(Request $req, $lang)
    // {
    //     return isset($req->headers['Accept-Language'][$lang]);
    // }

    public function getPreferredLocale(Request $req)
    {
        if (! is_array($req->headers['Accept-Language'])) {
            throw new Error(
                'The `L10n` middleware requires the `AcceptParser` middleware '.
                'in order to use the `getPreferredLocale()` method'
            );
        }

        $acceptLanguage = $req->headers['Accept-Language'];
        $locales = array_keys($acceptLanguage);

        return array_shift($locales);
    }
}
