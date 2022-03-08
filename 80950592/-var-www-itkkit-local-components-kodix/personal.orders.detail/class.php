<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

/**
 * <pre>
 * Class CKodixOrdersDetailComponent
 * Created by:  KODIX 11.12.2014 13:25
 * Email:       support@kodix.ru
 * Web:         www.kodix.ru
 * Developer:   Salnikov Dmitry
 * </pre>
 * @uses KDXPickupDelivery, "kodix.eshop"
 */

class CKodixOrdersDetailComponent extends CBitrixComponent
{

    public function onPrepareComponentParams($arParams)
    {
        return $arParams;
    }


    public function executeComponent()
    {
        if(!CModule::IncludeModule('sale')) die('sale module');
        $this->arResult['ORDER']=KDXOrder::getFullById($this->arParams["ORDER_ID"]);
        if(!$this->arResult['ORDER']){
            show404();
        }
        $this->arResult["STATUSES"]=KDXOrder::getStatuses();
        $this->arResult["DELIVERY"]=KDXDelivery::getArrayNames();
        $this->arResult["CITIES"]=KDXAddress::getCities();
        $this->arResult['PAYMENT']=array();
        $res = CSalePaySystem::GetList(array(),array('ACTIVE'=>'Y'));
        while($fields = $res->Fetch()){
            $this->arResult['PAYMENT'][$fields['ID']]=$fields;
        }
        if(Bitrix\Main\Loader::includeModule('kodix.eshop')) {
            if ($this->arResult['ORDER']->properties['PICKUP_POINT_XML_ID']['VALUE']) {
                $this->arResult['PICKUP_POINT'] = kdxPickupDelivery::getByXMLID($this->arResult['ORDER']->properties['PICKUP_POINT_XML_ID']['VALUE']);
            }
        }
        $this->IncludeComponentTemplate();
    }



}