<?php
/**
 * Abstract.php
 *
 * @category   CLS
 * @package    Integration
 * @author     David Alger <david@classyllama.com>
 * @copyright  Copyright (c) 2013 David Alger & Classy Llama Studios, LLC
 */

abstract class CLS_Integration_Model_Process_Abstract
{
    protected static function _getMemLimit() {
        static $limit = NULL;
        if ($limit === NULL) {
            $value = trim(ini_get('memory_limit'));
            $code = strtolower($value[strlen($value)-1]);
            switch ($code) {
                case 'g':   // intentional fall through
                    $value *= 1024;
                case 'm':   // intentional fall through
                    $value *= 1024;
                case 'k':   // intentional fall through
                    $value *= 1024;
            }
            $limit = (int)$value;
        }
        return $limit;
    }
    
    /**
     * Reschedules the cron job $interval seconds into the future.
     *
     * @param Mage_Cron_Model_Schedule $schedule
     * @param int $interval
     */
    protected function _rescheduleCron(Mage_Cron_Model_Schedule $pSchedule, $interval = 10) {
        $timestamp = Mage::getSingleton('core/date')->gmtTimestamp()+$interval;
        $schedule = Mage::getModel('cron/schedule');
        $schedule->setJobCode($pSchedule->getJobCode())
            ->setStatus(Mage_Cron_Model_Schedule::STATUS_PENDING)
            ->setCreatedAt($timestamp)
            ->setScheduledAt($timestamp)
        ;
        $schedule->save();

        return $this;
    }

    /**
     * Takes a data collections and process it within a memory monitoring loop
     *
     * @param CLS_Integration_Model_Resource_Erp_Db_Collection_Abstract $collection
     * @param string $callback
     */
    protected function _processDataCollection($collection, $callback) {
        $index = 0;
        $limit = self::_getMemLimit();        // store the memory limit in bytes for calculations
        $baseline = 0;                        // the current memory usage at last iteration
        $delta = 0;                           // maximum difference in memory usgae from one iteration to the next
        $space = NULL;                        // the remaining number of iterations we have based on the $delta
        
        foreach ($collection as $record) {
            $baseline = memory_get_usage();    // update the baseline
    
            try {
                $this->$callback($record);    // process the record
            } catch (Zend_Db_Exception $e) {
                // catch, log and skip items where an exception (like a deadlock or lock timeout) occurs, we don't want to die
                Mage::logException($e);
                continue;
            }
    
            if ($index == 0) {
                $baseline = memory_get_usage();    // if it's the first item, update this post-processing to avoid inflated delta
            } else {
                $delta = max($delta, memory_get_usage() - $baseline, 0.0001);    // calculate memory usage delta, we have a minimum to avoid division by zero! :)
                $space = floor(($limit - memory_get_usage()) / $delta);  // calculate the approximate iterations we have memory space for
            }
    
            // if we have space for less than 100 estimated iteration remaining, log a message and break to cleanup
            if ($space !== NULL && $space <= 100) {
                Mage::log("CLS_Integration [".__CLASS__."::".__FUNCTION__."]: Must terminate, within 100 iterations of remaining space allowed by memory_limit!");
                return false;
            }
        }
        return true;
    }
}
