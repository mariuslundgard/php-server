<?php

namespace Server;

class Application extends Module
{
    public function staticUrl($url)
    {
        $baseUrl = $this->config['baseStaticUrl'];

        if ($baseUrl) {
            $baseUrl = trim($baseUrl, '/');
        }

        return $baseUrl.'/'.trim($url, '/');
    }
}
