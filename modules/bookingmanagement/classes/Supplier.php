<?php

class Supplier extends SupplierCore
{
	public $price_delivery;
	public $price_return;

	/**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'supplier',
        'primary' => 'id_supplier',
        'multilang' => true,
        'fields' => array(
            'name' =>                array('type' => self::TYPE_STRING, 'validate' => 'isCatalogName', 'required' => true, 'size' => 64),
            'active' =>            array('type' => self::TYPE_BOOL),
            'date_add' =>            array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' =>            array('type' => self::TYPE_DATE, 'validate' => 'isDate'),

            /* Lang fields */
            'description' =>        array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml'),
            'meta_title' =>        array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 128),
            'meta_description' =>    array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 255),
            'meta_keywords' =>        array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 255),
            'price_delivery' =>   array('type' => self::TYPE_FLOAT, 'shop' => true, 'validate' => 'isPrice', 'required' => true),
            'price_return' =>   array('type' => self::TYPE_FLOAT, 'shop' => true, 'validate' => 'isPrice', 'required' => true),
            ),
        );

    /**
     * [getExchangeFeeOfCenter description]
     * @param  [integer]  $id_center     [description]
     * @param  [boolean]  $return_price    [description]
     * @return [float]               [description]
     */
    public function getExchangeFeeOfCenter($id_center, $return_price = false)
    {
        $price = 0;
        $sql = "SELECT price_delivery, price_return FROM `"._DB_PREFIX_."supplier` WHERE `id_supplier` = ".$id_center;

        if ($row = Db::getInstance()->getRow($sql)) {
            if ($return_price) {
                $price = $row['price_return'];
            } else {
                $price = $row['price_delivery'];
            }
        }

        return $price;
    }
}