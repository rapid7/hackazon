<?php

namespace App\Model;

class Model {

    /**
     * Pixie Dependancy Container
     * @var \PHPixie\Pixie
     */
    public $pixie;

    /**
     * Initializes the database module
     * 
     * @param \PHPixie\Pixie $pixie Pixie dependency container
     */
    public function __construct($pixie) {
        $this->pixie = $pixie;
    }

}