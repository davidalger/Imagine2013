-- 
--  cls_integration_process_lock - table schema
--  

CREATE TABLE `cls_integration_process_lock` (
  `code` varchar(50) NOT NULL COMMENT 'Job Code',
  `locked_at` timestamp NULL DEFAULT NULL COMMENT 'Locked At',
  `freed_at` timestamp NULL DEFAULT NULL COMMENT 'Freed At',
  `status` smallint(6) NOT NULL DEFAULT '0' COMMENT 'Lock Status',
  PRIMARY KEY (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Holds an atomically set lock to prevent overlapping jobs.';


-- 
--  cls_integration_process_lock - atomic lock update
--  ref: CLS_Integration_Model_Resource_Process_Lock::obtainLock
-- 

UPDATE `cls_integration_process_lock` SET `status` = 1 WHERE `status` = 0 AND `code` = 'product';
