<?php

/**
 * Module information
 */
$aModule = array(
    'id'           => 'without_own_module_namespace',
    'title'        => 'Test module #10 - not namespaced',
    'description'  => 'Double the price. Show payment error message during checkout.',
    'thumbnail'    => 'module.png',
    'version'      => '1.0.0',
    'author'       => 'OXID eSales AG',
    'extend'      => array(
       # \OxidEsales\Eshop\Application\Controller\PaymentController::class => 'without_own_module_namespace/Application/Controller/TestModuleTenPaymentController',
       # \OxidEsales\Eshop\Core\Price::class => 'without_own_module_namespace/Application/Model/TestModuleTenPrice'
       'payment' => 'without_own_module_namespace/Application/Controller/TestModuleTenPaymentController',
       'oxprice' => 'without_own_module_namespace/Application/Model/TestModuleTenPrice'

    ),
    'files' => array('TestModuleTenModel'  => 'without_own_module_namespace/Application/Model/TestModuleTenModel.php',
                     'TestModuleTenPaymentController' => 'without_own_module_namespace/Application/Controller/TestModuleTenPaymentController.php',
                     'TestModuleTenPrice' => 'without_own_module_namespace/Application/Model/TestModuleTenPrice.php'
    ),
    'settings' => array(
    )
);
