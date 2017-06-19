<?php
/**
 * Copyright © 2017 REES46, INC. All rights reserved.
 */
class Rees46_Personalization_Helper_Event extends Mage_Core_Helper_Abstract
{
	const REES46_SESSION_KEY_IDENTIFIER = 'rees46_events';

	/**
	 * Collect event's data to buffer for future rendering in template
	 * @param $name
	 * @param $data
	 */
	public function pushEvent($name, $data) {
		$events = $this->getEventsQueue();

		array_push($events, [
			'name' => $name,
			'data' => $data
		]);

		Mage::getSingleton('core/session')->setData(self::REES46_SESSION_KEY_IDENTIFIER, $events);
	}

	/**
	 * Return array of REES46 events storage
	 * @param bool $and_clear_queue Clear events if set to true
	 * @return array
	 */
	public function getEventsQueue($and_clear_queue = false) {
		$events = Mage::getSingleton('core/session')->getData(self::REES46_SESSION_KEY_IDENTIFIER);

		if (!is_array($events)) {
			$events = [];
		}

		if ($and_clear_queue) {
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
