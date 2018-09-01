<?php
NameSpace Jay\Custom\Database;
class Helper_Field{
	public $name;
	public $format;
	public $required;
	public $default;

	public function __construct( $name, $format, $required = false, $default = null ){
		$this->name     = $name;
		$this->format   = $format;
		$this->required = $required;
		$this->default  = $default;
	}
}
