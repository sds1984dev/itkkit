<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();


class CKodixCartComponent extends CBitrixComponent
{

    public function onPrepareComponentParams($arParams)
    {
        return $arParams;
    }


    public function executeComponent()
    {
        $cart=new KDXCart();
        $records=$cart->getAvailable();
        $products=$cart->getProducts();
        $this->arResult["ITEMS"]=$records;
        $this->arResult["PRODUCTS"]=$products;
        $this->arResult["CART"]=$cart;
        $this->IncludeComponentTemplate();
    }
}
