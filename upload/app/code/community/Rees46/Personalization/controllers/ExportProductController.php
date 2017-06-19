<?php
/**
 * Copyright © 2017 REES46, INC. All rights reserved.
 */
class Rees46_Personalization_ExportProductController extends Mage_Core_Controller_Front_Action
{
	const REES46_EXPORT_LIMIT = 1000;

	/**
	 * Pre dispatch action that allows to redirect to no route page in case of disabled extension through admin panel
	 */
	public function preDispatch()
	{
		parent::preDispatch();

		if (!Mage::helper('rees46_personalization/data')->isEnabled()
			|| !Mage::helper('rees46_personalization/data')->getAPIKey()
			|| !Mage::helper('rees46_personalization/data')->getSecretKey()
		) {
			$this->setFlag('', 'no-dispatch', true);
			$this->_redirect('noRoute');
		}
	}

	/**
	 * Exporting products via HTTP API
	 * Error is written in var/log/system.log
	 */
	public function indexAction()
	{
		$products = [];

		if (Mage::app()->getRequest()->getParam('start')) {
			$start = Mage::app()->getRequest()->getParam('start');
		} else {
			$start = 0;
		}

		$collection = Mage::getModel('catalog/product')
			->getCollection()
			->addAttributeToSelect('name')
			->addAttributeToSelect('description')
			->addAttributeToSelect('image')
			->addAttributeToFilter('status', ['eq' => 1])
			->addUrlRewrite()
			->addFinalPrice()
			->addAttributeToSort('entity_id', 'ASC')
			->joinField(
				'is_in_stock',
				'cataloginventory/stock_item',
				'is_in_stock',
				'product_id=entity_id',
				'{{table}}.stock_id=1',
				'left')
			->addAttributeToFilter('is_in_stock', ['gt' => 0])
			->setCurPage($start)
			->setPageSize(self::REES46_EXPORT_LIMIT);

		foreach ($collection as $product) {
			$products[] = [
				'id' => $product->getId(),
				'name' => $product->getName(),
				'price' => $product->getFinalPrice(),
				'currency' => Mage::app()->getStore()->getCurrentCurrencyCode(),
				'url' => $product->getProductUrl(),
				'picture' => (string)Mage::helper('catalog/image')->init($product, 'image')->resize(300, 300),
				'available' => $product->getIsInStock(),
				'categories' => $product->getCategoryIds(),
				'locations' => [],
				'brand' => '',
				'barcode' => '',
				'price_margin' => 0,
				'tags' => [],
				'is_child' => false,
				'is_fashion' => false
			];
		}

		if (count($products) > 0) {
			$curl_data = [
				'shop_id' => Mage::helper('rees46_personalization/data')->getAPIKey(),
				'shop_secret' => Mage::helper('rees46_personalization/data')->getSecretKey(),
				'items' => $products
			];

			$return = Mage::helper('rees46_personalization/curl')->query('PUT', 'http://api.rees46.com/import/products', json_encode($curl_data));

			if ($return['info']['http_code'] == 204) {
				if (count($products) == self::REES46_EXPORT_LIMIT) {
					$this->_redirect('rees46/exportproduct', ['_query' => ['start' => $start + self::REES46_EXPORT_LIMIT]]);
				} else {
					$this->_redirect('rees46/exportsync');
				}
			} else {
				Mage::log('REES46: could not export products ' . $this->get('start') . ' (' . $return['info']['http_code'] . ').');
			}
		} else {
			$this->_redirect('rees46/exportsync');
		}
	}
}
