<?php

class BookingManagement extends Module
{
	public function __construct()
	{
		$this->name = 'bookingmanagement';
		$this->tab = 'front_office_features';
		$this->version = '0.1';
		$this->author = 'PSA Team';
		$this->bootstrap = true;
		parent::__construct();
		$this->displayName = $this->l('Module of the booking management.');
		$this->description = $this->l('Module of the booking management.');
	}

	public function install()
	{
		// Call install parent method
		if (!parent::install()) {
			return false;
		}

		// Register hooks
		if (!$this->registerHook('displayFooterProduct')) {
			return false;
		}

		// Preset configuration values
		Configuration::updateValue('BOOKING_STANDARD_DAYS', '21');

		// All went well!
		return true;
	}
	public function uninstall()
	{
		// Call uninstall parent method
		if (!parent::uninstall()) {
			return false;
		}

		// Delete configuration values
		Configuration::deleteByName('BOOKING_STANDARD_DAYS');

		// All went well!
		return true;
	}

	public function getHookController($hook_name)
	{
		// Include the controller file
		require_once(dirname(__FILE__).'/controllers/hook/'. $hook_name.'.php');

		// Build dynamically the controller name
		$controller_name = $this->name.$hook_name.'Controller';

		// Instantiate controller
		$controller = new $controller_name($this, __FILE__, $this->_path);

		// Return the controller
		return $controller;
	}

	public function getContent()
	{
		$controller = $this->getHookController('getContent');
		return $controller->run();
	}

	public function hookDisplayFooterProduct()
	{
		$controller = $this->getHookController('displayFooterProduct');
		return $controller->run();
	}
}