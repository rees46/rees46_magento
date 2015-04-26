<?php
/**
 * News Image Helper
 *
 * @author Magento
 */
class Rees46_Personalization_Helper_Event extends Mage_Core_Helper_Abstract
{

	const REES46_SESSION_KEY_IDENTIFIER = 'rees46_events';

	/**
	 * Помещает информацию о событии в буфер для последующей отрисовки их в шаблоне
	 * @param $name
	 * @param $data
	 */
	public function pushEvent($name, $data) {
		$events = $this->getEventsQueue();
		array_push($events, array(
			'name' => $name,
			'data' => $data
		));
		Mage::getSingleton('core/session')->setData(self::REES46_SESSION_KEY_IDENTIFIER, $events);
//		var_dump($events);
	}


	/**
	 * Return array of REES46 events storage
	 * @param bool $and_clear_queue Clear events if set to true
	 * @return array
	 */
	public function getEventsQueue($and_clear_queue = false) {
		$events = Mage::getSingleton('core/session')->getData(self::REES46_SESSION_KEY_IDENTIFIER);
		if(!is_array($events)) {
			$events = array();
		}
		if($and_clear_queue) {
			$this->clearEventsQueue();
		}
		return $events;
	}


	/**
	 * Clear REES46 events storage
	 */
	public function clearEventsQueue() {
		Mage::getSingleton('core/session')->unsetData(self::REES46_SESSION_KEY_IDENTIFIER);
	}



}