<?php
/**
 * The admin class
 *
 * @since 2.0.0
 *
 * @author Kapil Paul
 *
 * @package DCoders\Bkash
 */

namespace DCoders\Bkash;

/**
 * Class Admin
 */
class Admin {
	/**
	 * Initialize the class
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function __construct() {
		$this->init_classes();
		$this->dispatch_actions();
	}

	/**
	 * Init Classes
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function init_classes() {
		new Admin\Menu();
	}

	/**
	 * Dispatch and bind actions
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function dispatch_actions() {

	}
}
