<?php

namespace Yaglot\Entities;

use Yaglot\Exceptions\ServerErrorException;

if (!defined('ABSPATH')) {
    exit;
}


class YaglotProjectEntity {

    /**
     * @var int
     */
    public $id;


    /**
     * @var string
     */
    public $title;


    /**
     * @var string
     */
    public $description;


    /**
     * YaglotProjectEntity constructor.
     * @param array $project
     * @throws ServerErrorException
     */
    public function __construct(array $project) {

        $this->setId($project)
            ->setTitle($project)
            ->setDescription($project);
    }


    /**
     * @param array $project
     * @return $this
     * @throws ServerErrorException
     */
    public function setId(array $project) {

        if (!(isset($project['id']) && is_numeric($project['id']))) {
            throw new ServerErrorException('Missing or invalid project id.', 422);
        }

        $this->id = (int)$project['id'];

        return $this;
    }


    /**
     * @param array $project
     * @return $this
     * @throws ServerErrorException
     */
    public function setTitle(array $project) {

        if (!(isset($project['title']) && is_string($project['title']))) {
            throw new ServerErrorException('Missing or invalid project title.', 422);
        }

        $this->title = $project['title'];

        return $this;
    }


    /**
     * @param array $project
     * @return $this
     * @throws ServerErrorException
     */
    public function setDescription(array $project) {

        if (!isset($project['description'])) {
            throw new ServerErrorException('Missing or invalid project description.', 422);
        }

        $this->description = (!empty($project['description']) && is_string($project['description'])) ? $project['description'] : '';

        return $this;
    }
}