<?php

namespace Yaglot\Services;

if (!defined('ABSPATH')) {
    exit;
}

use Yaglot\Entities\YaglotKeyInfo;
use Yaglot\Exceptions\ServerErrorException;


class YaglotProjectService {


    /**
     * @var string
     */
    private $path = "api/client/info";


    /**
     * @param string $key
     * @return YaglotKeyInfo
     * @throws ServerErrorException
     */
    public function getInfo($key) {

        $response = wp_remote_get(YAGLOT_DASHBOARD_URL . $this->path, [
            'headers' => [
                'X-Yaglot-Project-Key' => $key
            ]
        ]);
        $result = json_decode(wp_remote_retrieve_body($response), true);
        $code = (isset($result['code'])) ? $result['code'] : wp_remote_retrieve_response_code($response);
        $message = (isset($result['message'])) ? $result['message'] : wp_remote_retrieve_response_message($response);

        if ($code === 401) {
            throw new ServerErrorException("Entered API key is invalid", $code);
        }

        if ($code > 200) {
            throw new ServerErrorException("Can't load API key info from remote server: " . $message, $code);
        }

        if (!(isset($result['data']) && is_array($result['data']))) {
            throw new ServerErrorException("Can't load API key info from remote server", 500);
        }

        return new YaglotKeyInfo($result['data']);
    }


}