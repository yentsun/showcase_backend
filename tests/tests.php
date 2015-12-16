<?php

require_once '../utils.php';


class UnitTest extends PHPUnit_Framework_TestCase {


    public function test_getabsent_field_absent() {
        $data = array(
            'name' => 'Jack',
            'address' => '6th Park Lane, 4',
            'message' => 'hello'
        );
        $required = array('name', 'phone');
        $absent = get_absent($required, $data);
        $this->assertEquals(array('phone'), $absent);
    }

    public function test_getabsent_field_empty() {
        $data = array(
            'name' => 'Jack',
            'phone' => '',
            'address' => '6th Park Lane, 4',
            'message' => 'hello'
        );
        $required = array('name', 'phone');
        $absent = get_absent($required, $data);
        $this->assertEquals(array('phone'), $absent);
    }

    public function test_getabsent_all_ok() {
        $data = array(
            'name' => 'Jack',
            'phone' => '545-546-4545',
            'address' => '6th Park Lane, 4',
            'message' => 'hello'
        );
        $required = array('name', 'phone');
        $absent = get_absent($required, $data);
        $this->assertEquals(array(), $absent);
    }

    public function test_config() {
        $config = get_config('../config.ini', 'production') +
                  get_config('../config.ini', 'general');
        $this->assertArrayHasKey('email_from', $config);
        $this->assertArrayHasKey('lots_per_page', $config);
        $this->assertArrayHasKey('debug', $config);
        $this->assertArrayHasKey('path_prefix', $config);
        $this->assertArrayHasKey('order_addresses', $config);
        $this->assertArrayHasKey('feedback_addresses', $config);
        $this->assertArrayHasKey('db.url', $config);
        $this->assertArrayHasKey('db.user', $config);
        $this->assertArrayHasKey('db.password', $config);
    }
}