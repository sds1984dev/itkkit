<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use \Bitrix\Main;


class CKodixCatalogRecommendedComponent extends CBitrixComponent
{
    protected $select;
    public function onPrepareComponentParams($arParams)
    {
        if(!isset($arParams["CACHE_TIME"]) || $arParams["CACHE_TIME"] <= -1)	$arParams["CACHE_TIME"] = 3600;
        return $arParams;
    }

    public function executeComponent()
    {
        CModule::IncludeModule("iblock");
        $this->prepareSelect();
        $this->arResult['ITEMS']=$this->getData();
        $this->IncludeComponentTemplate();
    }

    protected function prepareSelect(){
        $select=array(
            "ID",
            "IBLOCK_ID",
            "NAME",
            "CODE",
            "XML_ID",
            "PREVIEW_TEXT",
            "DETAIL_TEXT",
            "PREVIEW_PICTURE",
            "DETAIL_PICTURE",
            "PROPERTY_CML2_MANUFACTURER",
            "PROPERTY_CML2_MANUFACTURER.NAME",
            "PROPERTY_SIZES",
            "PROPERTY_SIZES_CLOTHING",
            "PROPERTY_SIZES_TRAINERS",
            "PROPERTY_SIZES_ACCESSORIES",
            "PROPERTY_COLOR",
            "PROPERTY_SCOLOR",
            "PROPERTY_GALLERY",
            "PROPERTY_GRID",
            "PROPERTY_CML2_ARTICLE",
            "PROPERTY_CODE",
            "PROPERTY_GROUP_SORT",
            "PROPERTY_SEARCH_NAME",
            "PROPERTY_SALE",
            "DETAIL_PAGE_URL",
            "CATALOG_GROUP_".KDXSettings::getSetting("BASE_PRICE_ID"),
            "CATALOG_GROUP_".KDXSettings::getSetting("RETAIL_PRICE_ID"),
            'PROPERTY_BADGE',
            'PROPERTY_RETAIL_PRICE_MIN',
            'PROPERTY_RETAIL_PRICE_MAX',
            'PROPERTY_BASE_PRICE_MIN',
            'PROPERTY_BASE_PRICE_MAX',
            'PROPERTY_NOT_SHOW_SCND_PHOTO'
        );
        prepareLangSelect($select);
        $this->select=$select;
    }


    protected function getData(){
        $result=array();
        $cnt=intval($this->arParams["ITEMS_COUNT"]);
        if(!$cnt)
            $cnt=20;

        $arFilter = array(
            "IBLOCK_ID" => $this->arParams["IBLOCK_ID"],
            "ID" => $this->arParams["ELEMENT_ID"]
        );
        $global_filter = getGlobalFilterForSite();
        $obCache = new CPHPCache();
        $cacheID = serialize(array($arFilter,KDXCurrency::$CurrentCurrency));
        $cachePath = 'kcache/'.SITE_ID.'/catalog/recommended';
        if (!empty($this->arParams["RR_QUERY"])) {
            $cachePath .= '/'.$this->arParams["RR_QUERY"];
            $cacheID .=$this->arParams["RR_PARAMS"];
        }
        /*if($obCache->InitCache($this->arParams["CACHE_TIME"],$cacheID,$cachePath)){
            $result = $obCache->GetVars();
        }else {*/
            $related_goods = RetailRocket::Query($this->arParams["RR_QUERY"], $this->arParams["RR_PARAMS"]);

            if (!empty($related_goods)) {
                $this->arResult["RR"] = true;
                $arFilter['ID']=$related_goods;
                if ($global_filter)
                    $arFilter = array_merge($arFilter, $global_filter);
                $res = CIBlockElement::GetList(Array('NAME'=>'ASC'), $arFilter, false, array('nTopCount' => $cnt), $this->select);
                while($ar_fields = $res->GetNext()) {
                    prepareLangFields($ar_fields);
                    $result[$ar_fields["ID"]] = $ar_fields;
                }
            } else if ($this->arParams['RR_QUERY']=='UpSellItemToItems'){
                $element = CIblockElement::GetList(array(), $arFilter, false, array("nTopCount" => 1), array("ID", "PROPERTY_RECOMENDED", "IBLOCK_SECTION_ID", "PROPERTY_CML2_MANUFACTURER"))->GetNext();
                if (!$element["ID"]) {
                    return false;
                }

                $ids = array($element["ID"]);
                if ($this->arParams['SORT']){
                    $arOrder = $this->arParams['SORT'];
                } else {
                    $arOrder = array("NAME" => "ASC");
                }
                if (count($element["PROPERTY_RECOMENDED_VALUE"])) {
                    $arFilter = array(
                        "IBLOCK_ID" => $this->arParams["IBLOCK_ID"],
                        "ID" => $element["PROPERTY_RECOMENDED_VALUE"]
                    );

                    if ($global_filter) {
                        $arFilter = array_merge($global_filter, $arFilter);
                    }
                    $res = CIblockElement::GetList($arOrder, $arFilter, false, array("nTopCount" => $cnt - count($result)), $this->select);
                    while ($rec = $res->GetNext()) {
                        prepareLangFields($rec);
                        $result[$rec["ID"]] = $rec;
                        $ids[] = $rec['ID'];
                    }
                }

                if (count($result) < $cnt) {
                    $arFilter = array(
                        "IBLOCK_ID" => $this->arParams["IBLOCK_ID"],
                        "PROPERTY_RECOMENDED" => $element["ID"],
                        "!ID" => $ids
                    );

                    if ($global_filter) {
                        $arFilter = array_merge($global_filter, $arFilter);
                    }
                    $res = CIblockElement::GetList($arOrder, $arFilter, false, array("nTopCount" => $cnt - count($result)), $this->select);
                    while ($rec = $res->GetNext()) {
                        prepareLangFields($rec);
                        $result[$rec["ID"]] = $rec;
                        $ids[] = $rec['ID'];
                    }
                }

                if (count($result) < $cnt && $element["IBLOCK_SECTION_ID"]) {
                    $arFilter = array(
                        "IBLOCK_ID" => $this->arParams["IBLOCK_ID"],
                        "SECTION_ID" => $element["IBLOCK_SECTION_ID"],
                        "!ID" => $ids
                    );

                    if ($global_filter) {
                        $arFilter = array_merge($global_filter, $arFilter);
                    }
                    $res = CIblockElement::GetList($arOrder, $arFilter, false, array("nTopCount" => $cnt - count($result)), $this->select);
                    while ($rec = $res->GetNext()) {
                        prepareLangFields($rec);
                        $result[$rec["ID"]] = $rec;
                        $ids[] = $rec['ID'];
                    }
                }

                if (count($result) < $cnt && $element["PROPERTY_CML2_MANUFACTURER_VALUE"]) {

                    $arFilter = array(
                        "IBLOCK_ID" => $this->arParams["IBLOCK_ID"],
                        "PROPERTY_CML2_MANUFACTURER" => $element["PROPERTY_CML2_MANUFACTURER_VALUE"],
                        "!ID" => $ids
                    );

                    if ($global_filter) {
                        $arFilter = array_merge($global_filter, $arFilter);
                    }
                    $res = CIblockElement::GetList($arOrder, $arFilter, false, array("nTopCount" => $cnt - count($result)), $this->select);
                    while ($rec = $res->GetNext()) {
                        prepareLangFields($rec);
                        $result[$rec["ID"]] = $rec;
                        $ids[] = $rec['ID'];
                    }
                }
            } else {
                return false;
            }

                foreach ($result as $id => &$p) {

                    $p["BASE_PRICE"] = $p["PROPERTY_BASE_PRICE_MIN_VALUE"];
                    $p["RETAIL_PRICE"] = $p["PROPERTY_RETAIL_PRICE_MIN_VALUE"];

                    $p["BASE_PRICE_CONVERTED_FORMATED"] = KdxCurrency::convertAndFormat($p['BASE_PRICE'], KDXCurrency::$CurrentCurrency);
                    $p["RETAIL_PRICE_CONVERTED_FORMATED"] = KdxCurrency::convertAndFormat($p['RETAIL_PRICE'], KDXCurrency::$CurrentCurrency);

                    $p['PROPERTY_SIZES_VALUE'] = array();
                    if(!empty($p['PROPERTY_SIZES_CLOTHING_VALUE']))
                        $p['PROPERTY_SIZES_VALUE'] = $p['PROPERTY_SIZES_CLOTHING_VALUE'];
                    elseif(!empty($p['PROPERTY_SIZES_TRAINERS_VALUE']))
                        $p['PROPERTY_SIZES_VALUE'] = $p['PROPERTY_SIZES_TRAINERS_VALUE'];
                    elseif(!empty($p['PROPERTY_SIZES_ACCESSORIES_VALUE']))
                        $p['PROPERTY_SIZES_VALUE'] = $p['PROPERTY_SIZES_ACCESSORIES_VALUE'];

                    usort($p['PROPERTY_SIZES_VALUE'],'KDXDataCollector::sortSizes');
                }

            if($obCache->StartDataCache()){
                $obCache->EndDataCache($result);
            }

        //}
        return $result;
    }
}
