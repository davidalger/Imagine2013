<?php

// 
//  data install to setup records in cls_integration_process_lock table
//  

$locks = array('product', 'product_pricing', 'inventory', 'customer', 'order');

foreach ($locks as $code) {
    $lock = Mage::getModel('cls_integration/process_lock')->setCode($code)->save();
}
