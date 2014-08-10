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

class CsrfProtection extends Layer
{
    public function call(Request $req, Error $err = null)
    {
        // d(get_class($this) . '->call( ' .$req->method . ' ' .$req->path. ' ) -> ' . get_class($this->next));

        if ($err) {
            return parent::call($req, $err);
        }

        // d(get_called_class());
        return parent::call($req);
    }
    // const REASON_NO_REFERER = "Referer checking failed - no Referer.";
    // const REASON_BAD_REFERER = "Referer checking failed - %s does not match %s.";
    // const REASON_NO_CSRF_COOKIE = "CSRF cookie not set.";
    // const REASON_BAD_TOKEN = "CSRF token missing or incorrect.";

    // const CSRF_KEY_LENGTH = 32;

    // public function call(array $env)
    // {
    //     if (@$env['req']->META['csrf_processing_done']) {
    //         return null; // TODO: throw exception?
    //     }

    //     try {
    //         $csrfToken = @$env['req']->COOKIES[$env['config']['CSRF_COOKIE_NAME']];
    //         $csrfToken = $this->_sanitizeToken($csrfToken);
    //         $env['req']->META['CSRF_COOKIE'] = $csrfToken;
    //     }
    //     catch (\Exception $e) {
    //         //
    //     }

    //     // # Assume that anything not defined as 'safe' by RFC2616 needs protection
    //     if (!in_array($env['req']->method, ['GET', 'HEAD', 'OPTIONS', 'TRACE'])) {

    //     //     if getattr(request, '_dont_enforce_csrf_checks', False):
    //     //         # Mechanism to turn off CSRF checks for test suite.
    //     //         # It comes after the creation of CSRF cookies, so that
    //     //         # everything else continues to work exactly the same
    //     //         # (e.g. cookies are sent, etc.), but before any
    //     //         # branches that call reject().
    //     //         return self._accept(request)

    //         if ($env['req']->isSecure) {
    //     //         # Suppose user visits http://example.com/
    //     //         # An active network attacker (man-in-the-middle, MITM) sends a
    //     //         # POST form that targets https://example.com/detonate-bomb/ and
    //     //         # submits it via JavaScript.
    //     //         #
    //     //         # The attacker will need to provide a CSRF cookie and token, but
    //     //         # that's no problem for a MITM and the session-independent
    //     //         # nonce we're using. So the MITM can circumvent the CSRF
    //     //         # protection. This is true for any HTTP connection, but anyone
    //     //         # using HTTPS expects better! For this reason, for
    //     //         # https://example.com/ we need additional protection that treats
    //     //         # http://example.com/ as completely untrusted. Under HTTPS,
    //     //         # Barth et al. found that the Referer header is missing for
    //     //         # same-domain requests in only about 0.2% of cases or less, so
    //     //         # we can use strict Referer checking.
    //             $referer = $env['req']->META['HTTP_REFERER'];
    //             if (null === $referer) {
    //                 return $this->_reject($env['req'], self::REASON_NO_REFERER);
    //                 // return self._reject(request, REASON_NO_REFERER)
    //             }

    //     //         # Note that request.get_host() includes the port.
    //             $goodReferer = sprintf('https://%s/', $env['req']->host);

    //             // d($referer);
    //             // d($goodReferer);
    //             // exit;
    //     //         if not same_origin(referer, good_referer):
    //     //             reason = REASON_BAD_REFERER % (referer, good_referer)
    //     //             return self._reject(request, reason)
    //         }

    //     //     if csrf_token is None:
    //     //         # No CSRF cookie. For POST requests, we insist on a CSRF cookie,
    //     //         # and in this way we can avoid all CSRF attacks, including login
    //     //         # CSRF.
    //     //         return self._reject(request, REASON_NO_CSRF_COOKIE)

    //     //     # Check non-cookie token for match.
    //     //     request_csrf_token = ""
    //     //     if request.method == "POST":
    //     //         request_csrf_token = request.POST.get('csrfmiddlewaretoken', '')

    //     //     if request_csrf_token == "":
    //     //         # Fall back to X-CSRFToken, to make things easier for AJAX,
    //     //         # and possible for PUT/DELETE.
    //     //         request_csrf_token = request.META.get('HTTP_X_CSRFTOKEN', '')

    //     //     if not constant_time_compare(request_csrf_token, csrf_token):
    //     //         return self._reject(request, REASON_BAD_TOKEN)
    //     //     }

    //     }
    //     // return self._accept(request)

    //     $res = parent::call($req, $err);

    //     // if getattr(response, 'csrf_processing_done', False):
    //     //     return response

    //     return $res;
    // }

    // public function getToken(&$req)
    // {
    //     $req->META['CSRF_COOKIE_USED'] = true;

    //     return isset($req->META['CSRF_COOKIE'])
    //         ? $req->META['CSRF_COOKIE']
    //         : null;
    // }

    // public function rotateToken(&$req)
    // {
    //     $req->META['CSRF_COOKIE_USED'] = true;
    //     $req->META['CSRF_COOKIE'] = $this->_getNewCsrfKey();
    // }

    // protected function _getNewCsrfKey()
    // {
    //     return $this->_getRandomString(self::CSRF_KEY_LENGTH);
    // }

    // protected function _getRandomString($length = 8)
    // {
    //     $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    //     $randStr = '';

    //     for ($i = 0; $i < $length; $i++) {
    //         $randStr .= $chars[rand(0, strlen($chars) - 1)];
    //     }

    //     return $randStr;
    // }

    // protected function _sanitizeToken($token)
    // {
    //     $token = (string) $token;

    //     if (strlen($token) > self::CSRF_KEY_LENGTH) {
    //         return $this->_getNewCsrfKey();
    //     }

    //     // Allow only alphanum
    //     $token = preg_replace('/[^a-zA-Z0-9]+/', '', $token);

    //     // In case the cookie has been truncated to nothing at some point.
    //     if ($token == "") {
    //         $token = $this->_getNewCsrfKey();
    //     }

    //     return $token;
    // }
}
