<?php

namespace Auth;

use Server\Controller as Base;

class Controller extends Base
{
    public function showLoginPage()
    {
        $authError = $this->req->session->get('authError', array());
        $this->req->session->remove('authError');

        return $authError + array(
            'view' => 'auth/login',
            'loginUri' => 'auth/login',

            'styleSheets' => [
                'http://localhost/~mariuslundgard/body/dist/body.css'
            ],
            'scripts' => [
                'http://localhost/~mariuslundgard/body/dist/body.js'
            ],
            'bodyClassNames' => [
                'no-margin'
            ],
            'message' => 'Log in',
            'fieldErrors' => null,
            'data' => null
        );
    }

    public function login()
    {
        if ($this->req->session['isLoggedIn']) {
            $this->goToPath('/');
            return;
        }
        try {
            $cred = $this->getCredentials();
            if ('root' === $cred['username'] && 'vagrant' === $cred['password']) {
                $this->req->session['isLoggedIn'] = true;
                $this->goToPath('/');
                return;
            }
            $this->req->session->flash('authError', array(
                'message' => 'Incorrect credentials',
                'data' => $this->req->data->get()
            ));
        } catch (Error $err) {
            $this->req->session->flash('authError', $err->dump() + array(
                'data' => $this->req->data->get()
            ));
            $this->returnToLoginPage();
            return;
        }
        $this->returnToLoginPage();
    }

    protected function getCredentials()
    {
        $err = array(
            'username' => array(),
            'password' => array(),
        );

        $username = trim($this->req->data['username']);
        $password = trim($this->req->data['password']);

        if (empty($username)) {
            $err['username'][] = 'Username cannot be empty';
        }

        if (empty($password)) {
            $err['password'][] = 'Password cannot be empty';
        }

        if (count($err['username']) || count($err['password'])) {
            throw new Error('Invalid credentials', $err);
        }

        return compact('username', 'password');
    }

    protected function returnToLoginPage()
    {
        $loginPath = 'auth/login';
        // $loginPath = $this->app->master->getRealPath('auth/login');

        // THIS IS BUGGY
        // if ($referrer = $this->req->headers['Referer']) {
        //     $info = parse_url($referrer);
        //     if ($info['path'] === $loginPath) {
        //         $this->res->redirect('javascript://history.go(-1)');
        //         return;
        //     }
        // }

        // $this->res->redirect($loginPath);
        $this->goToPath($loginPath);
    }

    protected function goToPath($path = '/')
    {
        $goToPath = $this->app->master->getRealPath($path);
        $this->res->redirect($goToPath);
    }
}
