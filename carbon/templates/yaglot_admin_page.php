<?php

if (!defined('ABSPATH')) {
    exit;
}

?>

<script type="application/javascript">
    (function () {
        window.YaglotData = {
            'accountBillingUrl': <?php echo json_encode(((!empty($this->projectKeyInfo->account->id) ? sprintf(YAGLOT_BILLING_URL, $this->projectKeyInfo->account->id) : YAGLOT_DASHBOARD_URL)));?>,
            'languagesList': <?php echo json_encode($this->getLanguagesList()); ?>,
            'projectKeyInfo': <?php echo json_encode($this->getProjectInfo()); ?>
        }
    })();
</script>

<div class="wrap carbon-theme-options">
  <h2><?php echo esc_html($this->title); ?></h2>

    <?php if ($this->notifications) : ?>
        <?php foreach ($this->notifications as $notification) : ?>
        <div class="settings-error updated notice is-dismissible">
          <p><strong><?php echo $notification ?></strong></p>
        </div>
        <?php endforeach ?>
    <?php endif; ?>

  <form method="post" id="theme-options-form" enctype="multipart/form-data" action="">
    <div id="poststuff">
      <div id="post-body" class="metabox-holder columns-2">

        <div id="post-body-content">

          <div class="postbox carbon-box" id="<?php echo $this->get_id(); ?>">
            <fieldset
              class="inside container-holder carbon-grid theme-options-container container-<?php echo $this->get_id(); ?> <?php echo $this->is_tabbed() ? '' : 'carbon-fields-collection' ?>"></fieldset>
          </div>

          <div>

            <div id="publishing-action">
              <span class="spinner"></span>
              <input type="submit" value="<?php echo esc_attr(__('Save Changes', YAGLOT_SLUG)); ?>" name="publish" id="publish"
                     class="button button-primary button-large">
            </div>

            <div class="clear"></div>

          </div>
        </div>

        <div id="postbox-container-1" class="postbox-container">

          <div class="postbox">

            <h3 class="carbon-tabs-nav"><?php _e('Where are my translations?', YAGLOT_SLUG); ?></h3>

            <div class="inside">
                <?php echo sprintf(__('You can find all your translations in %s account', YAGLOT_SLUG), YAGLOT_NAME); ?>
            </div>

            <div class="inside">
              <a
                href="<?php echo esc_attr((!empty($this->projectKeyInfo->project->id)) ? sprintf(YAGLOT_TRANSLATIONS_URL, $this->projectKeyInfo->account->id, $this->projectKeyInfo->project->id) : YAGLOT_DASHBOARD_URL); ?>"
                class="button button-primary button-large"
                target="_blank" rel="noopener noreferrer">
                  <?php _e('Edit my translations', YAGLOT_SLUG); ?>
              </a>
            </div>
          </div>

            <?php if ($this->projectKeyInfo) : ?>
              <div class="postbox">

                <h3 class="carbon-tabs-nav">
                    <?php _e('Project', YAGLOT_SLUG); ?>:
                    <?php echo esc_html($this->projectKeyInfo->project->title); ?></h3>
                <div class="inside">
                    <?php echo esc_html($this->projectKeyInfo->project->description); ?>
                </div>
              </div>

              <div class="postbox">

                <h3 class="carbon-tabs-nav">
                    <?php _e('Plan', YAGLOT_SLUG); ?>: <?php echo esc_html($this->projectKeyInfo->plan->title); ?></h3>

                <div class="inside">
                  <p>
                    <strong><?php _e('Languages', YAGLOT_SLUG); ?>:</strong>
                      <?php echo ($this->projectKeyInfo->plan->limit_languages > -1) ? $this->projectKeyInfo->plan->limit_languages : __("Unlimited", YAGLOT_SLUG); ?>
                  </p>
                  <p>
                    <strong><?php _e('Page views', YAGLOT_SLUG); ?>:</strong>
                      <?php echo number_format($this->projectKeyInfo->plan->limit_page_views); ?>
                  </p>
                  <p>
                    <strong><?php _e('Characters', YAGLOT_SLUG); ?>:</strong>
                      <?php echo number_format($this->projectKeyInfo->plan->limit_translation_characters); ?>
                  </p>
                </div>

              </div>

              <div class="postbox">

                <h3 class="carbon-tabs-nav"><?php _e('Statistic', YAGLOT_SLUG); ?></h3>

                <div class="inside">

                  <p>
                    <strong><?php _e('Languages used', YAGLOT_SLUG); ?>:</strong>
                      <?php if ($this->projectKeyInfo->plan->limit_languages > -1): ?>
                          <?php echo "{$this->projectKeyInfo->usage->total_languages_count}/{$this->projectKeyInfo->plan->limit_languages}"; ?>
                      <?php else: ?>
                          <?php echo $this->projectKeyInfo->usage->total_languages_count; ?>
                      <?php endif; ?>
                  </p>

                  <p>
                    <strong><?php _e('Pages viewed', YAGLOT_SLUG); ?>:</strong>
                      <?php echo number_format($this->projectKeyInfo->usage->billing_period_page_views_count) . "/" . number_format($this->projectKeyInfo->plan->limit_page_views); ?>
                  </p>
                  <p>
                    <strong><?php _e('Characters translated', YAGLOT_SLUG); ?>:</strong>
                      <?php echo number_format($this->projectKeyInfo->usage->billing_period_translated_chars_count) . "/" . number_format($this->projectKeyInfo->plan->limit_translation_characters); ?>
                  </p>
                </div>

              </div>
            <?php endif; ?>
        </div>
      </div>
    </div>
  </form>


  <div class="clear">
    <a target="_blank"
       href="https://wordpress.org/support/view/plugin-reviews/<?php echo YAGLOT_SLUG; ?>?rate=5#postform">
        <?php echo sprintf(__("Love %s? Give us 5 stars on WordPress.org :)", YAGLOT_SLUG), YAGLOT_NAME); ?>
    </a>
  </div>

  <div class="yaglot-footer">
    <p><?php echo sprintf(esc_html__('If you need any help, you can contact us via email us at %s.', YAGLOT_SLUG), '<a href="mailto:' . esc_attr(YAGLOT_EMAIL) . '">' . YAGLOT_EMAIL . '</a>'); ?></p>
    <p><?php echo sprintf(esc_html__('You can also check our %sDocumentation%s.', YAGLOT_SLUG), '<a href="' . esc_attr(YAGLOT_DOCUMENTATION_URL . 'categories/wordpress') . '" target="_blank" rel="noopener noreferrer">', '</a>'); ?></p>
  </div>


</div>