<?php
/**
 * Created by:  KODIX 08.07.14 10:00
 * Email:       support@kodix.ru
 * Web:         www.kodix.ru
 * Developer:   Salnikov Dmitry
 * Класс предназначен для сбора и кеширования данных для интренет магазина
 */

class KDXSaleDataCollector {
    private static $arData=array();

    /** не плодим лишних объектов*/
    private function __construct()    { /* ... @return Singleton */ } // Защищаем от создания через new Singleton
    private function __clone() { /* ... @return Singleton */ } // Защищаем от создания через клонирование
    private function __wakeup() { /* ... @return Singleton */} // Защищаем от создания через unserialize


    /**
     * @return array
     *
     * Хитрая ("ленивая") функция - берет данные наименее затратным образом
     * Если уже вызывалась, то вернут тоже самое что и впрошлый раз, т.к. заботливо сохраняет результат в поле класса.
     */
    public static function getBrands($resetCache = false,$arrFilter = array()){

        $cacheId = func_get_args();
        if (!empty($cacheId)) {
            array_unshift($cacheId, $resetCache);
            $cacheId = serialize($cacheId);
        }

        if(isset(self::$arData[__METHOD__][$cacheId]) && !$resetCache){
            return self::$arData[__METHOD__][$cacheId];
        }
        else{
            $obCache = new CPHPCache;
            if(!$resetCache && $obCache->InitCache(KDXSettings::getSetting('DEFAULT_LONG_CACHE_TIME')?:3600*24, 'simple_id'.$cacheId, 'kcache/kodix.sale/data_collector/brands')){
                $arResult  = $obCache->GetVars();
            }
            else{
                if(!CModule::IncludeModule('iblock')) die('iblock module');
                $arOrder = array(
                	'NAME' => 'ASC',
                	'SORT' => 'ASC'
                );
                $arFilter = array(
                	'IBLOCK_ID' => KDXSettings::getSetting('BRANDS_IBLOCK_ID')?:-1,
                	'ACTIVE' => 'Y',
                );
                if(is_array($arrFilter) && !empty($arrFilter)) {
                    $arFilter = array_merge($arFilter, $arrFilter);
                }
                $arSelect = array(
                	'ID',
                	'IBLOCK_ID',
                    'XML_ID',
                	'NAME',
                	'DETAIL_PAGE_URL',
                	'PREVIEW_PICTURE',
                    'DETAIL_PICTURE',
                    'PREVIEW_TEXT',
                    'DETAIL_TEXT',
                    'PROPERTY_SHOW_MENU',
                );
                prepareLangSelect($arSelect);
                $res = CIBlockElement::GetList($arOrder, $arFilter, false, false, $arSelect);
                while ($fields = $res->GetNext()) {
                    $arResult[$fields['ID']]=$fields;
                }

                if($obCache->StartDataCache()){
                    if(empty($arResult)){
                        $obCache->AbortDataCache();
                    }
                    $obCache->EndDataCache($arResult);
                }
            }
            self::$arData[__METHOD__]=$arResult;
            return $arResult;
        }
    }

    /**
     * @param bool|int|array $ID список id товаров, которые следует обновить
     */
    public static function setAvailableSizes($ID = false){
        if(function_exists('setAvailableSizes')){
            setAvailableSizes($ID);
        }
    }

    public static function cancelOrders()
    {
        if(function_exists('cancelOrders')){
            cancelOrders();
        }
    }
    public static function cancelPaypalOrders()
    {
        if(function_exists('cancelPaypalOrders')){
            cancelPaypalOrders();
        }
    }
}