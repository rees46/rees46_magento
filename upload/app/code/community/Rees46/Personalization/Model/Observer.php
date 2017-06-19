<?php
/**
 * Copyright © 2017 REES46, INC. All rights reserved.
 */
class Rees46_Personalization_Model_Observer
{
	/**
	 * Lead tracking and sending a link to export
	 *
	 * @param Varien_Event_Observer $observer
	 */
	public function leadTracking(Varien_Event_Observer $observer)
	{
		$controllerAction = $observer->getEvent()->getAction();

		if ($controllerAction->getRequest()->getParam('section') == 'rees46') {
			if (!Mage::helper('rees46_personalization/data')->isLeadTracking()) {
				$params = [
					'website' => Mage::getBaseUrl(),
					'cms_version' => Mage::getVersion(),
					'module_version' => (string)Mage::getConfig()->getNode()->modules->Rees46_Personalization->version,
					'email' => Mage::getStoreConfig('trans_email/ident_general/email'),
					'first_name' => Mage::getSingleton('admin/session')->getUser()->getFirstname(),
					'last_name' => Mage::getSingleton('admin/session')->getUser()->getLastname(),
					'phone' => Mage::getStoreConfig('general/store_information/phone'),
					'city' => Mage::getStoreConfig('general/store_information/address'),
					'country' => Mage::getModel('directory/country')->loadByCode(Mage::getStoreConfig('general/country/default'))->getName()
				];

				$ch = curl_init();

				curl_setopt($ch, CURLOPT_HEADER, false);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
				curl_setopt($ch, CURLOPT_URL, 'https://rees46.com/trackcms/magento?' . http_build_query($params));
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

				curl_exec($ch);
				curl_close($ch);

				Mage::getModel('core/config')->saveConfig('rees46/actions/action_lead', true);
			}

			if (!Mage::helper('rees46_personalization/data')->isExported()) {
				$curl_data = [
					'store_key' => Mage::helper('rees46_personalization/data')->getAPIKey(),
					'store_secret' => Mage::helper('rees46_personalization/data')->getSecretKey(),
					'yml_file_url' => Mage::helper('core/url')->getHomeUrl() . 'rees46/export'
				];

				Mage::helper('rees46_personalization/curl')->query('PUT', 'https://rees46.com/api/shop/set_yml', json_encode($curl_data));

				Mage::getModel('core/config')->saveConfig('rees46/actions/action_export', true);
			}
		}
	}

	/**
	 * Event after show product.
	 * Used to track product views
	 *
	 * @param Varien_Event_Observer $observer
	 */
	public function productViewed(Varien_Event_Observer $observer)
	{
		$product = $observer->getEvent()->getData('product');

		if (!is_null($product)) {
			$product_data = $this->_prepareCommonProductInfo($product);

			Mage::helper('rees46_personalization/event')->pushEvent('view', $product_data);
		}
	}

	/**
	 * Event after product added to cart.
	 * Used to track product carts
	 *
	 * @param Varien_Event_Observer $observer
	 */
	public function productAddedToCart(Varien_Event_Observer $observer)
	{
		$product = $observer->getEvent()->getData('product');

		if (!is_null($product)) {
			$product_data = $this->_prepareCommonProductInfo($product);

			Mage::helper('rees46_personalization/event')->pushEvent('cart', $product_data);
		}
	}

	/**
	 * Event after product removed from cart.
	 * Used to track product carts
	 *
	 * @param Varien_Event_Observer $observer
	 */
	public function productRemovedFromCart(Varien_Event_Observer $observer)
	{
		$product = $observer->getEvent()->getQuoteItem()->getProduct();

		if (!is_null($product)) {
			$product_data = $this->_prepareCommonProductInfo($product);

			Mage::helper('rees46_personalization/event')->pushEvent('remove_from_cart', $product_data);
		}
	}

	/**
	 * Event after order created.
	 *
	 * @param Varien_Event_Observer $observer
	 */
	public function purchaseHappened(Varien_Event_Observer $observer)
	{
		$order = $observer->getEvent()->getOrder();

		if (!is_null($order)) {
			$order_data = [
				'order_id' => $order->getIncrementId(),
				'order_price' => $order->getBaseGrandTotal(),
				'products' => []
			];

			$items = $order->getItemsCollection();

			foreach ($items as $item) {
				$product = $item->getProduct();
				$product_data = $this->_prepareCommonProductInfo($product);
				$product_data['amount'] = $item->getQtyToShip();
				$order_data['products'][] = $product_data;
			}

			Mage::helper('rees46_personalization/event')->pushEvent('purchase', $order_data);
		}
	}

	/**
	 * Prepare base array of data about product for tracking
	 * @param Mage_Catalog_Model_Product $product
	 * @return array
	 */
	private function _prepareCommonProductInfo(Mage_Catalog_Model_Product $product) {
		return [
			'id' => $product->getId(),
			'stock' => $product->isAvailable(),
		];
	}
}
