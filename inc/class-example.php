<?php

namespace Jay\Custom\Database;

class Example extends Helper {

	public function __construct() {
		$this->latest_version = 3;

		$this->fields[] = new Helper_Field( 'id', '%d' );
		$this->fields[] = new Helper_Field( 'name', '%s', true );
		$this->fields[] = new Helper_Field( 'price', '%d', true );
		$this->fields[] = new Helper_Field( 'description', '%s' );
		$this->fields[] = new Helper_Field( 'datetime', '%s', false, current_time( 'mysql', 0 ) );
		$this->fields[] = new Helper_Field( 'datetime_utc', '%s', false, current_time( 'mysql', 1 ) );

		parent::__construct( 'test_database' );
	}

	protected function get_upgrade_sql( $target_version ) {

		$alter_table = "ALTER TABLE {$this->table_name} ";

		switch ( $target_version ) {
			case 1:
				return "CREATE TABLE {$this->table_name} (
                  id INT NOT NULL AUTO_INCREMENT ,
                  name VARCHAR(255) NOT NULL ,
                  price MEDIUMINT NOT NULL ,
                  PRIMARY KEY (id)
                )
                ENGINE = InnoDB;";
			case 2:
				return "{$alter_table}
                ADD
                description TEXT NULL
                AFTER price;";
			case 3:
				return "{$alter_table}
                ADD
                datetime DATETIME NULL,
                datetime_utc DATETIME NULL
                AFTER price;";
			default:
				throw new Exception( 'Trying to get version SQL update for version ' . $target_version . ' for datbase ' . $this->table_name );
		}
	}
}
