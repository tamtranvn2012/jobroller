<?php

require_once APP_TESTS_LIB . '/testcase.php';

/**
 * @group payments
 */
class APP_Payments_Processes_Test extends APP_UnitTestCase {

	function test_default_payments_support(){
		add_theme_support( 'app-payments', array() );
		add_theme_support( 'app-price-format', array() );

		$this->assertNotEmpty( appthemes_payments_get_args() );
	}

	function test_modified_payments_support(){
		$items = array( 'test' => array( 'hello' ) );

		add_theme_support( 'app-payments', array(
			'items' => $items
		) );

		$this->assertEquals( appthemes_payments_get_args( 'items' ), $items );
	}


}
