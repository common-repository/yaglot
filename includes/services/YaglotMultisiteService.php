<?php

namespace Yaglot\Services;

if (!defined('ABSPATH')) {
    exit;
}


class YaglotMultisiteService {


    /**
     * @var YaglotRequestUrlService
     */
    private $requestUrlService;


    /**
     * YaglotMultisiteService constructor.
     * @param YaglotRequestUrlService $requestUrlService
     */
    public function __construct(YaglotRequestUrlService $requestUrlService) {
        $this->requestUrlService = $requestUrlService;
    }


    /**
     * @return array
     */
    public function getListOfNetworkPath() {

        $paths = [];

        if (is_multisite()) {

            $sites = get_sites([
                'number' => 0,
            ]);

            foreach ($sites as $site) {
                $path = $site->path;
                array_push($paths, $path);
            }

        } else {
            array_push($paths, $this->requestUrlService->getHomeWordpressDirectory());
        }

        return $paths;
    }

}