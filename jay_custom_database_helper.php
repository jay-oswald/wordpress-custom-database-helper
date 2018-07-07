<?php

abstract class jay_custom_database_helper{
    protected $table_name;
    protected $table_name_without_prefix;
    protected $version_option_name;

    protected $latest_version;

    /**
     * @var jay_custom_database_helper_field[]
     */
    protected $fields;

    public function __construct($table_name){
        $this->table_name_without_prefix = $table_name;
        global $wpdb;
        $this->table_name = $wpdb->prefix . $table_name;
        $this->version_option_name = $this->table_name . '_database_version';

        try{
            $this->maybe_update_database();
        } catch (Exception $e){
            $this->handle_errors($e);
        }

        //TODO see if there is a more efficient way of doing this
        foreach($this->fields as $field_name => $data){
            $this->fields[$data->name] = $data;
            unset($this->fields[$field_name]);

            $filter_name = "{$this->table_name}_{$data->name}_default";
            $this->fields[$data->name]->default = apply_filters($filter_name, $this->fields[$data->name]->default);
        }
    }

    protected function maybe_update_database(){
        $current_version = get_option($this->version_option_name,false);

        if($current_version == $this->latest_version){
            return true;
        }

        if($current_version == false){
            $this->initalise_database();

            $current_version = 1;
            add_option($this->version_option_name, $current_version, '', true);
        }

        if(!$this->does_table_exist()){
            throw new Exception('Trying to update database ' . $this->table_name . ' but it does not exist');
        }

        return $this->upgrade_database_loop($current_version);
    }

    protected function upgrade_database_loop($current_version){
        while($current_version < $this->latest_version){
            $current_version++;
            $this->upgrade_database($current_version);

            update_option($this->version_option_name, $current_version, 'true');
        }
        return true;
    }

    protected function upgrade_database($target_version){
        global $wpdb;

        $sql = $this->get_upgrade_sql($target_version);
        if(!$sql){
            throw new Exception('Could not get SQL version ' . $target_version . ' for database ' . $this->table_name);
        }

        $result = $wpdb->query($sql);

        if(!$result)
            throw new Exception('Could not upgrade database ' . $this->table_name . ' to version ' . $target_version);
    }

    protected function initalise_database(){
        global $wpdb;

        if($this->does_table_exist())
            throw new Exception('Database ' . $this->table_name . ' already exists');

        $sql = $this->get_upgrade_sql(1);
        $result = $wpdb->query($sql);

        if(!$result)
            throw new Exception('Could not create database ' . $this->table_name);
    }

    protected function does_table_exist(){
        global $wpdb;

        $result = $wpdb->get_var("SHOW TABLES LIKE '{$this->table_name}';",1,1);

        if($result)
            return true;
        else
            return false;
    }

    abstract protected function get_upgrade_sql($target_version);

    protected function handle_errors(Exception $e){
        if( current_user_can('administrator')){
            echo $e->getMessage();
        }
    }

    public function insert($data){
        foreach($this->fields as $field){
            if(!$field->required)
                continue;

            if(!isset($data[$field->name]))
                throw new Exception('Need to pass field ' . $field->name . ' to insert into ' . $this->table_name);
            if(is_null($data[$field->name]))
                throw new Exception('NULL does not count as a value for field ' . $field->name . ' to insert into ' . $this->table_name);
        }

        $formats = [];
        foreach($data as $column=>$value){
            if(!isset($this->fields[$column]))
                throw new Exception("You are trying to insert column {$column} into table {$this->table_name} where it does not exist");

            if(is_null($value))
                $data[$column] = $this->fields[$column]->default;

            $formats[] = $this->fields[$column]->format;
        }

        global $wpdb;
        $result = $wpdb->insert($this->table_name, $data, $formats);

        if(!$result)
            throw new Exception("Error inserting row into table {$this->table_name} mysql error message: {$wpdb->last_error}");

        return $result;
    }

    public function update($data, $where){
        $format = [];
        foreach($data as $column=>$value){
            if(!isset($this->fields[$column]))
                throw new Exception("You are trying to update column {$column} in table {$this->table_name} where it does not exist");
            $format[] = $this->fields[$column]->format;
        }

        if(sizeof($where) == 0)
            throw new Exception("You are trying to update {$this->table_name} without any where condition");
        $where_format = [];
        foreach($where as $column=>$value){
            if(!isset($this->fields[$column]))
                throw new Exception("You are trying to use column {$column} in where condition to update table {$this->table_name} where it does not exist");
            $where_format[] = $this->fields[$column]->format;
        }

        global $wpdb;
        $wpdb->update($this->table_name, $data, $where, $format, $where_format);
    }
}