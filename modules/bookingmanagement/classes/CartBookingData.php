<?php

class CartBookingData extends ObjectModel
{
	public $id;
	public $id_cart;
	public $id_guest;
	public $id_customer;
	public $id_currency;
	public $id_product;
	public $id_delivery_center;
	public $id_return_center;
	public $quantity;
	public $num_days;
	public $comment;
	public $date_from;
	public $date_to;
	public $date_add;
	public $date_upd;

	public static $definition = array(
		'table' => 'cart_booking_data',
		'primary' => 'id',
		'fields' => array(
			'id_cart' => 		array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
			'id_guest' => 		array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
			'id_customer' => 	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
			'id_currency' =>    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
			'id_product' => 	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
			'id_delivery_center' =>  array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
			'id_return_center' => 	 array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
			'quantity' => 		array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
			'num_days' => 		array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
			'comment' =>  		array('type' => self::TYPE_STRING),
			'date_from' =>  	array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
			'date_to' =>  		array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
			'date_add' =>  		array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
			'date_upd' =>  		array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
			),
		);
	
	/**
     * [getCartBookingDataByCart description]
     * @param  [integer]  $id_cart     [description]
     * @return [array]               [description]
     */
	public function getCartBookingDataByCart($id_cart)
	{
		$result = array();
		$sql = "SELECT cbd.id, cbd.id_cart, dl.price_delivery, dl.name as dl_name, re.name as re_name, re.price_return
		FROM `"._DB_PREFIX_."cart_booking_data` AS cbd
		INNER JOIN `"._DB_PREFIX_."supplier` AS dl ON (cbd.id_delivery_center = dl.id_supplier)
		INNER JOIN `"._DB_PREFIX_."supplier` AS re ON (cbd.id_return_center = re.id_supplier)
		WHERE cbd.id_cart = ".$id_cart;

		if ($row = Db::getInstance()->getRow($sql)) {
			$result = $row;
		}

		return $result;
	}
}