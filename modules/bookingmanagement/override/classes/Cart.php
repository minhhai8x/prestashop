<?php

require_once _PS_MODULE_DIR_.'bookingmanagement/define.php';

class Cart extends CartCore
{
    /**
     * Return useful informations for cart
     *
     * @return array Cart details
     */
	public function getSummaryDetails($id_lang = null, $refresh = false)
	{
		$context = Context::getContext();
		if (!$id_lang) {
			$id_lang = $context->language->id;
		}

		$delivery = new Address((int)$this->id_address_delivery);
		$invoice = new Address((int)$this->id_address_invoice);

        // New layout system with personalization fields
		$formatted_addresses = array(
			'delivery' => AddressFormat::getFormattedLayoutData($delivery),
			'invoice' => AddressFormat::getFormattedLayoutData($invoice)
			);

		$base_total_tax_inc = $this->getOrderTotal(true);
		$base_total_tax_exc = $this->getOrderTotal(false);

		$total_tax = $base_total_tax_inc - $base_total_tax_exc;

		if ($total_tax < 0) {
			$total_tax = 0;
		}

		$currency = new Currency($this->id_currency);

		$products = $this->getProducts($refresh);

		foreach ($products as $key => &$product) {
			$product['price_without_quantity_discount'] = Product::getPriceStatic(
				$product['id_product'],
				!Product::getTaxCalculationMethod(),
				$product['id_product_attribute'],
				6,
				null,
				false,
				false
				);

			if ($product['reduction_type'] == 'amount') {
				$reduction = (!Product::getTaxCalculationMethod() ? (float)$product['price_wt'] : (float)$product['price']) - (float)$product['price_without_quantity_discount'];
				$product['reduction_formatted'] = Tools::displayPrice($reduction);
			}
		}

		$gift_products = array();
		$cart_rules = $this->getCartRules();
		$total_shipping = $this->getTotalShippingCost();
		$total_shipping_tax_exc = $this->getTotalShippingCost(null, false);
		$total_products_wt = $this->getOrderTotal(true, Cart::ONLY_PRODUCTS);
		$total_products = $this->getOrderTotal(false, Cart::ONLY_PRODUCTS);
		$total_discounts = $this->getOrderTotal(true, Cart::ONLY_DISCOUNTS);
		$total_discounts_tax_exc = $this->getOrderTotal(false, Cart::ONLY_DISCOUNTS);

        // The cart content is altered for display
		foreach ($cart_rules as &$cart_rule) {
            // If the cart rule is automatic (wihtout any code) and include free shipping, it should not be displayed as a cart rule but only set the shipping cost to 0
			if ($cart_rule['free_shipping'] && (empty($cart_rule['code']) || preg_match('/^'.CartRule::BO_ORDER_CODE_PREFIX.'[0-9]+/', $cart_rule['code']))) {

				$cart_rule['value_real'] -= $total_shipping;
				$cart_rule['value_tax_exc'] -= $total_shipping_tax_exc;
				$cart_rule['value_real'] = Tools::ps_round($cart_rule['value_real'], (int)$context->currency->decimals * _PS_PRICE_COMPUTE_PRECISION_);
				$cart_rule['value_tax_exc'] = Tools::ps_round($cart_rule['value_tax_exc'], (int)$context->currency->decimals * _PS_PRICE_COMPUTE_PRECISION_);
				if ($total_discounts > $cart_rule['value_real']) {
					$total_discounts -= $total_shipping;
				}
				if ($total_discounts_tax_exc > $cart_rule['value_tax_exc']) {
					$total_discounts_tax_exc -= $total_shipping_tax_exc;
				}

                // Update total shipping
				$total_shipping = 0;
				$total_shipping_tax_exc = 0;
			}

			if ($cart_rule['gift_product']) {
				foreach ($products as $key => &$product) {
					if (empty($product['gift']) && $product['id_product'] == $cart_rule['gift_product'] && $product['id_product_attribute'] == $cart_rule['gift_product_attribute']) {
                        // Update total products
						$total_products_wt = Tools::ps_round($total_products_wt - $product['price_wt'], (int)$context->currency->decimals * _PS_PRICE_COMPUTE_PRECISION_);
						$total_products = Tools::ps_round($total_products - $product['price'], (int)$context->currency->decimals * _PS_PRICE_COMPUTE_PRECISION_);

                        // Update total discounts
						$total_discounts = Tools::ps_round($total_discounts - $product['price_wt'], (int)$context->currency->decimals * _PS_PRICE_COMPUTE_PRECISION_);
						$total_discounts_tax_exc = Tools::ps_round($total_discounts_tax_exc - $product['price'], (int)$context->currency->decimals * _PS_PRICE_COMPUTE_PRECISION_);

                        // Update cart rule value
						$cart_rule['value_real'] = Tools::ps_round($cart_rule['value_real'] - $product['price_wt'], (int)$context->currency->decimals * _PS_PRICE_COMPUTE_PRECISION_);
						$cart_rule['value_tax_exc'] = Tools::ps_round($cart_rule['value_tax_exc'] - $product['price'], (int)$context->currency->decimals * _PS_PRICE_COMPUTE_PRECISION_);

                        // Update product quantity
						$product['total_wt'] = Tools::ps_round($product['total_wt'] - $product['price_wt'], (int)$currency->decimals * _PS_PRICE_COMPUTE_PRECISION_);
						$product['total'] = Tools::ps_round($product['total'] - $product['price'], (int)$currency->decimals * _PS_PRICE_COMPUTE_PRECISION_);
						$product['cart_quantity']--;

						if (!$product['cart_quantity']) {
							unset($products[$key]);
						}

                        // Add a new product line
						$gift_product = $product;
						$gift_product['cart_quantity'] = 1;
						$gift_product['price'] = 0;
						$gift_product['price_wt'] = 0;
						$gift_product['total_wt'] = 0;
						$gift_product['total'] = 0;
						$gift_product['gift'] = true;
						$gift_products[] = $gift_product;

                        break; // One gift product per cart rule
                    }
                }
            }
        }

        foreach ($cart_rules as $key => &$cart_rule) {
        	if (((float)$cart_rule['value_real'] == 0 && (int)$cart_rule['free_shipping'] == 0)) {
        		unset($cart_rules[$key]);
        	}
        }

        // Get cart booking data
        $cart_booking_data = new CartBookingData();
        $booking_data = $cart_booking_data->getCartBookingDataByCart($this->id);

        $summary = array(
        	'delivery' => $delivery,
        	'delivery_state' => State::getNameById($delivery->id_state),
        	'invoice' => $invoice,
        	'invoice_state' => State::getNameById($invoice->id_state),
        	'formattedAddresses' => $formatted_addresses,
        	'products' => array_values($products),
        	'gift_products' => $gift_products,
        	'discounts' => array_values($cart_rules),
        	'is_virtual_cart' => (int)$this->isVirtualCart(),
        	'total_discounts' => $total_discounts,
        	'total_discounts_tax_exc' => $total_discounts_tax_exc,
        	'total_wrapping' => $this->getOrderTotal(true, Cart::ONLY_WRAPPING),
        	'total_wrapping_tax_exc' => $this->getOrderTotal(false, Cart::ONLY_WRAPPING),
        	'total_shipping' => $total_shipping,
        	'total_shipping_tax_exc' => $total_shipping_tax_exc,
        	'total_products_wt' => $total_products_wt,
        	'total_products' => $total_products,
        	'total_price' => $base_total_tax_inc,
        	'total_tax' => $total_tax,
        	'total_price_without_tax' => $base_total_tax_exc,
        	'is_multi_address_delivery' => $this->isMultiAddressDelivery() || ((int)Tools::getValue('multi-shipping') == 1),
        	'free_ship' =>!$total_shipping && !count($this->getDeliveryAddressesWithoutCarriers(true, $errors)),
        	'carrier' => new Carrier($this->id_carrier, $id_lang),
        	);

        if (!empty($booking_data)) {
        	$summary['price_delivery'] = $booking_data['price_delivery'];
        	$summary['price_return'] = $booking_data['price_return'];
        	$summary['delivery_name'] = $booking_data['dl_name'];
        	$summary['return_name'] = $booking_data['re_name'];
        }

        $hook = Hook::exec('actionCartSummary', $summary, null, true);
        if (is_array($hook)) {
        	$summary = array_merge($summary, array_shift($hook));
        }

        return $summary;
    }
}