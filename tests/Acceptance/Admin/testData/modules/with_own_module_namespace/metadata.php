<?php

/**
 * Module information
 */
$aModule = array(
    'id'           => 'EshopAcceptanceTestModuleNine',
    'title'        => 'Test module #9 - namespaced',
    'description'  => 'Double the price. Show payment error message during checkout.',
    'thumbnail'    => 'module.png',
    'version'      => '1.0.0',
    'author'       => 'OXID eSales AG',
    'extend'      => array(
        \OxidEsales\Eshop\Application\Controller\PaymentController::class => \OxidEsales\EshopAcceptanceTestModule\Application\Controller\TestModuleNinePaymentController::class,
        \OxidEsales\Eshop\Core\Price::class => \OxidEsales\EshopAcceptanceTestModule\Application\Model\TestModuleNinePrice::class
    ),
    'files' => array(
    ),
    'settings' => array(
    )
);
