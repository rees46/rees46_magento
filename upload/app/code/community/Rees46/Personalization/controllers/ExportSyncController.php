<?php
/**
 * Copyright © 2017 REES46, INC. All rights reserved.
 */
class Rees46_Personalization_ExportSyncController extends Mage_Core_Controller_Front_Action
{
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
	 * Deleting inactive products via HTTP API
	 * Error is written in var/log/system.log
	 */
	public function indexAction()
	{
		$products = [];

		$collection = Mage::getModel('catalog/product')
			->getCollection()
			->addAttributeToSort('entity_id', 'ASC')
			->joinField(
				'is_in_stock',
				'cataloginventory/stock_item',
				'is_in_stock',
				'product_id=entity_id',
				'{{table}}.stock_id=1',
				'left')
			->addAttributeToFilter('is_in_stock', ['gt' => 0]);

		foreach ($collection as $product) {
			$products[] = $product->getId();
		}

		if (count($products) > 0) {
			$curl_data = [
				'method' => 'PATCH',
				'shop_id' => Mage::helper('rees46_personalization/data')->getAPIKey(),
				'shop_secret' => Mage::helper('rees46_personalization/data')->getSecretKey(),
				'items' => $products
			];

			$return = Mage::helper('rees46_personalization/curl')->query('POST', 'http://api.rees46.com/import/products', json_encode($curl_data));

			if ($return['info']['http_code'] != 204) {
				Mage::log('REES46: could not sync products (' . $return['info']['http_code'] . ').');
			}
		}
	}
}
