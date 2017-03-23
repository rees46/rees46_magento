<?php
/**
 * Copyright © 2017 REES46, INC. All rights reserved.
 */
class Rees46_Personalization_Model_Observer
{
	/**
	 * Event after show product.
	 * Used to track product views
	 *
	 * @param Varien_Event_Observer $observer
	 */
	public function productViewed(Varien_Event_Observer $observer)
	{
		$product = $observer->getEvent()->getData('product');
		if(!is_null($product)) {
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
		if(!is_null($product)) {
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
		if(!is_null($product)) {
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
		if(!is_null($order)) {
			$order_data = array(
				'order_id' => $order->getIncrementId(),
				'order_price' => $order->getBaseGrandTotal(),
				'products' => array()
			);
			$items = $order->getItemsCollection();
			foreach($items as $item) {
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
		$images = $product->getMediaGalleryImages();

		if($images && $images->count() > 0) {
			$image = $images->getFirstItem()->getUrl();
		} else {
			$image = null;
		}

		return array(
			'id' => $product->getId(),
			'stock' => $product->isAvailable(),
			'price' => $product->getSpecialPrice() ? $product->getSpecialPrice() : $product->getPrice(),
			'name' => $product->getName(),
			'categories' => implode(',', $product->getCategoryIds()),
			'image' => $image,
			'url' => $product->getProductUrl(false),
		);
	}
}
