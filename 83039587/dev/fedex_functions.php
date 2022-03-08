<?php
function track_shipment($tracknumber)
{
    // Copyright 2009, FedEx Corporation. All rights reserved.
    // Version 6.0.0

    require_once('fedex-common_custom.php');

    //The WSDL is not included with the sample code.
    //Please include and reference in $path_to_wsdl variable.
    $path_to_wsdl = "wdsl/TrackService_v19.wsdl";

    ini_set("soap.wsdl_cache_enabled", "0");

    $client = new SoapClient($path_to_wsdl, array('trace' => 1)); // Refer to http://us3.php.net/manual/en/ref.soap.php for more information

    $fedex_request['WebAuthenticationDetail'] = array(
            'ParentCredential' => array(
                    'Key' => getProperty('parentkey'), 
                    'Password' => getProperty('parentpassword')
            ),
            'UserCredential' => array(
                    'Key' => getProperty('key'), 
                    'Password' => getProperty('password')
            )
    );

    $fedex_request['ClientDetail'] = array(
            'AccountNumber' => getProperty('shipaccount'), 
            'MeterNumber' => getProperty('meter')
    );
    $fedex_request['TransactionDetail'] = array('CustomerTransactionId' => '*** Track Request using PHP ***');
    $fedex_request['Version'] = array(
            'ServiceId' => 'trck', 
            'Major' => '19', 
            'Intermediate' => '0', 
            'Minor' => '0'
    );
    $fedex_request['SelectionDetails'] = array(
            'PackageIdentifier' => array(
                    'Type' => 'TRACKING_NUMBER_OR_DOORTAG',
                    'Value' => $tracknumber // Replace 'XXX' with a valid tracking identifier
            )
    );



    try {
            if(setEndpoint('changeEndpoint')){
                    $newLocation = $client->__setLocation(setEndpoint('endpoint'));
            }

            $response = $client ->track($fedex_request);

        if ($response -> HighestSeverity != 'FAILURE' && $response -> HighestSeverity != 'ERROR'){
                    if($response->HighestSeverity != 'SUCCESS'){
                            echo '<table border="1">';
                            echo '<tr><th>Track Reply</th><th>&nbsp;</th></tr>';
                            trackDetails($response->Notifications, '');
                            echo '</table>';
                    }else{
                    if ($response->CompletedTrackDetails->HighestSeverity != 'SUCCESS'){
                                    echo '<table border="1">';
                                echo '<tr><th>Shipment Level Tracking Details</th><th>&nbsp;</th></tr>';
                                trackDetails($response->CompletedTrackDetails, '');
                                    echo '</table>';
                            }else{
                                    echo '<table border="1">';
                                echo '<tr><th>Package Level Tracking Details</th><th>&nbsp;</th></tr>';
                                trackDetails($response->CompletedTrackDetails->TrackDetails, '');
                                    echo '</table>';
                            }
                    }
            printSuccess($client, $response);
        }else{
            printError($client, $response);
        } 

        writeToLog($client);    // Write to log file   
    } catch (SoapFault $exception) {
        printFault($exception, $client);
    }
    return $response->Notifications->Message;
}

function test_service($version='product')
{
    if ($version=='test') {
        require_once('fedex-common_custom.php');
    } else {
        require_once('fedex-common_custom.php');
    }
    

    //The WSDL is not included with the sample code.
    //Please include and reference in $path_to_wsdl variable.
    $path_to_wsdl = "wdsl/ValidationAvailabilityAndCommitmentService_v14.wsdl";

    ini_set("soap.wsdl_cache_enabled", "0");

    $client = new SoapClient($path_to_wsdl, array('trace' => 1)); // Refer to http://us3.php.net/manual/en/ref.soap.php for more information

    $fedex_request['WebAuthenticationDetail'] = array(
            'ParentCredential' => array(
                    'Key' => getProperty('parentkey'), 
                    'Password' => getProperty('parentpassword')
            ),
            'UserCredential' => array(
                    'Key' => getProperty('key'), 
                    'Password' => getProperty('password')
            )
    );

    $fedex_request['ClientDetail'] = array(
            'AccountNumber' => getProperty('shipaccount'), 
            'MeterNumber' => getProperty('meter')
    );
    $fedex_request['TransactionDetail'] = array('CustomerTransactionId' => ' *** Service Availability Request v5.1 using PHP ***');
    $fedex_request['Version'] = array(
            'ServiceId' => 'vacs', 
            'Major' => '14',
            'Intermediate' => '0', 
            'Minor' => '0'
    );
    $fedex_request['Origin'] = array(
            'PostalCode' => '77510', // Origin details
            'CountryCode' => 'US'
    );
    $fedex_request['Destination'] = array(
            'PostalCode' => '38017', // Destination details
            'CountryCode' => 'US'
     );
    $fedex_request['ShipDate'] = getProperty('serviceshipdate');
    $fedex_request['CarrierCode'] = 'FDXE'; // valid codes FDXE-Express, FDXG-Ground, FDXC-Cargo, FXCC-Custom Critical and FXFR-Freight
    $fedex_request['Service'] = 'PRIORITY_OVERNIGHT'; // valid code STANDARD_OVERNIGHT, PRIORITY_OVERNIGHT, FEDEX_GROUND, ...
    $fedex_request['Packaging'] = 'YOUR_PACKAGING'; // valid code FEDEX_BOX, FEDEX_PAK, FEDEX_TUBE, YOUR_PACKAGING, ...



    try {
            if(setEndpoint('changeEndpoint')){
                    $newLocation = $client->__setLocation(setEndpoint('endpoint'));
            }

            $response = $client ->serviceAvailability($fedex_request);

        if ($response -> HighestSeverity != 'FAILURE' && $response -> HighestSeverity != 'ERROR'){
            echo 'The following service type(s) are available.'. Newline;
            echo '<table border="1">';
            foreach ($response->Options as $optionKey => $option){
                    echo '<tr><td><table>';
                    if(is_string($option)){
                                    echo '<tr><td>' . $optionKey . '</td><td>' . $option . '</td></tr>';
                    }else{           
                                    foreach($option as $subKey => $subOption){
                                            echo '<tr><td>' . $subKey . '</td><td>' . $subOption . '</td></tr>';
                                    }
                    }
                    echo '</table></td></tr>';
            }
            echo'</table>';

            printSuccess($client, $response);
        }else{
            printError($client, $response);
        } 

        writeToLog($client);    // Write to log file   
    } catch (SoapFault $exception) {
        printFault($exception, $client);
    }
    return $response->Notifications->Message;
}

function cancel_shipping($tracknumber)
{
    require_once('fedex-common_custom.php');
    $path_to_wsdl = "wdsl/ShipService_v26.wsdl";
    ini_set("soap.wsdl_cache_enabled", "0");

    $client = new SoapClient($path_to_wsdl, array('trace' => 1)); // Refer to http://us3.php.net/manual/en/ref.soap.php for more information

    $fedex_request['WebAuthenticationDetail'] = array(
            'ParentCredential' => array(
                    'Key' => getProperty('parentkey'), 
                    'Password' => getProperty('parentpassword')
            ),
            'UserCredential' => array(
                    'Key' => getProperty('key'), 
                    'Password' => getProperty('password')
            )
    );

    $fedex_request['ClientDetail'] = array(
            'AccountNumber' => getProperty('shipaccount'), 
            'MeterNumber' => getProperty('meter')
    );
    $fedex_request['TransactionDetail'] = array('CustomerTransactionId' => ' *** Cancel Shipment Request using PHP ***');
    $fedex_request['Version'] = array(
            'ServiceId' => 'ship', 
            'Major' => '26', 
            'Intermediate' => '0', 
            'Minor' => '0'
    );
    $fedex_request['ShipTimestamp'] = date('c');
    $fedex_request['TrackingId'] = array(
            'TrackingIdType' =>'GROUND', // valid values EXPRESS, GROUND, USPS, etc
            'TrackingNumber'=>$tracknumber,
    );  
    $fedex_request['DeletionControl'] = 'DELETE_ONE_PACKAGE'; // Package/Shipment



    try {
            if(setEndpoint('changeEndpoint')){
                    $newLocation = $client->__setLocation(setEndpoint('endpoint'));
            }

            $response = $client ->deleteShipment($fedex_request);

        if ($response -> HighestSeverity != 'FAILURE' && $response -> HighestSeverity != 'ERROR'){
            printSuccess($client, $response);
        }else{
            printError($client, $response);
        } 

        writeToLog($client);    // Write to log file   
    } catch (SoapFault $exception) {
        printFault($exception, $client);
    }
    return $response->Notifications->Message;
}


function fedex_send_order($order_data,$silent=''){
//    echo '<pre>';
//    print_r ();
//    echo '</pre>';
//    die();
    
    require_once('/var/www/itkkit/bitrix/admin/fedex/fedex-common_custom.php');

    //The WSDL is not included with the sample code.
    //Please include and reference in $path_to_wsdl variable.
    $path_to_wsdl = __DIR__."/wdsl/ShipService_v26.wsdl";
    define('FEDEX_PATH','/var/www/itkkit/bitrix/admin/fedex/');
//    define('SHIP_LABEL', $order_data['order_id'].'_shipexpresslabel.pdf', true);  // PDF label file. Change to file-extension .pdf for creating a PDF label (e.g. shiplabel.pdf)
//    echo '<pre>';
//    print_r (SHIP_LABEL);
//    echo '</pre>';

    ini_set("soap.wsdl_cache_enabled", "0");

    $client = new SoapClient($path_to_wsdl, array('trace' => 1)); // Refer to http://us3.php.net/manual/en/ref.soap.php for more information

    $fedex_request['WebAuthenticationDetail'] = array(
            'ParentCredential' => array(
                    'Key' => getProperty('parentkey'), 
                    'Password' => getProperty('parentpassword')
            ),
            'UserCredential' => array(
                    'Key' => getProperty('key'), 
                    'Password' => getProperty('password')
            )
    );

    $fedex_request['ClientDetail'] = array(
            'AccountNumber' => getProperty('shipaccount'), 
            'MeterNumber' => getProperty('meter')
    );
    $fedex_request['TransactionDetail'] = array('CustomerTransactionId' => '*** Express International Shipping Request using PHP ***');
    $fedex_request['Version'] = array(
            'ServiceId' => 'ship', 
            'Major' => '26', 
            'Intermediate' => '0', 
            'Minor' => '0'
    );

    $fedex_request['RequestedShipment'] = array(
            'ShipTimestamp' => date('c'),
            'DropoffType' => 'REGULAR_PICKUP', // valid values REGULAR_PICKUP, REQUEST_COURIER, DROP_BOX, BUSINESS_SERVICE_CENTER and STATION
            'ServiceType' => 'INTERNATIONAL_PRIORITY', // valid values STANDARD_OVERNIGHT, PRIORITY_OVERNIGHT, FEDEX_GROUND, ...
            'PackagingType' => 'YOUR_PACKAGING', // valid values FEDEX_BOX, FEDEX_PAK, FEDEX_TUBE, YOUR_PACKAGING, ...
            'Shipper' => addShipper(),
            'Recipient' => addRecipient($order_data),
            'ShippingChargesPayment' => addShippingChargesPayment(),
            'CustomsClearanceDetail' => addCustomClearanceDetail($order_data),                                                                                                       
            'LabelSpecification' => addLabelSpecification(),
            'CustomerSpecifiedDetail' => array(
                    //'MaskedData'=> 'SHIPPER_ACCOUNT_NUMBER'
            ), 
            'PackageCount' => 1,
            'RequestedPackageLineItems' => array(
                    '0' => addPackageLineItem1($order_data)
            ),
            
            
    );
    
//echo '<pre>';
//print_r ($fedex_request);
//echo '</pre>';



    try{
            if(setEndpoint('changeEndpoint')){
                    $newLocation = $client->__setLocation(setEndpoint('endpoint'));
            }

            $response = $client->processShipment($fedex_request); // FedEx web service invocation

        if ($response->HighestSeverity != 'FAILURE' && $response->HighestSeverity != 'ERROR'){
            //успех!
            if ($silent!=='silent') {
                printSuccess($client, $response);
            }
            $output = '<!DOCTYPE html><html><head><meta charset="utf-8">'
                    . '<title>'.$order_data['order_id'].'_shipexpresslabel для печати</title>'
                    . '</head>'
                    . '<body>'
                    . '<img src="'.$order_data['order_id'].'_shipexpresslabel.png" style="width: 100%;">'
                    . '</body>'
                    . '</html>';
            $fp = fopen(FEDEX_PATH.$order_data['order_id'].'_shipexpresslabel.png', 'wb');
            fwrite($fp, ($response->CompletedShipmentDetail->CompletedPackageDetails->Label->Parts->Image));
            fclose($fp);
            file_put_contents(FEDEX_PATH.$order_data['order_id'].'_shipexpresslabel.html', $output);
            echo '<a href="https://itkkit.ru/bitrix/admin/sale_print.php?PROPS_ENABLE=Y&doc=waybillnew_2&ORDER_ID='.$order_data['order_id'].'&SHOW_ALL=Y" target="_blank">Инвойс на заказ №'.$order_data['order_id'].'</a></br>';
            echo 'Label <a href="https://www.itkkit.com/bitrix/admin/fedex/'.$order_data['order_id'].'_shipexpresslabel.html'.'" target="_blank">'.$order_data['order_id'].'_shipexpresslabel.html'.'</a> was generated.</br>';  
            $send_result['tracknum']=str_replace(' ', '',$response->CompletedShipmentDetail->CompletedPackageDetails->OperationalDetail->OperationalInstructions[5]->Content);
            $send_result['message']=$response->Notifications->Message;
            CModule::IncludeModule("sale");
            $arOrder = CSaleOrder::GetByID($order_data['order_id']);
            if ($arOrder)
            {
               $arFields = array(
                  "TRACKING_NUMBER" => $send_result['tracknum'],
                  "TRACKING_WEIGHT" => $order_data['weight_kg']*1000,
                  "COMMENTS" => 'https://www.itkkit.com/bitrix/admin/fedex/'.$order_data['order_id'].'_shipexpresslabel.html',
               );
               CSaleOrder::Update($order_data['order_id'], $arFields);
            }
            
        }else{
            //ошибка!
            printError($client, $response);
            $send_result['tracknum']='none';
            $send_result['message']=$response->Notifications->Message;
        }

            writeToLog($client);    // Write to log file
    } catch (SoapFault $exception) {
        $text=htmlspecialchars($client->__getLastRequest());
        echo '<pre>';
        print_r ($text);
        echo '</pre>';
        
        printFault($exception, $client);
    }
    return $send_result;
};

function addShipper(){
	$shipper = array(
		'Contact' => array(
			'PersonName' => 'itk store',
			'CompanyName' => 'itk store',
			'PhoneNumber' => '+37167671505'
		),
		'Address' => array(
			'StreetLines' => array('z. a. meierovica blvd 18'),
			'City' => 'Riga',
			'StateOrProvinceCode' => '',
			'PostalCode' => '1050',
			'CountryCode' => 'LV'
		)
	);
	return $shipper;
}

function addRecipient($order_data){
//    echo '<pre>';
//    print_r ($order_data);
//    echo '</pre>';
//    die();
	$recipient = array(
		'Contact' => array(
			'PersonName' => $order_data['contact_name'],
			'CompanyName' => '',
			'PhoneNumber' => $order_data['phone_no'],
                        'EMailAddress' => $order_data['email'],
		),
		'Address' => array(
			'StreetLines' => array($order_data['address1'],),
			'City' => $order_data['city'],
			'StateOrProvinceCode' => '',
			'PostalCode' => $order_data['zip'],
			'CountryCode' => 'RU',
			'Residential' => false
		)
	);
	return $recipient;	                                    
}

function addShippingChargesPayment(){
	$shippingChargesPayment = array('PaymentType' => 'SENDER',
        'Payor' => array(
			'ResponsibleParty' => array(
				'AccountNumber' => getProperty('billaccount'),
				'Contact' => null,
				'Address' => array(
					'CountryCode' => 'LV'
				)
			)
		)
	);
	return $shippingChargesPayment;
}

function addCustomClearanceDetail($order_data){
    foreach ($order_data['order_content'] as $key=>$order_item) {
        if (isset($order_item['TYPE'])) {
            $fedex_items[]=array(
                    'NumberOfPieces' => 1,
                    'Description' => $order_item['TYPE'],//выдрать из категории
                    'CountryOfManufacture' => 'LV',//LV
                    'Weight' => array(
                            'Units' => 'KG', //Сделать инструмент веса по категориям. Надо выгрузить в HL блок, и дать возможность редактировать.
                            'Value' => $order_item['WEIGHT_KG'],
                    ),
                    'Quantity' => 1,
                    'QuantityUnits' => 'EA',
                    'UnitPrice' => array( //Цена товара
                            'Currency' => 'EUR', 
                            'Amount' => $order_item['PRICE']
                    ),
                    'CustomsValue' => array( //Нет таможенного сбора.
                            'Currency' => 'EUR', 
                            'Amount' => $order_item['PRICE']
                    )
                );
        }
    };
//    echo '<pre>';
//print_r ($fedex_items);
//echo '</pre>';
    
	$customerClearanceDetail = array(
		'DutiesPayment' => array(
			'PaymentType' => 'SENDER', // valid values RECIPIENT, SENDER and THIRD_PARTY
			'Payor' => array(
				'ResponsibleParty' => array(
					'AccountNumber' => getProperty('dutyaccount'),
					'Contact' => null,
					'Address' => array(
						'CountryCode' => 'LV'
					)
				)
			)
		),
		'DocumentContent' => 'NON_DOCUMENTS',                                                                                            
		'CustomsValue' => array(
			'Currency' => 'EUR', 
			'Amount' => $order_data['order_price']
		),
		'Commodities' => $fedex_items,
		'ExportDetail' => array(
			'B13AFilingOption' => 'NOT_REQUIRED'
		)
	);
	return $customerClearanceDetail;
}

function addLabelSpecification(){
	$labelSpecification = array(
		'LabelFormatType' => 'COMMON2D', // valid values COMMON2D, LABEL_DATA_ONLY
		'ImageType' => 'PNG',  // valid values DPL, EPL2, PDF, ZPLII and PNG
		'LabelStockType' => 'PAPER_7X4.75'
	);
	return $labelSpecification;
}

function addPackageLineItem1($order_data){
	$packageLineItem = array(
		'SequenceNumber'=>1,
		'GroupPackageCount'=>1,
		'Weight' => array(
			'Value' => $order_data['weight_kg'], //Сделать инструмент веса по категориям. 
			'Units' => 'KG'
		),
		'Dimensions' => array(
			'Length' => 30, //https://pyrus.com/t#id83039587
			'Width' => 40,
			'Height' => 10,
			'Units' => 'CM'
		),
                'CustomerReferences' => array(
                    '0' => array(
                            'CustomerReferenceType' => 'CUSTOMER_REFERENCE', 
                            'Value' => $order_data['reference']
                    ),
                )
	);
	return $packageLineItem;
}


function fedex_addr_validation($order_data){
    //Валидация адреса
    include_once('fedex-common_custom.php');
    $path_to_wsdl =  __DIR__."/wdsl/AddressValidationService_v4.wsdl"; 
    ini_set("soap.wsdl_cache_enabled", "0");

    $client = new SoapClient($path_to_wsdl, array('trace' => 1)); // Refer to http://us3.php.net/manual/en/ref.soap.php for more information

    $fedex_request=[
        'WebAuthenticationDetail'=>[
            'ParentCredential' => [
                'Key' => getProperty('parentkey'), 
                'Password' => getProperty('parentpassword')
            ],
            'UserCredential' => [
                'Key' => getProperty('key'), 
                'Password' => getProperty('password')
            ]
        ],

        'ClientDetail'=>[
            'AccountNumber' => getProperty('shipaccount'), 
            'MeterNumber' => getProperty('meter'),
            'Localization' => [
                'LanguageCode'=>'EN',
                'LocaleCode' => 'us',
            ],
        ],

        'TransactionDetail' => [
            'CustomerTransactionId' => '*** TEST Address Validation Request using PHP ***',
            'Localization' => [
                    'LanguageCode'=>'EN',
                    'LocaleCode' => 'us',
            ],
        ],

        'Version'=>[
            'ServiceId' => 'aval', 
            'Major' => '4', 
            'Intermediate' => '0', 
            'Minor' => '0',
        ],

        'InEffectAsOfTimestamp'=>date('c'),


    ];

    $fedex_request['AddressesToValidate'] = array(
            0 => array(
                    'ClientReferenceId' => $order_data['reference'],
                    'Address' => array(
                            'StreetLines' => array($order_data['address1']),
                            'City' => $order_data['city'],
                            'PostalCode' => $order_data['zip'],
                            'CountryCode' => 'RU',
                    ),
            ),
    );

    try {
            if(setEndpoint('changeEndpoint')){
                    $newLocation = $client->__setLocation(setEndpoint('endpoint'));
            }

        $response = $client ->addressValidation($fedex_request);

        if ($response -> HighestSeverity != 'FAILURE' && $response -> HighestSeverity != 'ERROR'){
            $addressResult=$response -> AddressResults;
//            echo '<pre>';
//            print_r ($response);
//            echo '</pre>';
            
            //Если улица подтверждена - то идем дальше
            if ($addressResult->Attributes[17]->Value) {
//                echo '<pre>';
//                print_r ('Улица подтверждена!');
//                echo '</pre>';
//                if($addressResult->EffectiveAddress){
//        		echo 'Proposed Address:' . Newline;
//        		echo '<table border="1">';
//        		printAddress($addressResult->EffectiveAddress);
//        		echo '</table>';
//        	}
                return true;
            }
        }else{
            printError($client, $response);
            writeToLog($client);
            writeToLog($response);
            return false;
        }
    } catch (SoapFault $exception) {
        printFault($exception, $client);
        writeToLog($client);
        writeToLog($response);
        return false;
    }
}


function printAddress($addressLine){
	foreach ($addressLine as $key => $value){
		if(is_array($value) || is_object($value)){
			printAddress($value);
		}else{
			echo '<tr><td>'. $key . '</td><td>' . $value . '</td></tr>';
		}
	}
}


function get_delivery_data($order_id){
    //Выбираем данные в заказ из собственно свойств заказа а не из профилей польщователя, иначе невозможно редактировать в админке кривые адреса.
    
    $db_props = CSaleOrderPropsValue::GetOrderProps($order_id);
    
    $delivery_data=[];
    while ($arProps = $db_props->Fetch()) {
        $delivery_data[$arProps['CODE']]=$arProps['VALUE'];
    }
    
    return($delivery_data);
}

function get_order_data($order_id){
    $order_detail=CSaleOrder::GetByID($order_id);
//    $user_id=$order_detail['USER_ID'];
//    $rsUser = CUser::GetByID($user_id)->Fetch();
    $delivery_data=get_delivery_data($order_id);
    $order_price=$order_detail['PRICE'];
   
    $order_content=get_order_content($order_id);
    
    $phone_prepare=str_replace(' ','',$delivery_data['DELIVERY_PHONE']);
    $phone_prepare=str_replace('(','',$phone_prepare);
    $phone_prepare=str_replace(')','',$phone_prepare);
    $phone_prepare=str_replace('-','',$phone_prepare);
    
    
    
    $result=[
        'order_id'=>$order_id,
        'order_price'=>$order_price,
        'zip'=>$delivery_data['DELIVERY_ZIP'],
        'contact_name'=>$delivery_data['DELIVERY_CONTACT_NAME'].' '.$delivery_data['DELIVERY_CONTACT_LAST_NAME'],
        'address1'=>$delivery_data['DELIVERY_STREET'].','.$delivery_data['DELIVERY_HOUSE'].','.$delivery_data['DELIVERY_FLAT'],
        'city'=>$delivery_data['DELIVERY_CITY'],
        'phone_no'=>$phone_prepare,
        'email'=>$delivery_data['DELIVERY_CONTACT_EMAIL'],
        'weight_kg'=>$order_content['weight'],
        'total_customs_value'=>0,
        'reference'=>$order_id.'_label',
        'order_content'=>$order_content,
    ];
   
//    echo '<pre>';
//    print_r ($result);
//    echo '</pre>';
//    die();
//    echo '<pre>';
//    print_r ($delivery_data);
//    echo '</pre>';
//    echo '<pre>';
//    print_r ($result);
//    echo '</pre>';
    return $result;
}

function get_order_content($order_id) {
    $result='';
    //++++++++++++++++++++++Собираем информацию по заказу+++++++++++++++++++++++++++
    //надо посчитать вес посылки
    $dbBasketItems = CSaleBasket::GetList(
            array(
                ),
            array(
                    "ORDER_ID" => $order_id,
                ),
            false,
            false,
            array('PRODUCT_ID','NAME','PRICE')
        );

    while ($order_item = $dbBasketItems->Fetch())
    {
//        echo '<pre>';
//        print_r ($order_item);
//        echo '</pre>';
        

        //проверяем товар это или торговое предложение
        $mxResult = CCatalogSku::GetProductInfo($order_item['PRODUCT_ID']);
//        echo '<pre>';
//        print_r ($mxResult);
//        echo '</pre>';
        
        if (is_array($mxResult))
        {
            //echo 'ID товара = '.$mxResult['ID'];
            $PRODUCT_ID=$mxResult['ID'];
        }
        else
        {
            //ShowError('Это не торговое предложение');
            $PRODUCT_ID=$order_item['PRODUCT_ID'];
        }

        //ищем ID раздела в котором находится данный товар
        $res = CIBlockElement::GetByID($PRODUCT_ID)->Fetch();
        $prop=CIBlockElement::GetByID($PRODUCT_ID)->GetNextElement()->GetProperties();
        $order_item['NAME']=$prop['EN_NAME']['VALUE'];
        $order_item['PRICE']=round($order_item['PRICE']/1.21, 2);
//        echo '<pre>';
//        print_r ($order_item);
//        echo '</pre>';
//        print "<pre>"; print_r($prop['EN_NAME']['VALUE']); print "</pre>";
//        echo '<pre>';
//        print_r ($res);
//        echo '</pre>';
        
        $select = ["UF_WEIGHT_GR","UF_EN_NAME"];
        $sort = ["SORT" => "ASC"];
        $filter = [
            'IBLOCK_ID' => 1,
            'ID' => $res['IBLOCK_SECTION_ID'],
        ];


        $order_item['WEIGHT_KG']=0;
        $rsResult = CIBlockSection::GetList($sort,$filter,false,$select)->Fetch();
//        echo '<pre>';
//        print_r ($rsResult);
//        echo '</pre>';
        $order_item['WEIGHT_KG']=$rsResult["UF_WEIGHT_GR"]/1000;
        $order_item['TYPE']=$rsResult['UF_EN_NAME'];
    //    echo '<pre>';
    //    print_r ($order_item);
    //    echo '</pre>';
        $order_items[]=$order_item;
        $result[]=$order_item;
    }
//        echo '<pre>';
//        print_r ($order_items);
//        echo '</pre>';

        $weight=0;    
        foreach ($order_items as $item) {
            $weight+=floatval($item['WEIGHT_KG']);
        }

//        echo '<pre>';
//        print_r ($weight);
//        echo '</pre>';
        $result['weight']=$weight;
        return $result;
    //-------------------------Собираем информацию по заказу------------------------
}
