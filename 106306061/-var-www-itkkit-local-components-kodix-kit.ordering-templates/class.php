<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

class CKodixOrderingComponent extends CBitrixComponent
{
    /** @var KDXOrder */
    public $order;

    public $page = '';

    public $profileID = false;
    public $addressClass = false;

    public function onPrepareComponentParams($arParams)
    {
        $arParams['CLOSE_CHECKOUT'] = ($arParams['CLOSE_CHECKOUT'] == 'Y') ? 'Y' : 'N';
        return $arParams;
    }


    public function executeComponent()
    {
        global $APPLICATION;

        if ($this->arParams['CLOSE_CHECKOUT'] == 'Y') {
            $this->IncludeComponentTemplate('close');
            return;
        }
        if ($_SERVER['REQUEST_METHOD'] == 'GET' && !empty($_REQUEST['SUCCESS']) && intval($_REQUEST['ORDER_ID']) > 0) {
            global $USER;
            $this->arResult['ORDER'] = KDXOrder::getFullById(intval($_REQUEST['ORDER_ID']), $USER->GetID());

            if ($this->arResult['ORDER']) {
                $this->IncludeComponentTemplate('success');
                return;
            }
        }

        CBitrixComponent::includeComponentClass('kodix:personal.addresses');
        $this->addressClass = new CKodixPersonalAddressComponent;
        $this->addressClass->initComponent('kodix:personal.addresses');
        $this->addressClass->prepareFieldMap();
        $this->addressClass->collectProps();
        $this->addressClass->postHandler();
        $this->addressClass->loadUserAddresses();

        $this->arResult['ADDRESSES'] = $this->addressClass->returnLoadUserAddresses();

        $this->addressClass->collectCountries();
        $this->addressClass->setTemplateName('order');

        $this->order = KDXOrder::loadFromSession();

        try {
            $this->handlePost($_POST);
        } catch (Exception $e) {
            $this->arResult["ERRORS"][] = $e->getMessage();
        }

//        printr($this->arResult["ERRORS"]);
//        printr($_SESSION['ORDERING']);	
		
		

        if ($_SESSION["ORDERING"]["STEP"] < 1) {
            $_SESSION["ORDERING"]["STEP"] = 1;
        }

        $this->profileID = KDXAddress::getLastAddressId();

        if (!intval($this->profileID)) {
            $_SESSION["ORDERING"]["STEP"] = 1;
        }

        if ($this->profileID > 0 && $_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->order->setAddress($this->profileID);
        }


        if ($_SESSION["ORDERING"]["STEP"] < 2 && $this->profileID > 0 && $_SERVER['REQUEST_METHOD'] != 'POST') {
            //
            $this->order->delivery_id = false;
            $this->order->price_delivery = 0;
            $this->order->comment = false;
            $this->order->pay_system_id = false;
            $this->order->saveToSession();
            $_SESSION["ORDERING"]["STEP"] = 2;
        }

        $this->setResultForStep();
        $this->order->calculateSaleDiscount();
		
        $this->arResult["AVAILABLE"] = $this->order->calculatedOrder['BASKET_ITEMS'];
		

        $criteoTovars = array();
        foreach ($this->arResult["AVAILABLE"] as $tovars) {
            if ($tovars['PROPS']['PARENT_ID']) {
                $criteoTovars[$tovars['PROPS']['PARENT_ID']] = array(
                    'id' => $tovars['PROPS']['PARENT_ID'],
                    'price' => round($tovars['PRICE']),
                    'quantity' => $criteoTovars[$tovars['PROPS']['PARENT_ID']]['quantity'] + round($tovars['QUANTITY'])
                );
            }
        }
        sort($criteoTovars);
        $criteoView = array('event' => 'viewBasket', 'item' => $criteoTovars);
        $APPLICATION->SetPageProperty("CRITEOVIEW", ',' . json_encode($criteoView));

        /*$this->order->getVATPrice($this->order->calculatedOrder['ORDER_PRICE']);
        if (!$this->order->add_vat) {
            $this->order->calculatedOrder['ORDER_PRICE'] -= $this->order->vat_price;
        }*/
        if (!$this->order->add_vat) {
            $this->order->getVATPrice($this->order->calculatedOrder['ORDER_PRICE'] - $this->order->price_delivery);
            $this->order->calculatedOrder['ORDER_PRICE'] -= $this->order->vat_price;
        } else {
            $this->order->getVATPrice($this->order->calculatedOrder['ORDER_PRICE'] - $this->order->price_delivery);
        }
        $this->arResult["ORDER"] = $this->order;
		
		

        $this->IncludeComponentTemplate($this->page);
    }

    protected function handlePost($data)
    {

        if (empty($data)) {
            return false;
        }

        $_SESSION["ORDERING"]["NEXT_COMMENT"] = 0;

        if (isset($_REQUEST['CART'])) {
            foreach ($_REQUEST['QUANTITY'] as $bID => $quantity) {
                CSaleBasket::Update($bID, array('QUANTITY' => intval($quantity)));
            }
            CSaleBasket::UpdateBasketPrices(CSaleBasket::GetBasketUserID(true), SITE_ID);

//            $this->order->cart = new KDXCart();
            if ($this->order->profile_id) {
                $this->order->setAddress($this->order->profile_id);
                $_SESSION["ORDERING"]["STEP"] = 2;
            } else {
                $_SESSION["ORDERING"]["STEP"] = 1;
            }

            $this->order->delivery_id = false;
            $this->order->price_delivery = 0;
            $this->order->comment = false;
            $this->order->pay_system_id = false;
            $this->order->saveToSession();
        }

        if ($data["PROFILE"] && empty($data["DELIVERY"])) {
            $profile_id = intval($data["PROFILE"]);
            if (intval($profile_id)) {
                $this->arResult["SELECTED_DELIVERY"] = $this->order->delivery_id;

                $this->order->setAddress($profile_id);
                $this->order->delivery_id = false;
                $this->order->price_delivery = 0;
                $this->order->comment = false;
                $this->order->pay_system_id = false;
                $this->order->saveToSession();
                $_SESSION["ORDERING"]["STEP"] = 2;
                return true;
            }
        }

        if ($data["DELIVERY"] && empty($data["PAYSYSTEM"])) {
            $delivery_id = $data["DELIVERY"];

            if ($data["DELIVERY"] == 'kdx_self:courier') {
                $Qprofile = CSaleOrderUserProps::GetList(array(), array("USER_ID" => CUser::GetId(), 'NAME' => 'self'), false, array('nTopCount' => 1));
                $Rprofile = $Qprofile->Fetch();
                if ($Rprofile['ID']) {
                    $this->order->setAddress($Rprofile['ID']);
                }
            }

            $this->order->setDelivery($delivery_id);
            $this->order->pay_system_id = false;
            $this->order->saveToSession();

            $_SESSION["ORDERING"]["STEP"] = 3;
            return true;
        }

        if ($data["PAYSYSTEM"] && empty($data["COMMENT"])) {
            $ps_id = $data["PAYSYSTEM"];
            if ($this->order->profile_id) {
                $this->order->setAddress($this->order->profile_id);
            }
            $this->order->setPaySystem($ps_id);
            $this->order->saveToSession();
            $_SESSION["ORDERING"]["STEP"] = 4;

            if ($data['NEXT_COMMENT'] == 1 && $_REQUEST['SUCCESS'] == 'Y') {
                $_SESSION["ORDERING"]["NEXT_COMMENT"] = 1;
            }
            return true;
        }

        if ($data["COMMENT"]) {
            $this->order->comment = $data["COMMENT"];
            $this->order->saveToSession();
            return true;
        }


        if ($_SESSION["ORDERING"]["STEP"] == 4 && $data["CREATE_ORDER"] == 'Y') {
//            $this->order->comment=$data["comment"];
			
            //echo '<pre>';print_r($this->order);exit;
            if ($this->order->create()) {
                KDXCart::ClearCoupon();
                $this->page = 'success';
            }
            return true;
        }
    }


    protected function setResultForStep()
    {
        if ($_SESSION["ORDERING"]["STEP"] > 1) {
            $this->arResult["DELIVERIES"] = KDXDelivery::getList();

            $this->arResult["IS_DELIVERIES_AVAILABLE"] = false;

            $availableCount = 0;
            $firstDeliv = false;

            foreach ($this->arResult["DELIVERIES"] as $delivery) {
                if (count($delivery['PROFILES']) > 0) {
                    if ($delivery['SID'] != 'kdx_self') {
                        $this->arResult["IS_DELIVERIES_AVAILABLE"] = true;

                        $availableCount += count($delivery['PROFILES']);

                        if (!$firstDeliv) {
                            foreach ($delivery['PROFILES'] as $key => $arProfile) {
                                $firstDeliv = $delivery['SID'] . ':' . $key;
                                $this->arResult["FIRST_DELIVERY"] = $firstDeliv;
                                break;
                            }
                        }
                    }
                }
            }

            if ($availableCount == 1) {
                /*
                 *  если у нас только 1 способ доставки
                 *  сразу перекидываем на него
                 *  закрыл
                    $this->order->setDelivery($firstDeliv);
                    $this->order->saveToSession();
                    if($_SESSION["ORDERING"]["STEP"] == 2) $_SESSION["ORDERING"]["STEP"]++;
                */
            }
        }

        if ($_SESSION["ORDERING"]["STEP"] > 2) {
            //
            if ($this->order->delivery_id) {
                $this->order->setDelivery($this->order->delivery_id);
                $this->arResult["DELIVERY"] = new KDXDelivery($this->order->delivery_id);
                list($deliveryID, $deliveryProfileID) = explode(':', $this->order->delivery_id);
                $this->arResult["PAY_SYSTEMS"] = KDXPaySystem::getList($deliveryID, $deliveryProfileID);
            }

            if (count($this->arResult["PAY_SYSTEMS"]) == 1) {
                $arPaySys = current($this->arResult["PAY_SYSTEMS"]);
                $this->order->setPaySystem($arPaySys['ID']);
                $this->order->saveToSession();

                if ($_SESSION["ORDERING"]["STEP"] == 3) {
                    $_SESSION["ORDERING"]["STEP"]++;
                }
            }

        }

        CSaleBasket::UpdateBasketPrices(CSaleBasket::GetBasketUserID(true), SITE_ID);
        $cart = new KDXCart();
        $this->arResult["CART"] = $cart;
        $this->arResult["AVAILABLE"] = $cart->getAvailable();
        $this->arResult["UNAVAILABLE"] = $cart->getUnavailable();
        $this->arResult['IS_EMPTY'] = empty($this->arResult["AVAILABLE"]) && empty($this->arResult["UNAVAILABLE"]);

        $this->arResult['COUPONS'] = KDXCart::getCoupons();
        $this->arResult['APPLIED_COUPONS'] = $cart->getAppliedCoupons();


        if (KDXCurrency::$CurrentCurrency !== CURRENCY_DEFAULT) {
            $this->arResult['NEED_CONVERT'] = true;
        }
    }
}
