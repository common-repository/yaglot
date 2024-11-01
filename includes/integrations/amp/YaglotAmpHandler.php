<?php

namespace Yaglot\Integrations\Amp;

if (!defined('ABSPATH')) {
    exit;
}

class YaglotAmpHandler {

    public function __construct() {
        $this->hooks();
    }

    /**
     * @return void
     */
    public function hooks() {
        add_action('amp_post_template_head', [$this, 'ampPostTemplate']);
    }

    /**
     * @return void
     */
    public function ampPostTemplate() {
        ?>

      <link rel="stylesheet" href="<?php echo esc_url(YAGLOT_URL . 'assets/css/sdk.css'); ?>"/>
      <script type="text/javascript">

          (function (window, document) {

              var YaglotSwitcher = function () {
              };

              YaglotSwitcher.prototype.onSelectChange = function (target) {

                  var option = target.options.namedItem(target.value);
                  if (!option) {
                      return;
                  }

                  var href = option.dataset.href;
                  if (!href) {
                      return;
                  }

                  window.location = option.dataset.href;
              };

              YaglotSwitcher.prototype.onDropdownOpen = function (target) {

                  var height = window.innerHeight,
                      top = this.getOffset(target.parentNode).top - window.scrollY,
                      position = window.getComputedStyle(target.parentNode).getPropertyValue("position"),
                      bottom = window.getComputedStyle(target.parentNode).getPropertyValue("bottom");

                  if (top > height / 2 || position === "fixed" && bottom !== "auto") {
                      target.classList.add(this.options.switcherClass + "-open-up")
                  } else {
                      target.classList.remove(this.options.switcherClass + "-open-up");
                  }

                  target.classList.toggle("closed");
              };

              YaglotSwitcher.prototype.getOffset = function (element) {

                  var top = 0,
                      left = 0;

                  do {
                      top += element.offsetTop || 0;
                      left += element.offsetLeft || 0;
                      element = element.offsetParent;
                  } while (element);

                  return {
                      top: top,
                      left: left
                  }
              };

              YaglotSwitcher.prototype.options = {
                  'switcherClass': 'yg-sw',
                  'mainClass': 'yg'
              };

              window.YaglotSwitcher = new YaglotSwitcher();

          })(window, document);

      </script>
        <?php
    }
}
