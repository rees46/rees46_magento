<?php
/**
 * Copyright © 2017 REES46, INC. All rights reserved.
 */
class Rees46_Personalization_BlockController extends Mage_Core_Controller_Front_Action
{
	/**
	 * Pre dispatch action that allows to redirect to no route page in case of disabled extension through admin panel
	 */
	public function preDispatch()
	{
		parent::preDispatch();

		if (!Mage::helper('rees46_personalization')->isEnabled()) {
			$this->setFlag('', 'no-dispatch', true);
			$this->_redirect('noRoute');
		}
	}

	/**
	 * Render recommended products as block
	 */
	public function indexAction()
	{
		$this->loadLayout();
		$products = [];

		$product_ids = explode(',', $this->getRequest()->getParam('ids'));
		$type = Mage::helper('core')->escapeHtml($this->getRequest()->getParam('type'));
		$limit = intval($this->getRequest()->getParam('limit'));

		$attributes = Mage::getSingleton('catalog/config')->getProductAttributes();
		$collection = Mage::getModel('catalog/product')
			->getCollection()
			->addAttributeToFilter('entity_id', ['in' => $product_ids])
			->addAttributeToSelect($attributes);

		/* Sort items as REES46 returned */
		foreach($product_ids as $id) {
			foreach($collection as $product) {
				if ($product->getId() == $id) {
					$products[] = $product;

					if (count($products) == $limit) {
						break;
					}
				}
			}
		}

		if (count($products) > 0) {
			if ($this->getRequest()->getParam('title')) {
				$title = urldecode($this->getRequest()->getParam('title'));
			} else {
				$title = Mage::helper('rees46_personalization')->__($type);
			}

			$html = '<div class="rees46 rees46-recommend"><div class="recommender-block-title">' . Mage::helper('core')->escapeHtml($title) . '</div><div class="recommended-items">';

			foreach($products as $product) {
				$productForImage = Mage::getModel('catalog/product')->load($product->getId());

				$product_url = $product->getProductUrl(false);

				if (strpos($product_url, '?') !== false) {
					$product_url = str_replace('?', '?recommended_by=' . $type . '&	', $product_url);
				} else {
					$product_url = $product_url . '?recommended_by=' . $type;
				}

				$html .= '<div class="recommended-item">
				  <div class="recommended-item-photo">
					<a href="' . $product_url . '"><img src="' . $productForImage->getThumbnailUrl(200, 200) . '"></a></div>
				  <div class="recommended-item-title">
					<a href="' . $product_url . '">' . $product->getName() . '</a>
				  </div>
				  <div class="recommended-item-price">' . $product->getFormatedPrice() . '</div>
				  <div class="recommended-item-action"><a href="' . $product_url . '">' . Mage::helper('rees46_personalization')->__('More') . '</a></div>
				</div>';
			}

			$html .= '</div></div>';

			echo $html;
		} else {
			echo '';
		}
	}
}
