<?php
/**
 * Personalizatoin module observer
 *
 * @author Magento
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
	 * Подготавливает базовый массив данных о товаре для трекинга
	 * @param Mage_Catalog_Model_Product $product
	 * @return array
	 */
	private function _prepareCommonProductInfo(Mage_Catalog_Model_Product $product) {
		return array(
			'item_id' => $product->getId(),
			'name' => $product->getName(),
			'description' => $product->getDescription(),
			'categories' => $product->getCategoryIds(),
			'price' => $product->getSpecialPrice() ? $product->getSpecialPrice() : $product->getPrice(),
			'is_available' => $product->isAvailable(),
			'locations' => null,
			'url' => null,
			'image_url' => null,
			'tags' => null,
			// @todo: не забыть трекинг рекомендера
			'recommended_by' => null
		);
	}
}