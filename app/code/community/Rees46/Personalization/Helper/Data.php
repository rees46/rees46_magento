<?php
/**
 * Copyright © 2017 REES46, INC. All rights reserved.
 */
class Rees46_Personalization_Helper_Data extends Mage_Core_Helper_Data
{
	/**
	 * Path to store config if front-end output is enabled
	 *
	 * @var string
	 */
	const XML_PATH_ENABLED = 'personalization/view/enabled';

	/**
	 * REES46 API Key
	 * @var string
	 */
	const XML_PATH_API_KEY = 'personalization/view/api_key';

	/**
	 * REES46 Secret Key
	 * @var string
	 */
	const XML_PATH_SECRET_KEY = 'personalization/view/secret_key';

	/**
	 * Checks whether news can be displayed in the frontend
	 *
	 * @param integer|string|Mage_Core_Model_Store $store
	 * @return boolean
	 */
	public function isEnabled($store = null)
	{
		return Mage::getStoreConfigFlag(self::XML_PATH_ENABLED, $store);
	}

	/**
	 * Return REES46 API Key
	 * @param integer|string|Mage_Core_Model_Store $store
	 * @return string
	 */
	public function getAPIKey($store = null) {
		$api_key = Mage::getStoreConfig(self::XML_PATH_API_KEY, $store);
		return $api_key ? $api_key : false;
	}

	/**
	 * Returns ids array of products in customer's cart
	 * @return array
	 */
	public function getCartProductIds() {
		$cart = Mage::getModel('checkout/cart')->getQuote();
		return array_map(function($element){ return $element->getProductId(); }, $cart->getAllItems());
	}
}
