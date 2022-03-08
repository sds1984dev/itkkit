<?
\Bitrix\Main\Loader::includeModule('kodix.main');
\Bitrix\Main\Loader::registerAutoLoadClasses(
	'kodix.sale',
	array(
		'KDXSaleEventHandler' => 'general/KDXSaleEventHandler.php',
		'KDXCBRF' => 'general/KDXCBRF.php',
		'kdxBankLVCurrency' => 'general/kdxBankLVCurrency.php',
		'KDXCurrency' => 'general/KDXCurrency.php',
		'KDXCurrencyChecker' => 'general/KDXCurrencyChecker.php',
		'KDXSaleDataCollector' => 'general/KDXSaleDataCollector.php',
		'KDXCart' => 'general/KDXCart.php',
		'KDXProductProvider' => 'general/KDXProductProvider.php',
		'KDXWishList' => 'general/KDXWishList.php',
		'KDXAddress' => 'general/KDXAddress.php',
		'KDXOrder' => 'general/KDXOrder.php',
		'KDXDelivery' => 'general/KDXDelivery.php',
		'KDXPaySystem' => 'general/KDXPaySystem.php',

		'KDXLocationLink' => 'general/properties/KDXLocationLink.php',

        'KDXDeliveryEMS' => 'sale_delivery/delivery_kdx_ems.php',
        'KDXDeliveryFedex' => 'sale_delivery/delivery_kdx_fedex.php',
        'KDXDeliverySelf' => 'sale_delivery/delivery_kdx_self.php',
        'KDXDeliveryDPD' => 'sale_delivery/delivery_kdx_dpd.php',
        'KDXDeliveryCourier' => 'sale_delivery/delivery_kdx_courier.php',
        'KDXDeliveryPony' => 'sale_delivery/delivery_kdx_pony.php',
        'KDXDeliveryRusPost' => 'sale_delivery/delivery_kdx_rus_post.php',
        'KDXDeliveryLatvianPost' => 'sale_delivery/delivery_kdx_latvian_post.php',
        'KDXDeliveryCourierOutRing' => 'sale_delivery/delivery_kdx_courier_out_ring.php',
        'KDXDeliveryDHL' => 'sale_delivery/delivery_kdx_dhl.php',
        'KDXDeliveryEXPDEL' => 'sale_delivery/delivery_kdx_expdel.php',
        'KDXDeliveryUPSEC' => 'sale_delivery/delivery_kdx_upsec.php',
        'KDXDeliveryUPSEX' => 'sale_delivery/delivery_kdx_upsex.php',

        'Kodix\Sale\Filter\FilterTable' => 'lib/filter.php',
        '\Kodix\Sale\Filter\FilterTable' => 'lib/filter.php',
        '\Kodix\Sale\WishList\WishListTable' => 'lib/wishlist.php',
        'Kodix\Sale\WishList\WishListTable' => 'lib/wishlist.php',
	)
)
?>