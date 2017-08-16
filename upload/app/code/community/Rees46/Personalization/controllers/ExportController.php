<?php
/**
 * Copyright © 2017 REES46, INC. All rights reserved.
 */
class Rees46_Personalization_ExportController extends Mage_Core_Controller_Front_Action
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
	 * Exporting categories via HTTP API
	 * Error is written in var/log/system.log
	 */
	public function indexAction()
	{
		$categories = [];

		$curl_data = [
			'shop_id' => Mage::helper('rees46_personalization/data')->getAPIKey(),
			'shop_secret' => Mage::helper('rees46_personalization/data')->getSecretKey(),
			'categories' => []
		];

		$categories = Mage::helper('catalog/category')->getStoreCategories(false, true, true);

		if (count($categories) > 0){
			foreach($categories as $category){
				$curl_data['categories'][] = [
					'id' => $category->getId(),
					'name' => $category->getName(),
					'parent' => $category->getParentId(),
				];
			}
		}

		$return = Mage::helper('rees46_personalization/curl')->query('POST', 'https://api.rees46.com/import/categories', json_encode($curl_data));

		if ($return['info']['http_code'] == 204) {
			$this->_redirect('rees46/exportproduct');
		} else {
			Mage::log('REES46: could not export categories (' . $return['info']['http_code'] . ').');
		}
	}
}
