<?php

namespace Yaglot\Helpers;

use Yaglot\Client\Api\Enum\BotType;

if (!defined('ABSPATH')) {
    exit;
}

class ServerHelper {

    /**
     * @param array $server
     * @param bool $use_forwarded_host
     * @return string
     */
    public static function fullUrl(array $server, $use_forwarded_host = false) {
        return self::urlOrigin($server, $use_forwarded_host) . $server['REQUEST_URI'];
    }


    /**
     * @param array $server
     * @param bool $use_forwarded_host
     * @return string
     */
    protected static function urlOrigin(array $server, $use_forwarded_host = false) {
        return self::getProtocol($server) . '://' . self::getHost($server, $use_forwarded_host);
    }


    /**
     * @param array $server
     * @return int
     */
    public static function detectBot(array $server) {

        $userAgent = self::getUserAgent($server);
        $checkBotAgent = preg_match('/bot|favicon|crawl|facebook|slurp|spider/i', $userAgent);
        $checkBotGoogle = (TextHelper::contains($userAgent, 'Google') ||
            TextHelper::contains($userAgent, 'facebook') ||
            TextHelper::contains($userAgent, 'wprocketbot') ||
            TextHelper::contains($userAgent, 'SemrushBot'));

        if ($userAgent !== null && !$checkBotAgent) {
            return BotType::HUMAN;
        }
        if ($userAgent !== null && $checkBotAgent && $checkBotGoogle) {
            return BotType::GOOGLE;
        }
        foreach (self::otherBotAgents() as $agent => $agentBot) {
            if ($userAgent !== null && $checkBotAgent && !$checkBotGoogle && Text::contains($userAgent, $agent)) {
                return $agentBot;
            }
        }

        return BotType::OTHER;
    }


    /**
     * @return array
     */
    private static function otherBotAgents() {
        return [
            'bing'   => BotType::BING,
            'yahoo'  => BotType::YAHOO,
            'Baidu'  => BotType::BAIDU,
            'Yandex' => BotType::YANDEX
        ];
    }


    /**
     * @param array $server
     * @return bool
     */
    private static function isSsl(array $server) {
        return !empty($server['HTTPS']) && $server['HTTPS'] === 'on';
    }


    /**
     * @param array $server
     * @return string
     */
    public static function getProtocol(array $server) {
        $protocol = strtolower($server['SERVER_PROTOCOL']);
        return substr($protocol, 0, strpos($protocol, '/')) . (self::isSsl($server) ? 's' : '');
    }


    /**
     * @param array $server
     * @return string
     */
    public static function getPortForUrl(array $server) {

        $ssl = self::isSsl($server);

        if ((!$ssl && self::getPort($server) === '80') ||
            ($ssl && self::getPort($server) === '443')) {
            return '';
        }
        return ':' . self::getPort($server);
    }


    /**
     * @param array $server
     * @return string
     */
    public static function getPort(array $server) {
        return $server['SERVER_PORT'];
    }


    /**
     * @param array $server
     * @param bool $use_forwarded_host
     * @return string
     */
    public static function getHost(array $server, $use_forwarded_host = false) {

        $host = null;

        if ($use_forwarded_host && isset($server['HTTP_X_FORWARDED_HOST'])) {
            $host = $server['HTTP_X_FORWARDED_HOST'];
        } elseif (isset($server['HTTP_HOST'])) {
            $host = $server['HTTP_HOST'];
        }

        if ($host === null) {
            $host = $server['SERVER_NAME'] . self::getPort($server);
        }

        return $host;
    }


    /**
     * @param array $server
     * @return string|null
     */
    public static function getUserAgent(array $server) {
        return isset($server['HTTP_USER_AGENT']) ? $server['HTTP_USER_AGENT'] : null;
    }
}
