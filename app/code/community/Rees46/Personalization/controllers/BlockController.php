<?php
/**
 * News frontend controller
 *
 * @author Magento
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
		$product_ids = $this->getRequest()->getParam('ids');
		$product_ids = explode(',', $product_ids);
		$recommender_type = $this->getRequest()->getParam('type');
		$minimum_recommended_products = intval($this->getRequest()->getParam('minimum'));
		$products = array();

		$attributes = Mage::getSingleton('catalog/config')->getProductAttributes();
		$collection = Mage::getModel('catalog/product')
			->getCollection()
			->addAttributeToFilter('entity_id', array('in' => $product_ids))
			->addAttributeToSelect($attributes);

		/* Sort items as REES46 returned */
		foreach($product_ids as $id) {
			foreach($collection as $product) {
				if($product->getId() == $id) {
					$products[] = $product;
				}
			}
		}

		if( $minimum_recommended_products > 0 && count($products) >= $minimum_recommended_products ) {

			/* Get block title - from locale or from front-end */
			$block_title = Mage::helper('rees46_personalization')->__($recommender_type);
			if($this->getRequest()->getParam('block_title')) {
				$block_title = $this->getRequest()->getParam('block_title');
				$block_title = urldecode($block_title);
			}

			$html = '<div class="rees46 rees46-recommend"><div class="recommender-block-title">' . $block_title . '</div><div class="recommended-items">';
			foreach($products as $product) {

				/* Specific settings cause problems with large photos, so find it another way */
				$productForImage = Mage::getModel('catalog/product')->load($product->getId());

				/* Sometimes Magento adds special attributes to URL's, so add our attributes depending of this situation */
				$product_url = $product->getProductUrl(false);
				if( strpos($product_url, '?') !== false ) {
					$product_url = str_replace('?', '?recommended_by=' . $recommender_type . '&	', $product_url);
				} else {
					$product_url = $product_url . '?recommended_by=' . $recommender_type;
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