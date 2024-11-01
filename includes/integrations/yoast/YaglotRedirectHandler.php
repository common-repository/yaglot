<?php

namespace Yaglot\Integrations\Yoast;

use Yaglot\Services\YaglotLanguagesService;
use Yaglot\Services\YaglotRequestUrlService;

if (!defined('ABSPATH')) {
    exit;
}

class YaglotRedirectHandler extends \WPSEO_Redirect_Handler {


    /**
     * @var YaglotLanguagesService
     */
    private $languagesService;


    /**
     * @var YaglotRequestUrlService
     */
    private $requestUrlService;


    /**
     * YaglotRedirectHandler constructor.
     * @param YaglotLanguagesService $languagesService
     * @param YaglotRequestUrlService $requestUrlService
     * @param string $originalLanguage
     * @param string $currentLanguage
     */
    public function __construct(YaglotLanguagesService $languagesService,
                                YaglotRequestUrlService $requestUrlService) {

        $this->languagesService = $languagesService;
        $this->requestUrlService = $requestUrlService;
    }


    /**
     * The options where the URL redirects are stored.
     *
     * @var string
     */
    private $normal_option_name = 'wpseo-premium-redirects-export-plain';


    /**
     * The option name where the regex redirects are stored.
     *
     * @var string
     */
    private $regex_option_name = 'wpseo-premium-redirects-export-regex';


    /**
     * The URL that is called at the moment.
     * @var string
     */
    protected $request_url = '';


    public function load() {

        // Only handle the redirect when the option for php redirects is enabled.
        if (!$this->load_php_redirects()) {
            return;
        }

        // Set the requested URL.
        $this->set_request_url();

        // Check the normal redirects.
        $this->handle_normal_redirects($this->request_url);

        do_action('yaglot_another_redirect_override', $this->request_url);
    }

    /**
     * @return void
     */
    protected function set_request_url() {
        $this->request_url = $this->get_request_uri();
    }


    /**
     * Checks if the current URL matches a normal redirect.
     *
     * @param string $request_url The request url to look for.
     *
     * @return void
     */
    protected function handle_normal_redirects($request_url) {

        $redirects = $this->get_redirects($this->normal_option_name);
        $this->redirects = $this->normalize_redirects($redirects);

        if ('/' !== $request_url) {
            $request_url = trim($request_url, '/');
        }

        $redirect_url = '';

        if (isset($request_url[2]) && '/' === $request_url[2]) {
            $code_language = explode('/', $request_url);

            $langs = $this->languagesService->getLanguages();

            if (!isset($langs[$code_language[0]])) {
                $redirect_url = $this->find_url($request_url);
            } else {
                $redirect_url = str_replace($code_language[0] . '/', '', $request_url);
                $redirect_url = $this->find_url($redirect_url);

                if (!empty($redirect_url)) {

                    $eligible_url = $redirect_url['url'];
                    if ('/' !== $eligible_url[0]) {
                        $eligible_url = '/' . $eligible_url;
                    }

                    if (substr($eligible_url, -1) !== '/') {
                        $eligible_url .= '/';
                    }

                    if ($this->requestUrlService->isEligibleUrl($eligible_url)) {
                        $redirect_url['url'] = sprintf('%s/%s', $code_language[0], $redirect_url['url']);
                    }
                }
            }
        }

        if (!empty($redirect_url)) {
            $this->is_redirected = true;
            $this->do_redirect($redirect_url['url'], $redirect_url['type']);
        }
    }
}
