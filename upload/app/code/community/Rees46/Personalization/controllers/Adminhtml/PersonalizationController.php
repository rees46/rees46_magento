<?php
/**
 * Copyright © 2017 REES46, INC. All rights reserved.
 */
class Rees46_Personalization_Adminhtml_PersonalizationController extends Mage_Adminhtml_Controller_Action
{
	/**
	 * Init actions
	 *
	 * @return Magentostudy_News_Adminhtml_NewsController
	 */
	protected function _initAction()
	{
		/* load layout, set active menu and breadcrumbs */
		$this->loadLayout()
			->_setActiveMenu('personalization/overview')
			->_addBreadcrumb(
				Mage::helper('rees46_personalization')->__('Personalization'),
				Mage::helper('rees46_personalization')->__('Personalization')
			)
			->_addBreadcrumb(
				Mage::helper('rees46_personalization')->__('Overview'),
				Mage::helper('rees46_personalization')->__('Overview')
			)
		;
		$this->_addContent($this->getLayout()->createBlock('adminhtml/template')->setTemplate('rees46/personalization/index.phtml'));
		return $this;
	}

	/**
	 * Index action
	 */
	public function indexAction()
	{
		$this->_title($this->__('Personalization'))
			->_title($this->__('Overview'));

		$this->_initAction();
		$this->renderLayout();
	}

	/**
	 * Check the permission to run it
	 *
	 * @return boolean
	 */
	protected function _isAllowed()
	{
		switch ($this->getRequest()->getActionName()) {
			default:
				return Mage::getSingleton('admin/session')->isAllowed('personalization/overview');
				break;
		}
	}
}
