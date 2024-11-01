<?php

namespace Yaglot\Entities;

use Yaglot\Exceptions\ServerErrorException;

if (!defined('ABSPATH')) {
    exit;
}


class YaglotProjectUsageEntity {

    /**
     * @var int
     */
    public $billing_period_page_views_count;


    /**
     * @var int
     */
    public $billing_period_translated_chars_count;


    /**
     * @var int
     */
    public $total_page_views_count;


    /**
     * @var int
     */
    public $total_translated_chars_count;


    /**
     * @var int
     */
    public $total_translated_words_count;


    /**
     * @var int
     */
    public $total_languages_count;


    /**
     * YaglotProjectUsageEntity constructor.
     * @param array $usage
     * @throws ServerErrorException
     */
    public function __construct(array $usage) {

        $this->setBillingPeriodPageViewsCount($usage)
            ->setTotalPageViewsCount($usage)
            ->setBillingPeriodTranslatedCharsCount($usage)
            ->setTotalTranslatedCharsCount($usage)
            ->setTotalLanguagesCount($usage)
            ->setTotalTranslatedWordsCount($usage);
    }


    /**
     * @param array $usage
     * @return $this
     * @throws ServerErrorException
     */
    public function setBillingPeriodPageViewsCount(array $usage) {

        if (!(isset($usage['billing_period_page_views_count']) && is_numeric($usage['billing_period_page_views_count']))) {
            throw new ServerErrorException('Missing or invalid usage billing_period_page_views_count.', 422);
        }

        $this->billing_period_page_views_count = (int)$usage['billing_period_page_views_count'];

        return $this;
    }


    /**
     * @param array $usage
     * @return $this
     * @throws ServerErrorException
     */
    public function setBillingPeriodTranslatedCharsCount(array $usage) {

        if (!(isset($usage['billing_period_translated_chars_count']) && is_numeric($usage['billing_period_translated_chars_count']))) {
            throw new ServerErrorException('Missing or invalid usage billing_period_translated_chars_count.', 422);
        }

        $this->billing_period_translated_chars_count = (int)$usage['billing_period_translated_chars_count'];

        return $this;
    }


    /**
     * @param array $usage
     * @return $this
     * @throws ServerErrorException
     */
    public function setTotalPageViewsCount(array $usage) {

        if (!(isset($usage['total_page_views_count']) && is_numeric($usage['total_page_views_count']))) {
            throw new ServerErrorException('Missing or invalid usage total_page_views_count.', 422);
        }

        $this->total_page_views_count = (int)$usage['total_page_views_count'];

        return $this;
    }


    /**
     * @param array $usage
     * @return $this
     * @throws ServerErrorException
     */
    public function setTotalTranslatedCharsCount(array $usage) {

        if (!(isset($usage['total_translated_chars_count']) && is_numeric($usage['total_translated_chars_count']))) {
            throw new ServerErrorException('Missing or invalid usage total_translated_chars_count.', 422);
        }

        $this->total_translated_chars_count = (int)$usage['total_translated_chars_count'];

        return $this;
    }


    /**
     * @param array $usage
     * @return $this
     * @throws ServerErrorException
     */
    public function setTotalLanguagesCount(array $usage) {

        if (!(isset($usage['total_languages_count']) && is_numeric($usage['total_languages_count']))) {
            throw new ServerErrorException('Missing or invalid usage total_languages_count.', 422);
        }

        $this->total_languages_count = (int)$usage['total_languages_count'];

        return $this;
    }


    /**
     * @param array $usage
     * @return $this
     * @throws ServerErrorException
     */
    public function setTotalTranslatedWordsCount(array $usage) {

        if (!(isset($usage['total_translated_words_count']) && is_numeric($usage['total_translated_words_count']))) {
            throw new ServerErrorException('Missing or invalid usage total_translated_words_count.', 422);
        }

        $this->total_translated_words_count = (int)$usage['total_translated_words_count'];

        return $this;
    }
}