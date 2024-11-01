<?php

namespace Yaglot\Services;

use Yaglot\Client\Api\LanguageEntry;

if (!defined('ABSPATH')) {
    exit;
}


class YaglotMenuService {

    /**
     * @var \Yaglot\Services\YaglotLanguagesService
     */
    private $languagesService;


    /**
     * @var \Yaglot\Services\YaglotOptionsService
     */
    private $optionsService;


    /**
     * @var \Yaglot\Services\YaglotRequestUrlService
     */
    private $requestUrlService;


    /**
     * @var \Yaglot\Services\YaglotSwitchersService
     */
    private $switchersService;


    /**
     * YaglotMenu constructor.
     */
    public function __construct(YaglotOptionsService $optionsService,
                                YaglotLanguagesService $languagesService,
                                YaglotRequestUrlService $requestUrlService,
                                YaglotSwitchersService $switchersService) {

        $this->languagesService = $languagesService;
        $this->optionsService = $optionsService;
        $this->requestUrlService = $requestUrlService;
        $this->switchersService = $switchersService;

        $this->hooks();
    }


    /**
     * @return void
     */
    public function hooks() {

        add_action('admin_head-nav-menus.php', [$this, 'addNavMenuMetaBoxes']);
        add_filter('nav_menu_link_attributes', [$this, 'addNavMenuLinkAttributes'], 10, 2);
        add_filter('nav_menu_css_class', [$this, 'addNavMenuCssClass'], 10, 2);
        
    }


    /**
     * @param array $classes
     * @param object $item
     * @return array
     */
    public function addNavMenuCssClass($classes, $item) {

        $str = 'yaglot_menu_title-';

        if (strpos($item->post_name, $str) !== false) {

            if (!$this->requestUrlService->isTranslatableUrl()
                || !$this->requestUrlService->isEligibleUrl($this->requestUrlService->getFullUrl())) {

                $attrs['style'] = 'display:none';

                return $attrs;
            }

            $lang = explode('-', substr($item->post_name, strlen($str)));

            $classes[] = apply_filters('yaglot_nav_menu_link_class', $lang[0]);
        }

        return $classes;
    }


    /**
     * @param array $attrs
     * @param object $item
     * @return array
     */
    public function addNavMenuLinkAttributes($attrs, $item) {

        $str = 'yaglot_menu_title-';

        if (strpos($item->post_name, $str) !== false) {

            if (!$this->requestUrlService->isTranslatableUrl()
                || !$this->requestUrlService->isEligibleUrl($this->requestUrlService->getFullUrl())) {

                $attrs['style'] = 'display:none';

                return $attrs;
            }

            if (!isset($attrs['class'])) {
                $attrs['class'] = '';
            }

            $attrs['class'] .= ' yg-lang';
        }

        return $attrs;
    }


    /**
     * @return void
     */
    public function addNavMenuMetaBoxes() {
        add_meta_box('yaglot_nav_link', __(sprintf('%s Language', YAGLOT_NAME), YAGLOT_SLUG), [$this, 'navMenuLinks'], 'nav-menus', 'side', 'low');
    }


    /**
     * Output menu links.
     */
    public function navMenuLinks() {

        /**
         * @var LanguageEntry[] $languagesConfigured
         */
        $languagesConfigured = $this->languagesService->getLanguagesConfigured();
        $languagesAvailable = array_merge([$this->optionsService->getOriginalLanguage()], $this->optionsService->getTargetLanguages()); ?>

      <div id="posttype-yaglot-languages" class="posttypediv">
        <div id="tabs-panel-yaglot-languages-endpoints" class="tabs-panel tabs-panel-active">
          <ul id="yaglot-languages-endpoints-checklist" class="categorychecklist form-no-clear">

              <?php

              $i = 1;

              foreach ($languagesConfigured as $key => $language) : ?>

                  <li>
                    <label class="menu-item-title">
                      <input <?php echo ! in_array($language->getIso639(), $languagesAvailable) ? 'disabled="disabled"' : ""; ?>
                            type="checkbox" class="menu-item-checkbox"
                            name="menu-item[<?php echo esc_attr($i); ?>][menu-item-object-id]"
                            value="<?php echo esc_attr($i); ?>"/> <?php echo esc_html($language->getEnglishName()); ?>
                    </label>
                    <input type="hidden" class="menu-item-type"
                           name="menu-item[<?php echo esc_attr($i); ?>][menu-item-type]" value="custom"/>
                    <input type="hidden" class="menu-item-title"
                           name="menu-item[<?php echo esc_attr($i); ?>][menu-item-title]"
                           value="[yaglot_menu_title-<?php echo esc_attr($language->getIso639()); ?>]"/>
                    <input type="hidden" class="menu-item-url"
                           name="menu-item[<?php echo esc_attr($i); ?>][menu-item-url]"
                           value="[yaglot_menu_current_url-<?php echo esc_attr($language->getIso639()); ?>]"/>
                    <input type="hidden" class="menu-item-classes"
                           name="menu-item[<?php echo esc_attr($i); ?>][menu-item-classes]"/>
                  </li>

              <?php $i++;
              endforeach; ?>
          </ul>
        </div>
        <p class="button-controls">
				<span class="list-controls">
					<a
            href="<?php echo esc_url(admin_url('nav-menus.php?page-tab=all&selectall=1#posttype-yaglot-languages')); ?>"
            class="select-all"><?php esc_html_e('Select all', YAGLOT_SLUG); ?></a>
				</span>
          <span class="add-to-menu">
					<button type="submit" class="button-secondary submit-add-to-menu right"
                  value="<?php esc_attr_e('Add to menu', YAGLOT_SLUG); ?>" name="add-post-type-menu-item"
                  id="submit-posttype-yaglot-languages"><?php esc_html_e('Add to menu', YAGLOT_SLUG); ?></button>
					<span class="spinner"></span>
				</span>
        </p>
      </div>
        <?php
    }
}