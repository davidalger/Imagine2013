<?php
/**
 * Lock.php
 *
 * @category   CLS
 * @package    Integration
 * @author     David Alger <david@classyllama.com>
 * @copyright  Copyright (c) 2013 David Alger & Classy Llama Studios, LLC
 */

class CLS_Integration_Model_Resource_Process_Lock extends Mage_Core_Model_Resource_Db_Abstract
{
    protected $_isPkAutoIncrement = false;
    
	public function _construct() {
		$this->_init('cls_integration/process_lock', 'code');
	}
	
	public function obtainLock(CLS_Integration_Model_Process_Lock $object) {
	    $data = $this->_prepareDataForSave($object);
	    unset($data[$this->getIdFieldName()]);
	    $data['status'] = CLS_Integration_Model_Process_Lock::STATUS_LOCKED;
	    
	    
	    $adapter = $this->_getWriteAdapter();
	    $condition = array(
	        $this->getIdFieldName().'=?' => $object->getId(),
	    	'status=?' => CLS_Integration_Model_Process_Lock::STATUS_FREE,
	    );
	    
	    return $adapter->update($this->getMainTable(), $data, $condition) == 1;
	}
}
