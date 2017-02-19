<?php

class BookingManagementGetContentController
{
	public function __construct($module, $file, $path)
	{
		$this->file = $file;
		$this->module = $module;
		$this->context = Context::getContext(); $this->_path = $path;
	}

	public function processConfiguration()
	{
		if (Tools::isSubmit('booking_form'))
		{
			$booking_standard_days = Tools::getValue('booking_standard_days');
			Configuration::updateValue('BOOKING_STANDARD_DAYS', $booking_standard_days);
			$this->context->smarty->assign('confirmation', 'ok');
		}
	}

	public function renderForm()
	{
		$fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->module->l('Booking management configuration'),
					'icon' => 'icon-car'
				),
				'input' => array(
					array(
						'type' => 'text',
						'label' => $this->module->l('Standard days:'),
						'name' => 'booking_standard_days',
						'desc' => $this->module->l('Booking standard days.'),
					),
				),
				'submit' => array('title' => $this->module->l('Save'))
			)
		);

		$helper = new HelperForm();
		//$helper->table = 'mymodcomments';
		$helper->default_form_language = (int)Configuration::get('PS_LANG_DEFAULT');
		$helper->allow_employee_form_lang = (int)Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG');
		$helper->submit_action = 'booking_form';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->module->name.'&tab_module='.$this->module->tab.'&module_name='.$this->module->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->tpl_vars = array(
			'fields_value' => array(
				'booking_standard_days' => Tools::getValue('booking_standard_days', Configuration::get('BOOKING_STANDARD_DAYS')),
			),
			'languages' => $this->context->controller->getLanguages()
		);

		return $helper->generateForm(array($fields_form));
	}

	public function run()
	{
		$this->processConfiguration();
		$html_confirmation_message = $this->module->display($this->file, 'getContent.tpl');
		$html_form = $this->renderForm();
		return $html_confirmation_message.$html_form;
	}
}