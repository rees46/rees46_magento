<?php
/**
 * Copyright © 2017 REES46, INC. All rights reserved.
 */
class Rees46_Personalization_Helper_Data extends Mage_Core_Helper_Data
{
	const REES46_ACTION_LEAD = 'rees46/actions/action_lead';
	const REES46_ACTION_EXPORT = 'rees46/actions/action_export';
	const REES46_ENABLED = 'rees46/settings/enabled';
	const REES46_API_KEY = 'rees46/settings/api_key';
	const REES46_API_SECRET = 'rees46/settings/secret_key';

	public function isLeadTracking($store = null)
	{
		return Mage::getStoreConfigFlag(self::REES46_ACTION_LEAD, $store);
	}

	public function isExported($store = null)
	{
		return Mage::getStoreConfigFlag(self::REES46_ACTION_EXPORT, $store);
	}

	/**
	 * Checks whether news can be displayed in the frontend
	 *
	 * @param integer|string|Mage_Core_Model_Store $store
	 * @return boolean
	 */
	public function isEnabled($store = null)
	{
		return Mage::getStoreConfigFlag(self::REES46_ENABLED, $store);
	}

	/**
	 * Return REES46 API Key
	 * @param integer|string|Mage_Core_Model_Store $store
	 * @return string
	 */
	public function getAPIKey($store = null) {
		$api_key = Mage::getStoreConfig(self::REES46_API_KEY, $store);
		return $api_key ? $api_key : false;
	}

	/**
	 * Return REES46 Secret Key
	 * @param integer|string|Mage_Core_Model_Store $store
	 * @return string
	 */
	public function getSecretKey($store = null) {
		$secret_key = Mage::getStoreConfig(self::REES46_API_SECRET, $store);
		return $secret_key ? $secret_key : false;
	}

	/**
	 * Returns ids array of products in customer's cart
	 * @return array
	 */
	public function getCartProductIds() {
		$ids = [];

		$cart = Mage::getModel('checkout/cart')->getQuote();

		foreach ($cart->getAllVisibleItems() as $product) {
			$ids[] = $product->getProductId();
		}

		return json_encode($ids);
	}
}
