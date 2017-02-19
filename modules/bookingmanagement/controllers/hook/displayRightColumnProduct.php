<?php

class BookingManagementDisplayRightColumnProductController
{
	public function __construct($module, $file, $path)
	{
		$this->file = $file;
		$this->module = $module;
		$this->context = Context::getContext(); $this->_path = $path;
	}

	public function initContent()
	{
		// Get list of suppliers
		require_once _PS_MODULE_DIR_.$this->module->name.'/define.php';
		$supplier = new Supplier();
		$supplier_list = $supplier->getSuppliers();
		$options = array();
		if (!empty($supplier_list)) {
			foreach ($supplier_list as $item) {
				$options[$item['id_supplier']] = $item['name'];
			}
		}

		$this->context->smarty->assign(array(
			'car_centers' => $options,
			));
	}
}