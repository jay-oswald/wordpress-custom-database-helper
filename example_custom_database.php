<?php

class example_custom_database extends jay_custom_database_helper{

    public function __construct(){
        $this->latest_version = 2;

        parent::__construct('test_database');
    }

    protected function get_upgrade_sql($target_version){

        $alter_table = "ALTER TABLE {$this->table_name} ";

        switch($target_version){
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
                description TEXT NOT NULL
                AFTER price;";
            default:
                throw new Exception('Trying to get version SQL update for version ' . $target_version . ' for datbase ' . $this->table_name);
        }
    }
}