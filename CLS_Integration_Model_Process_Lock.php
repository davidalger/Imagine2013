<?php
/**
 * Lock.php
 *
 * @category   CLS
 * @package    Integration
 * @author     David Alger <david@classyllama.com>
 * @copyright  Copyright (c) 2013 David Alger & Classy Llama Studios, LLC
 */

class CLS_Integration_Model_Process_Lock extends Mage_Core_Model_Abstract
{
    const STATUS_LOCKED = 1;
    const STATUS_FREE = 0;
    
	protected function _construct() {
		$this->_init('cls_integration/process_lock');
	}
	
	public function obtainLock() {
	    $this->setLockedAt(Mage::getSingleton('core/date')->gmtDate());
	    
	    $result = $this->_getResource()->obtainLock($this);
	    if ($result == true) {
	        $this->setStatus(self::STATUS_LOCKED);
	    }
	    return $result;
	}
	
	public function releaseLock() {
	    $this->setFreedAt(Mage::getSingleton('core/date')->gmtDate())
	        ->setStatus(self::STATUS_FREE)
	        ->save()
	    ;
	    return $this;
	}
	
	public function isLocked() {
	    return $this->getStatus() == self::STATUS_LOCKED;
	}
}
