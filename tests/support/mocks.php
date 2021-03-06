<?php
/**
 * Schema
 *
 * Expressive table definitions
 *
 * @author 		Jamie Rumbelow <http://jamierumbelow.net>
 * @version		0.2.0
 * @copyright 	(c)2011 Jamie Rumbelow
 */
 

class Mocked {
    protected $_calls = array();
    protected $_expectations = array();
    
    public function expect_call($method, $count = 1, $params = array()) {
        $this->_expectations[$method] = array($count, $params);
    }
    
    public function assert(&$test) {
        foreach ($this->_expectations as $method => $details) {
            if (isset($this->_calls[$method])) {
                $test->assert_equal($details[1], $this->_calls[$method][0], "$method expected to be called with " . str_replace("\n", "", var_export($details[1], TRUE)) . ", instead called with " . str_replace("\n", "", var_export($this->_calls[$method][0], TRUE)));
            } else {
                $test->failure("$method expected to be called on " . get_class($this) . ", no calls found");
            }
        }
    }
    
    protected function _track_call($method, $params) {
        if (!isset($this->_calls[$method])) {
            $this->_calls[$method] = array();
        }
        
        $this->_calls[$method][] = $params;
    }
}

class Mock_CI {
    public $load;
    public $dbforge;
}

class Mock_Loader extends Mocked {
    public function dbforge() { $this->_track_call('dbforge', array()); }
}

class Mock_DBForge extends Mocked {
    public function add_field($columns) { $this->_track_call('add_field', $columns); }
    public function add_key($key, $primary = FALSE) { $this->_track_call('add_key', array($key, $primary)); }
    public function create_table($name) { $this->_track_call('create_table', $name); }
    public function add_column($table, $column) { $this->_track_call('add_column', array($table, $column)); }
    public function drop_column($table, $column) { $this->_track_call('drop_column', array($table, $column)); }
    public function modify_column($table, $column) { $this->_track_call('modify_column', array($table, $column)); }
}

class Mock_Schema_Table_Definition extends Schema_Table_Definition {
    public function columns() { return Schema_Test_Data::mock_column_data(); }
    public function keys() { return array(); }
    public function table_name() { return 'table_name'; }
}

function &get_instance() {
    static $instance;
    
    if (!$instance) {
        $instance = new Mock_CI();
    }
    
    return $instance;
}