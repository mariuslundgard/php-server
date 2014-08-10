<?php

namespace Server;

class Application extends Module
{
    public function staticUrl($url)
    {
        if ('http' === strtolower(substr($url, 0, 4))) {
            return $url;
        }

        $baseUrl = $this->config['baseStaticUrl'];

        if ($baseUrl) {
            $baseUrl = trim($baseUrl, '/');
        }

        return $baseUrl.'/'.trim($url, '/');
    }
}
