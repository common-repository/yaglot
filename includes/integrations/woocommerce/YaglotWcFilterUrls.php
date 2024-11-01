<?php

namespace Yaglot\Integrations\Woocommerce;

use Yaglot\Helpers\UrlFilterHelper;
use Yaglot\Services\YaglotOptionsService;
use Yaglot\Services\YaglotRequestUrlService;

if (!defined('ABSPATH')) {
    exit;
}

class YaglotWcFilterUrls {


    /**
     * @var YaglotOptionsService
     */
    private $optionsService;


    /**
     * @var YaglotRequestUrlService
     */
    private $requestUrlService;

    /**
     * @var string
     */
    private $originalLanguage;


    /**
     * @var string
     */
    private $currentLanguage;


    /**
     * YaglotWcFilterUrls constructor.
     * @param YaglotOptionsService $optionsService
     * @param YaglotRequestUrlService $requestUrlService
     * @param string $originalLanguage
     * @param string $currentLanguage
     */
    public function __construct(YaglotOptionsService $optionsService,
                                YaglotRequestUrlService $requestUrlService,
                                $originalLanguage,
                                $currentLanguage) {

        $this->optionsService = $optionsService;
        $this->requestUrlService = $requestUrlService;
        $this->originalLanguage = $originalLanguage;
        $this->currentLanguage = $currentLanguage;

        $this->hooks();
    }

    /**
     * @return void
     */
    public function hooks() {

        add_filter('woocommerce_get_cart_url', [$this, 'woocommerceFilterUrlWithoutAjax']);
        add_filter('woocommerce_get_checkout_url', [$this, 'woocommerceFilterUrlWithoutAjax']);
        add_filter('woocommerce_payment_successful_result', [$this, 'woocommerceFilterUrlArray']);
        add_filter('woocommerce_get_checkout_order_received_url', [$this, 'woocommerceFilterOrderReceivedUrl']);
        add_action('woocommerce_reset_password_notification', [$this, 'woocommerceFilterResetPassword']);
        add_filter('woocommerce_login_redirect', [$this, 'woocommerceFilterUrlLogRedirect']);
        add_filter('woocommerce_registration_redirect', [$this, 'woocommerceFilterUrlLogRedirect']);
    }


    /**
     * @param string $urlFilter
     * @return string
     */
    public function woocommerceFilterUrlWithoutAjax($urlFilter) {
        return UrlFilterHelper::filterUrlWithoutAjax($urlFilter, $this->currentLanguage, $this->originalLanguage, $this->requestUrlService);
    }


    /**
     * @param string $urlFilter
     * @return string
     */
    public function woocommerceFilterUrlLogRedirect($urlFilter) {
        return UrlFilterHelper::filterUrlLogRedirect($urlFilter, $this->currentLanguage, $this->originalLanguage, $this->requestUrlService);
    }


    /**
     * Filter woocommerce order received URL
     *
     * @param string $url_filter
     * @return string
     */
    public function woocommerceFilterOrderReceivedUrl($url_filter) {

        $choose_current_language = $this->currentLanguage;
        $url = $this->requestUrlService->createUrlObject($url_filter);

        if ($this->currentLanguage !== $this->originalLanguage) {

            if (substr(get_option('permalink_structure'), -1)) {
                return str_replace('/?key', '?key', $url->getForLanguage($choose_current_language));
            } else {
                return str_replace('//?key', '/?key', str_replace('?key', '/?key', $url->getForLanguage($choose_current_language)));
            }
        } else {
            if (isset($_SERVER['HTTP_REFERER'])) {

                $choose_current_language = $url->detectCurrentLanguage();
                if ($choose_current_language && $choose_current_language !== $this->originalLanguage) {
                    if (substr(get_option('permalink_structure'), -1) !== '/') {
                        return str_replace('/?key', '?key', $url->getForLanguage($choose_current_language));
                    } else {
                        return str_replace('//?key', '/?key', str_replace('?key', '/?key', $url->getForLanguage($choose_current_language)));
                    }
                }
            }
        }
        return $url_filter;
    }


    /**
     * Filter array woocommerce filter with optional Ajax
     *
     * @param array $result
     * @return array
     */
    public function woocommerceFilterUrlArray($result) {

        $choose_current_language = $this->currentLanguage;
        if ($this->currentLanguage !== $this->originalLanguage) {
            $url = $this->requestUrlService->createUrlObject($result['redirect']);
        } else {
            if (isset($_SERVER['HTTP_REFERER'])) {
                $url = $this->requestUrlService->createUrlObject($_SERVER['HTTP_REFERER']); //phpcs:ignore
                $choose_current_language = $url->detectCurrentLanguage();
                $url = $this->requestUrlService->createUrlObject($result['redirect']);
            }
        }
        $result['redirect'] = $url->getForLanguage($choose_current_language);

        return $result;
    }


    /**
     * Redirect URL Lost password for WooCommerce
     *
     * @param mixed $url
     */
    public function woocommerceFilterResetPassword($url) {

        if ($this->currentLanguage === $this->originalLanguage) {
            return $url;
        }

        $url_redirect = add_query_arg('reset-link-sent', 'true', wc_get_account_endpoint_url('lost-password'));
        $url_redirect = $this->requestUrlService->createUrlObject($url_redirect);

        wp_redirect($url_redirect->getForLanguage($this->currentLanguage));

        exit;
    }
}
