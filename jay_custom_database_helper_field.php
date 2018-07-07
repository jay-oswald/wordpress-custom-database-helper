<?php

class jay_custom_database_helper_field{
    public $name;
    public $format;
    public $required;
    public $default;

    public function __construct($name, $format, $required = false, $default = null){
        $this->name = $name;
        $this->format = $format;
        $this->required = $required;
        $this->default = $default;
    }
}