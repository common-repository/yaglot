<?php

namespace Yaglot\Entities;

use Yaglot\Exceptions\ServerErrorException;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


class YaglotPlanEntity {

    /**
     * @var int
     */
    public $id;


    /**
     * @var string
     */
    public $title;


    /**
     * @var int
     */
    public $limit_translation_characters;


    /**
     * @var int
     */
    public $limit_page_views;


    /**
     * @var int
     */
    public $limit_languages;


    /**
     * YaglotPlanEntity constructor.
     * @param array $plan
     * @throws ServerErrorException
     */
    public function __construct(array $plan) {

        $this->setId($plan)
            ->setTitle($plan)
            ->setLimitTranslationCharacters($plan)
            ->setLimitPageViews($plan)
            ->setLimitLanguages($plan);
    }


    /**
     * @param array $plan
     * @return $this
     * @throws ServerErrorException
     */
    public function setId(array $plan) {

        if (!(isset($plan['id']) && is_numeric($plan['id']))) {
            throw new ServerErrorException('Missing or invalid plan id.', 422);
        }

        $this->id = (int) $plan['id'];

        return $this;
    }


    /**
     * @param array $plan
     * @return $this
     * @throws ServerErrorException
     */
    public function setTitle(array $plan) {

        if (!(isset($plan['title']) && is_string($plan['title']))) {
            throw new ServerErrorException('Missing or invalid plan title.', 422);
        }

        $this->title = $plan['title'];

        return $this;
    }


    /**
     * @param array $plan
     * @return $this
     * @throws ServerErrorException
     */
    public function setLimitTranslationCharacters(array $plan) {

        if (!(isset($plan['limit_translation_characters']) && is_numeric($plan['limit_translation_characters']))) {
            throw new ServerErrorException('Missing or invalid plan limit_translation_characters.', 422);
        }

        $this->limit_translation_characters = (int) $plan['limit_translation_characters'];

        return $this;
    }


    /**
     * @param array $plan
     * @return $this
     * @throws ServerErrorException
     */
    public function setLimitPageViews(array $plan) {

        if (!(isset($plan['limit_page_views']) && is_numeric($plan['limit_page_views']))) {
            throw new ServerErrorException('Missing or invalid plan limit_page_views.', 422);
        }

        $this->limit_page_views = (int) $plan['limit_page_views'];

        return $this;
    }


    /**
     * @param array $plan
     * @return $this
     * @throws ServerErrorException
     */
    public function setLimitLanguages(array $plan) {

        if (!(isset($plan['limit_languages']) && is_numeric($plan['limit_languages']))) {
            throw new ServerErrorException('Missing or invalid plan limit_languages.', 422);
        }

        $this->limit_languages = (int) $plan['limit_languages'];

        return $this;
    }


}