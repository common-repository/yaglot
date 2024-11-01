<?php

namespace Yaglot\Widgets;

use Yaglot\Services\YaglotOptionsService;
use Yaglot\Services\YaglotRequestUrlService;
use Yaglot\Services\YaglotSwitchersService;

if (!defined('ABSPATH')) {
    exit;
}


class YaglotSwitcherWidget extends \WP_Widget {

    /**
     * @var YaglotSwitchersService
     */
    private $switcherService;


    /**
     * @var YaglotRequestUrlService
     */
    private $requestUrlService;


    /**
     * @var YaglotOptionsService
     */
    private $optionsService;


    /**
     * @var array
     */
    private $switchers = [];


    /**
     * Register widget with WordPress.
     * @param YaglotSwitchersService $switchersService
     * @param YaglotRequestUrlService $requestUrlService
     */
    public function __construct(YaglotOptionsService $optionsService,
                                YaglotSwitchersService $switchersService,
                                YaglotRequestUrlService $requestUrlService) {

        $this->optionsService = $optionsService;
        $this->switcherService = $switchersService;
        $this->requestUrlService = $requestUrlService;

        foreach ($this->optionsService->getSwitchers() as $switcher) {
            $this->switchers[$switcher['switcher_id']] = $switcher;
        }

        parent::__construct(YAGLOT_SLUG, __(sprintf('%s Translate', YAGLOT_NAME), YAGLOT_SLUG), [
            'description' => __(sprintf('Display %s switcher in widget', YAGLOT_NAME), YAGLOT_SLUG),
        ]);
    }


    /**
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array $args Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget($args, $instance) {

        if (!$this->requestUrlService->isEligibleUrl($this->requestUrlService->getFullUrl())) {
            return;
        }

        $title = $instance['title'];
        if (!isset($this->switchers[$instance['switcher_id']])) {
            return;
        }

        $tt = (!empty($title)) ? $args['before_title'] . $title . $args['after_title'] : '';

        $options = $this->switchers[$instance['switcher_id']];
        $options['fixed_position'] = false;

        $switcherHtml = $this->switcherService->getHtml($options);

        echo $args['before_widget'] . $tt . $switcherHtml . $args['after_widget'];
    }


    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     */
    public function form($instance) {

        if (isset($instance['title'])) {
            $title = $instance['title'];
        } else {
            $title = '';
        }

        if (isset($instance['switcher_id'])) {
            $id = $instance['switcher_id'];
        } else {
            $id = '';
        } ?>

      <p>
        <label for="<?php echo esc_html($this->get_field_id('title')); ?>">
            <?php esc_html_e('Title:', YAGLOT_SLUG); ?>
        </label>
        <input class="widefat" id="<?php echo esc_html($this->get_field_id('title')); ?>"
               name="<?php echo esc_html($this->get_field_name('title')); ?>" type="text"
               value="<?php echo esc_attr($title); ?>"/>
      </p>

        <?php if (empty($this->switchers)) : ?>
        <p>
            <?php esc_html_e('There are no switchers available.', YAGLOT_SLUG); ?>
        </p>
        <?php else: ?>

        <p>
          <label for="<?php echo esc_html($this->get_field_id('switcher_id')); ?>">
              <?php esc_html_e('Switcher', YAGLOT_SLUG); ?>:
          </label>
          <select
            name="<?php echo esc_html($this->get_field_name('switcher_id')); ?>"
            class="widefat"
            id="<?php echo esc_html($this->get_field_id('switcher_id')); ?>">

              <option value=""><?php esc_html_e('-- Select --', YAGLOT_SLUG); ?></option>
              <?php foreach ($this->switchers as $switcher): ?>
                  <?php $selected = ($id == $switcher['switcher_id']) ? 'selected="selected"' : ''; ?>
                <option value="<?php echo esc_attr($switcher['switcher_id']); ?>" <?php echo $selected; ?> >
                    #<?php echo $switcher['switcher_id']; ?> - <?php echo $switcher['type'];?>
                </option>
              <?php endforeach; ?>
          </select>
        </p>

        <?php endif;
    }


    /**
     * Sanitize widget form values as they are saved.
     *
     * @see WP_Widget::update()
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    public function update($new_instance, $old_instance) {

        $instance = [];
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['switcher_id'] = (!empty($new_instance['switcher_id'])) ? $new_instance['switcher_id'] : '';

        return $instance;
    }
}
