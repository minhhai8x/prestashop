CREATE TABLE IF NOT EXISTS `PREFIX_cart_booking_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_cart` int(11) NOT NULL,
  `id_guest` int(11) NOT NULL,
  `id_customer` int(11) NOT NULL,
  `id_currency` int(11) NOT NULL,
  `id_product` int(11) NOT NULL,
  `id_delivery_center` int(11) NOT NULL,
  `id_return_center` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `num_days` int(11) NOT NULL,
  `comment` text,
  `date_from` datetime NOT NULL,
  `date_to` datetime NOT NULL,
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;