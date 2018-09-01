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

	protected function upgrade_database_version( $target_version ) {
		global $wpdb;

		switch ( $target_version ) {
			case 1:
				$result = $wpdb->prepare( 'CREATE TABLE %s (
                  id INT NOT NULL AUTO_INCREMENT ,
                  name VARCHAR(255) NOT NULL ,
                  price MEDIUMINT NOT NULL ,
                  PRIMARY KEY (id)
                )
                ENGINE = InnoDB
                ;', [ $this->table_name, ] );
				break;
			case 2:
				$result = $wpdb->prepare( 'ALTER TABLE %s
				ADD
                description TEXT NULL
                AFTER price
				;', [ $this->table_name, ] );
				break;
			case 3:
				$result = $wpdb->prepare( 'ALTER TABLE %s
				ADD
                datetime DATETIME NULL,
                datetime_utc DATETIME NULL
                AFTER price;
				;', [ $this->table_name ] );
				break;
			default:
				throw new Exception( 'Trying to get version SQL update for version ' . $target_version . ' for datbase ' . $this->table_name );
		}
		return $result->get_results();
	}
}
