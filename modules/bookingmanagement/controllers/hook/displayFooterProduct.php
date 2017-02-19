<?php

class BookingManagementDisplayFooterProductController
{
	public function __construct($module, $file, $path)
	{
		$this->file = $file;
		$this->module = $module;
		$this->context = Context::getContext(); $this->_path = $path;
	}

	public function run()
	{
		$this->context->controller->addJqueryUI('ui.datepicker');
		$this->context->controller->addJS(array(
                _THEME_DIR_.'modules/'.$this->module->name.'/product.js',
            ));
	}
}