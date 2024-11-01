<?php

namespace Yaglot\Carbon;

use Carbon_Fields\Datastore\Theme_Options_Datastore;

class Yaglot_Theme_Options_Datastore extends Theme_Options_Datastore {

    public function __construct() {

        parent::__construct();

        $this->key_toolset = new Yaglot_Key_Toolset();
    }

}