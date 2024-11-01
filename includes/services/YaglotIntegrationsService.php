<?php

namespace Yaglot\Services;

use Yaglot\Integrations\Amp\YaglotAmpHandler;
use Yaglot\Integrations\Woocommerce\YaglotWcFilterUrls;
use Yaglot\Integrations\Woocommerce\YaglotWcTranslate;
use Yaglot\Integrations\Yoast\YaglotRedirectHandler;

if (!defined('ABSPATH')) {
    exit;
}


class YaglotIntegrationsService {


    /**
     * @var YaglotLanguagesService
     */
    private $languagesService;


    /**
     * @var YaglotRequestUrlService
     */
    private $requestUrlService;


    /**
     * @var YaglotOptionsService
     */
    private $optionsService;


    /**
     * @var string
     */
    private $originalLanguage;


    /**
     * @var string
     */
    private $currentLanguage;


    /**
     * @var bool
     */
    private $ampIntegrated = false;


    /**
     * @var bool
     */
    private $yoastIntegrated = false;


    /**
     * @var bool
     */
    private $woocommerceIntegrated = false;


    /**
     * YaglotIntegrationsService constructor.
     * @param YaglotLanguagesService $languagesService
     * @param YaglotRequestUrlService $requestUrlService
     * @param YaglotOptionsService $optionsService
     * @param string $originalLanguage
     * @param string $currentLanguage
     */
    public function __construct(YaglotLanguagesService $languagesService,
                                YaglotRequestUrlService $requestUrlService,
                                YaglotOptionsService $optionsService,
                                $originalLanguage,
                                $currentLanguage) {

        $this->languagesService = $languagesService;
        $this->requestUrlService = $requestUrlService;
        $this->optionsService = $optionsService;
        $this->originalLanguage = $originalLanguage;
        $this->currentLanguage = $currentLanguage;

        $this->loadYoastSeoIntegration();
        $this->loadWoocommerceIntegration();
        $this->loadAmpIntegration();
    }

    /**
     * @return bool
     */
    public function isAmpIntegrated() {
        return $this->ampIntegrated;
    }


    private function loadAmpIntegration() {

        if ( ! defined( 'AMPFORWP_PLUGIN_DIR' )
            && ! defined( 'AMP__VERSION' ) ) {

            return;
        }

        require_once yaglot_path([ 'includes', 'integrations', 'amp', 'YaglotAmpHandler.php' ]);

        $this->ampIntegrated = true;

        new YaglotAmpHandler();
    }


    /**
     * @return bool
     */
    public function isYoastIntegrated() {
        return $this->yoastIntegrated;
    }

    /**
     * @return void
     */
    private function loadYoastSeoIntegration() {

        $dirYoastPremium = plugin_dir_path(YAGLOT_DIR) . 'wordpress-seo-premium';
        if ( ! file_exists( $dirYoastPremium . '/wp-seo-premium.php' ) ) {
           return;
        }

        if ( ! function_exists( 'is_plugin_active' ) ) {
            include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        }

        $yoastPluginData        = get_plugin_data( $dirYoastPremium . '/wp-seo-premium.php' );
        $dirYoastPremiumInside = $dirYoastPremium . '/premium/';

        // Override yoast redirect
        if ( ! is_admin()
            && version_compare( $yoastPluginData['Version'], '7.1.0', '>=' )
            && is_plugin_active( 'wordpress-seo-premium/wp-seo-premium.php' )
            && file_exists( $dirYoastPremiumInside )
            && file_exists( $dirYoastPremiumInside . 'classes/redirect/redirect-handler.php' )
            && file_exists( $dirYoastPremiumInside . 'classes/redirect/redirect-util.php' ) ) {

            require_once $dirYoastPremiumInside . 'classes/redirect/redirect-handler.php';
            require_once $dirYoastPremiumInside . 'classes/redirect/redirect-util.php';
            require_once yaglot_path([ 'includes', 'integrations', 'yoast', 'YaglotRedirectHandler.php' ]);

            $this->yoastIntegrated = true;

            $redirectHandler = new YaglotRedirectHandler(
                $this->languagesService,
                $this->requestUrlService
            );

            $redirectHandler->load();
        }
    }


    /**
     * @return bool
     */
    public function isWoocommerceIntegrated() {
        return $this->woocommerceIntegrated;
    }


    /**
     * @return void
     */
    private function loadWoocommerceIntegration() {

        if ( ! function_exists( 'is_plugin_active' ) ) {
            include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        }

        if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
            return;
        }

        require_once yaglot_path([ 'includes', 'integrations', 'woocommerce', 'YaglotWcFilterUrls.php' ]);
        require_once yaglot_path([ 'includes', 'integrations', 'woocommerce', 'YaglotWcTranslate.php' ]);


        $this->woocommerceIntegrated = true;

        new YaglotWcFilterUrls(
            $this->optionsService,
            $this->requestUrlService,
            $this->originalLanguage,
            $this->currentLanguage
        );
    }


    /**
     * @return YaglotWcTranslate
     */
    public function getWoocommerceTranslationService() {
        return new YaglotWcTranslate(
            $this->optionsService,
            $this->requestUrlService,
            $this->originalLanguage,
            $this->currentLanguage
        );
    }
}