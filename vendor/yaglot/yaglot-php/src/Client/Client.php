<?php

namespace Yaglot\Client;

use Psr\Cache\CacheItemPoolInterface;
use Psr\Http\Message\ResponseInterface;
use Yaglot\Client\Api\Exception\ApiError;
use Yaglot\Client\Caching\Cache;
use Yaglot\Client\Caching\CacheInterface;
use Yaglot\Client\HttpClient\ClientInterface;
use Yaglot\Client\HttpClient\CurlClient;

/**
 * Class Client
 * @package Yaglot\Client
 */
class Client
{
    /**
     * Library version
     *
     * @var string
     */
    const VERSION = '0.0.1';

    /**
     * Yaglot API Key
     *
     * @var string
     */
    protected $apiKey;

    /**
     * Options for client
     *
     * @var array
     */
    protected $options;

    /**
     * Http Client
     *
     * @var ClientInterface
     */
    protected $httpClient;

    /**
     * @var Profile
     */
    protected $profile;

    /**
     * @var CacheInterface
     */
    protected $cache;

    /**
     * Client constructor.
     * @param string    $apiKey     your Yaglot API key
     * @param array     $options    an array of options, currently only "host" is implemented
     */
    public function __construct($apiKey, $options = [])
    {
        $this->apiKey = $apiKey;
        $this->profile = new Profile($apiKey);

        $this
            ->setHttpClient()
            ->setOptions($options)
            ->setHttpAuth()
            ->prepareHost()
            ->setCache();
    }

    /**
     * Creating Guzzle HTTP connector based on $options
     */
    protected function setupConnector()
    {
        $this->httpClient = new CurlClient();
    }

    /**
     * Default options values
     *
     * @return array
     */
    public function defaultOptions()
    {
        return [
            'host'  => 'https://api.yaglot.com',
            'apiHost'  => 'https://api.yaglot.com',
            'dashboardHost'  => 'https://dashboard.yaglot.com',
            'requestTarget' => 'api'
        ];
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param array $options
     * @return $this
     */
    public function setOptions($options)
    {
        // merging default options with user options
        $this->options = array_merge($this->defaultOptions(), $options);
        return $this;
    }

    /**
     * @return $this
     */
    public function prepareHost()
    {
        switch ($this->options['requestTarget']) {
            case "dashboard":
                $this->options['host'] = $this->options['dashboardHost'];
                break;
            case "api":
                $this->options['host'] = $this->options['apiHost'];
                break;
        }

        return $this;
    }

    /**
     * @param null|ClientInterface $httpClient
     * @param null|string $customHeader
     * @return $this
     */
    public function setHttpClient($httpClient = null, $customHeader = null)
    {
        if ($httpClient === null) {
            $httpClient = new CurlClient();

            $header = 'Yaglot-Context: PHP\\'.self::VERSION;
            if (!is_null($customHeader)) {
                $header .= ' ' .$customHeader;
            }
            $httpClient->addHeader($header);
        }
        if ($httpClient instanceof ClientInterface) {
            $this->httpClient = $httpClient;
        }
        return $this;
    }

    /**
     * @return $this
     */
    public function setHttpAuth()
    {
        switch ($this->options['requestTarget']) {
            case "dashboard":
                $this->httpClient->addHeader('X-Yaglot-Project-Key: ' . $this->apiKey);
                break;
            case "api":
                $this->httpClient->addHeader('X-Authorization: ' . $this->apiKey);
                break;
        }
        return $this;
    }

    /**
     * @return ClientInterface
     */
    public function getHttpClient()
    {
        return $this->httpClient;
    }

    /**
     * @return Profile
     */
    public function getProfile()
    {
        return $this->profile;
    }

    /**
     * @param null|CacheInterface $cache
     * @return $this
     */
    public function setCache($cache = null)
    {
        if ($cache === null || !($cache instanceof CacheInterface)) {
            $cache = new Cache();
        }

        $this->cache = $cache;

        return $this;
    }

    /**
     * @return CacheInterface
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * @param null|CacheItemPoolInterface $cacheItemPool
     * @return $this
     */
    public function setCacheItemPool($cacheItemPool)
    {
        $this->getCache()->setItemPool($cacheItemPool);

        return $this;
    }

    /**
     * Make the API call and return the response.
     *
     * @param string $method    Method to use for given endpoint
     * @param string $endpoint  Endpoint to hit on API
     * @param array $body       Body content of the request as array
     * @param bool $asArray     To know if we return an array or ResponseInterface
     * @return array|ResponseInterface
     * @throws ApiError
     */
    public function makeRequest($method, $endpoint, $body = [], $asArray = true)
    {
        try {
            list($rawBody, $httpStatusCode, $httpHeader) = $this->getHttpClient()->request(
                $method,
                $this->makeAbsUrl($endpoint),
                [],
                $body
            );

            $array = json_decode($rawBody, true);
        } catch (\Exception $e) {
            throw new ApiError($e->getMessage(), $body);
        }

        if ($asArray) {
            return $array;
        }
        return [
            $rawBody,
            $httpStatusCode,
            $httpHeader,
            $array
        ];
    }

    /**
     * @param string $endpoint
     * @return string
     */
    protected function makeAbsUrl($endpoint)
    {
        return $this->options['host'] . $endpoint;
    }
}
