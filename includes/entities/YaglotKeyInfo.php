<?php

namespace Yaglot\Entities;

use Yaglot\Exceptions\ServerErrorException;

if (!defined('ABSPATH')) {
    exit;
}


class YaglotKeyInfo {

    /**
     * @var YaglotPlanEntity
     */
    public $plan;

    /**
     * @var YaglotAccountEntity
     */
    public $account;

    /**
     * @var YaglotProjectEntity
     */
    public $project;


    /**
     * @var YaglotProjectUsageEntity
     */
    public $usage;


    /**
     * @var YaglotProjectLanguageEntity[]
     */
    public $languages = [];

    /**
     * YaglotKeyInfo constructor.
     * @param array $data
     * @throws ServerErrorException
     */
    public function __construct(array $data) {

        $this->setAccount($data)
            ->setPlan($data)
            ->setProject($data)
            ->setUsage($data)
            ->setLanguages($data);
    }

    /**
     * @param array $data
     * @return $this
     * @throws ServerErrorException
     */
    public function setPlan(array $data) {

        if (!(isset($data['plan']) && is_array($data['plan']))) {
            throw new ServerErrorException('Missing or invalid project plan data.', 422);
        }

        $this->plan = new YaglotPlanEntity($data['plan']);

        return $this;
    }


    /**
     * @param array $data
     * @return $this
     * @throws ServerErrorException
     */
    public function setAccount(array $data) {

        if (!(isset($data['account']) && is_array($data['account']))) {
            throw new ServerErrorException('Missing or invalid project account data.', 422);
        }

        $this->account = new YaglotAccountEntity($data['account']);

        return $this;
    }


    /**
     * @param array $data
     * @return $this
     * @throws ServerErrorException
     */
    public function setProject(array $data) {

        if (!(isset($data['project']) && is_array($data['project']))) {
            throw new ServerErrorException('Missing or invalid project data.', 422);
        }

        $this->project = new YaglotProjectEntity($data['project']);

        return $this;
    }


    /**
     * @param array $data
     * @return $this
     * @throws ServerErrorException
     */
    public function setUsage(array $data) {

        if (!(isset($data['usage']) && is_array($data['usage']))) {
            throw new ServerErrorException('Missing or invalid project usage data.', 422);
        }

        $this->usage = new YaglotProjectUsageEntity($data['usage']);

        return $this;
    }


    /**
     * @param array $data
     * @return $this
     * @throws ServerErrorException
     */
    public function setLanguages(array $data) {

        if (!(isset($data['languages']) && is_array($data['languages']))) {
            throw new ServerErrorException('Missing or invalid project languages data.', 422);
        }

        $this->languages = array_map(function($language){

            if(!is_array($language)) {
                throw new ServerErrorException('Missing or invalid project languages data.', 422);
            }

            return new YaglotProjectLanguageEntity($language);

        }, $data['languages']);

        return $this;
    }
}