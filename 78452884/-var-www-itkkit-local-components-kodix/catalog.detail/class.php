<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use \Bitrix\Main;
use \Bitrix\Catalog\CatalogViewedProductTable as CatalogViewedProductTable;


class CKodixCatalogDetailComponent extends CBitrixComponent
{
    public function onPrepareComponentParams($arParams)
    {
        if(!isset($arParams["CACHE_TIME"]) || $arParams["CACHE_TIME"] <= -1)	$arParams["CACHE_TIME"] = 3600;
        return $arParams;
    }

    public function executeComponent()
    {
        Main\Loader::includeModule("catalog");
        Main\Loader::includeModule("sale");
        global $APPLICATION;
        $APPLICATION->SetPageProperty("BODY_CLASS", "item");
        $this->arResult=$this->getData();
       
        if($this->arResult["ID"]){
            CatalogViewedProductTable::refresh($this->arResult["ID"], CSaleBasket::GetBasketUserID(true));
        }
        if($this->arResult["ID"] && $GLOBALS['USER']->IsAuthorized()){
            $this->logUsersIteraction($this->arResult["ID"]);
        }
        if($this->arResult["IBLOCK_SECTION_ID"]){
            $this->addSectionsChain($this->arResult["IBLOCK_SECTION_ID"]);
    }
        $this->IncludeComponentTemplate();
    }


    function logUsersIteraction($ID) {
        $currentUserId = $GLOBALS['USER']->GetID();


        $hlblock = \Bitrix\Highloadblock\HighloadBlockTable::getById(6)->fetch();
        $entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlblock);
        $entityDataClass = $entity->getDataClass();

        $result = $entityDataClass::getList(array(
            "select" => array("ID", "UF_UID", "UF_IID", "UF_ITERACTIONS"),
            "filter" => array("UF_UID"=>$currentUserId),
        ));

        $arSeenItems = [];

        while ($arRow = $result->Fetch()) {
            $arSeenItems[$arRow['UF_IID']] = ['ID' => $arRow['ID'], 'ITERACTIONS' => $arRow['UF_ITERACTIONS']];
        }
        if(isset($arSeenItems[$ID])){
            $arFields = array (
                'UF_UID' => $currentUserId,
                'UF_IID' => $ID,
                'UF_ITERACTIONS' => $arSeenItems[$ID]['ITERACTIONS'] + 1
            );
            $entityDataClass::update($arSeenItems[$ID]['ID'],$arFields);
        } else {
            $arFields = array (
                'UF_UID' => $currentUserId,
                'UF_IID' => $ID,
                'UF_ITERACTIONS' => 1
            );
            $entityDataClass::add($arFields);
        }
    }

    function addSectionsChain($sID)
    {
        $arSection = KDXDataCollector::getSection($sID);

        if($arSection['IBLOCK_SECTION_ID'])
            $this->addSectionsChain($arSection['IBLOCK_SECTION_ID']);

        global $APPLICATION;

        if(LANGUAGE_ID == 'en')
            $arSection['NAME'] = $arSection['UF_EN_NAME']?$arSection['UF_EN_NAME']:$arSection['NAME'];


        $APPLICATION->AddChainItem($arSection['NAME'],$arSection['SECTION_PAGE_URL']);
    }

    protected function getData(){

        global $APPLICATION;
        $groups = CUser::GetUserGroup(CUser::GetId());
        $cache = new CPHPCache();
        $cacheTime = $this->arParams["CACHE_TIME"];
        $cachePath = "catalog/detail";
        $cacheId = $this->arParams["CODE"].$this->arParams["SECTION_CODE"].serialize($groups);
        if (($cacheTime > 0 && $cache->InitCache($cacheTime, $cacheId, $cachePath)) and (0>1)){
            $result = $cache->GetVars();
        } else {
            $result=array();
            $arFilter = array(
                "IBLOCK_ID" => $this->arParams["IBLOCK_ID"],
                "CODE" => $this->arParams["CODE"],
                "=ID" => $this->arParams["ELEMENT_ID"],
                'ACTIVE' => 'Y'
            );

            /*if($global_filter = KDXSettings::getSetting('GLOBAL_SALE_FILTER')){
                $arFilter = array_merge($global_filter,$arFilter);
            }*/

            $item_el=CIblockElement::GetList(array(), $arFilter)->GetNextElement();
            if($item_el){
                $fields=$item_el->GetFields();
                $props=$item_el->GetProperties();
                $result=$fields;
                foreach($props as $p){
                    if($p["CODE"]=="GALLERY"){
    //                    $result["GALLERY"]=KDXGallery::sortGallery($p["VALUE"], $p["DESCRIPTION"]);
                        $result["GALLERY"] = $p["VALUE"];
                        continue;
                    }
                    elseif($p['CODE'] == 'CANT_ORDER'){
                        $result['CANT_ORDER'] = (intval($p['VALUE_ENUM_ID'])) ?true:false;
                        continue;
                    }
                    elseif($p['CODE'] == 'CML2_MANUFACTURER')
                    {
                        $arBrands = KDXSaleDataCollector::getBrands();
                        foreach ($p['VALUE'] as $key => $brandId){
                            $p["VALUE"][$key] = $arBrands[ $brandId ];
                        }
                    }
                    if(!$p["VALUE"])
                        $p["VALUE"]=$p["~VALUE"];
                    $result[$p["CODE"]]=$p["VALUE"];
                    $result['PROPERTY_'.$p["CODE"].'_VALUE']=$p["VALUE"];
                    prepareLangFields($result);
                }

                //узнаем цену товара
                $result["BASE_PRICE"]=GetCatalogProductPrice(
                    $fields["ID"],
                    KDXSettings::getSetting("BASE_PRICE_ID")
                );
                $result["RETAIL_PRICE"]=GetCatalogProductPrice(
                    $fields["ID"],
                    KDXSettings::getSetting("RETAIL_PRICE_ID")
                );

                //собираем размеры - ДОЛГО это ищется! в правку!
//                echo '<pre>';
//                var_dump ($result["BASE_PRICE"]);
//                echo '</pre>';
//                die();
                $res=[];
                $res=CIblockElement::GetList(
                        array(), 
                        array(
                    "IBLOCK_ID"=>$this->arParams["SKU_IBLOCK_ID"],
                    "PROPERTY_CML2_LINK"=>$fields["ID"],
                    'ACTIVE' => 'Y',
     //               'ACTIVE_DATE' => 'Y',
                    '>CATALOG_QUANTITY' => 0,
                    ">CATALOG_PRICE_".KDXSettings::getSetting("RETAIL_PRICE_ID") => 0,
                ), 
                        false, 
                        false, 
                        array(
                    "ID",
                    //"NAME",
                    //"PROPERTY_CML2_LINK",
                    "PROPERTY_SIZE",
                    "PROPERTY_SIZE_EU",
                    //"PROPERTY_CML2_ARTICLE",
                    //"CATALOG_QUANTITY",
                    "CATALOG_GROUP_".KDXSettings::getSetting("BASE_PRICE_ID"),
                    "CATALOG_GROUP_".KDXSettings::getSetting("RETAIL_PRICE_ID"),
                    ));
//              
                $result["HAVE_REAL_SIZES"] = false;
                
                while($size=$res->GetNext()){
//                        echo '<pre>';
//                        print_r ($size);
//                        echo '</pre>';
//                        die();
                    $test[]=$size['PROPERTY_SIZE_VALUE'];

                    $result["HAVE_REAL_SIZES"] = true;
                    //if($size['CATALOG_QUANTITY'] > 0)
                       

                    $size['RETAIL_PRICE'] = $size['CATALOG_PRICE_'.KDXSettings::getSetting('RETAIL_PRICE_ID')];
                    $size['BASE_PRICE'] = $size['CATALOG_PRICE_'.KDXSettings::getSetting('BASE_PRICE_ID')];

                    $result["REAL_SIZES"][$size["PROPERTY_SIZE_VALUE"]]=$size;
                }
//                    echo '<pre>';
//                    print_r ($test);
//                    echo '</pre>';
//                    die();
                
                usort($result["REAL_SIZES"],'KDXDataCollector::sortSizes');
                

                $result["SIZE_GRID"]=$this->getSizeGrid($result);

                $criteoView = array('event'=>'viewItem', 'item'=>$fields['ID']);
                $APPLICATION->SetPageProperty("CRITEOVIEW", ','.json_encode($criteoView));

                $arResultName = explode(' ', $result['NAME']);
                $result['DESCRIPTION_NAME'] = $result['NAME'];
                foreach($arResultName as $key=>$val)
                {
                    preg_match('/.*а$/u', $val, $resultName);
                    $reName = preg_replace('/а$/', 'у', $resultName);

                    if($reName[0])
                    $result['DESCRIPTION_NAME'] = str_replace($val, $reName[0], $result['NAME']);
                }
                
                $description = GetMessage('DESCRIPTION_TEMP', array(
                    '#брэнд#' => $result['CML2_MANUFACTURER']['NAME'],
                    '#название#' => $result['DESCRIPTION_NAME'],
                    '#цена#' => $result['BASE_PRICE_MIN'],
                    '#валюта#' => KDXCurrency::$CurrentCurrency
                ));

                $APPLICATION->SetPageProperty("description", $description);

                if($this->arResult['PREVIEW_TEXT'])
                    $APPLICATION->SetPageProperty("description", $this->arResult['PREVIEW_TEXT']);

                if(LANGUAGE_ID == 'en' && $this->arResult['PREVIEW_TEXT'])
                    $APPLICATION->SetPageProperty("description", $this->arResult['EN_PREVIEW_TEXT']);
            }
            if ($cache->StartDataCache($cacheTime, $cacheId, $cachePath)){
                $cache->EndDataCache($result);
            }
        }

        if (empty($result["ID"])){
             show404();
        }
        
        return $result;
    }

    protected function getSizeGrid($data){
        if(intval($data["GRID"]))
            return $data["GRID"];

        // Поиск подходящей сетки размеров
        $sort   = array('ID' => 'DESC');
        $filter = array(
            'IBLOCK_ID' => $this->arParams["GRIDS_IBLOCK_ID"],
            'ACTIVE'    => 'Y'
        );
        // навигация
        $pagination = array(
            'nTopCount' => 1
        );
        // селект
        $select = array('ID', 'NAME');
        $grid=false;
        if (!$grid && $data['ID']) {
            // получаем размерную сетку по продукту
            $Grids = CIBlockElement::GetList($sort, array_merge($filter, array(
                'PROPERTY_PRODUCT' => $data['ID']
            )), false, $pagination, $select);
            $grid = $Grids->GetNext();
            $grid=$grid["ID"];
            unset($Grids);
        }
        if (!$grid) {
            if($data['IBLOCK_SECTION_ID']) {
                // Получим родителей секции
                $arSectionID=array();
                $sections=get_sections_chain_items($data['IBLOCK_SECTION_ID'], $this->arParams["CATALOG_IBLOCK_ID"]);
                foreach($sections as $s){
                    $arSectionID[]=$s["ID"];
                }
                // По разделу и бренду
                if($data['CML2_MANUFACTURER'][0]) {
                    $Grids = CIBlockElement::GetList($sort, array_merge($filter, array(
//                        'PROPERTY_SECTION' => $arSectionID,
                        'PROPERTY_BRAND' => $data['PROPERTY_CML2_MANUFACTURER_VALUE'][0]
                    )), false, $pagination, $select);
                    $grid = $Grids->GetNext();
                    $grid=$grid["ID"];
                    unset($Grids);
                }
                // Только по разделу
                if(!$grid) {
                    $Grids = CIBlockElement::GetList($sort, array_merge($filter, array(
                        'PROPERTY_SECTION' => $arSectionID
                    )), false, $pagination, $select);
                    $grid = $Grids->GetNext();
                    $grid=$grid["ID"];
                    unset($Grids);
                }
            }
        }

        if(intval($grid))
        {
            $res = CIBlockElement::GetByID($grid);

            $obGrid = $res->GetNextElement();
            $arFields = $obGrid->GetFields();
            $arFields['IMAGE_ID'] = $obGrid->GetProperty('IMAGE_ID');
            $arFields['SIZES_VALUES'] = $obGrid->GetProperty('SIZES_VALUES');
            $arFields['SIZES_VARIANTS'] = $obGrid->GetProperty('SIZES_VARIANTS');
            $arFields['PROPERTY_EN_DETAIL_TEXT_VALUE'] = $obGrid->GetProperty('EN_DETAIL_TEXT')['~VALUE']['TEXT'];
            prepareLangFields($arFields);

            $db_prop = CIBlockPropertyEnum::GetList(array("id"=>"ASC"), array("IBLOCK_ID"=>KDXSettings::getSetting('SIZE_GRID_IBLOCK_ID'), "CODE"=>"SIZES_VARIANTS"));
            while($ar_prop = $db_prop->Fetch()) {
                if(in_array($ar_prop['VALUE'],$arFields['SIZES_VARIANTS']['VALUE'])) {
                    $key = str_replace ( 'key_' , '', $ar_prop['XML_ID']);
                    $arFields['SIZES_VARIANTS']['SORT_VALUE'][$key] = $ar_prop['VALUE'];

                }
            }

            if ($arFields['IMAGE_ID']['VALUE_XML_ID']) {
                $arFields['IMAGE_ID']['CUSTOM_IMG'] = null;
                $HLClass = Bitrix\Highloadblock\HighloadBlockTable::getList(array('filter' => array('TABLE_NAME' => KDXSettings::getSetting('NEW_SIZE_GRID_PICTURES'))))->fetch();
                if ($HLClass) {
                    $HLClass = Bitrix\Highloadblock\HighloadBlockTable::compileEntity($HLClass);
                    $HLClass = $HLClass->getDataClass();

                    if ($value = $HLClass::getList(array('filter' => array('UF_XML_ID' => $arFields['IMAGE_ID']['VALUE_XML_ID'])))->fetch()) {
                        if(!empty($value['UF_PICTURE'])){
                            $arFields['IMAGE_ID']['CUSTOM_IMG'] = CFile::GetPath($value['UF_PICTURE']);
                        }
                    }
                }
            }

            $arFields['GRID_PIC'] = $obGrid->GetProperty('GRID_PIC');
            if($arFields['GRID_PIC']['USER_TYPE'] == 'directory')
            {
                $HLClass = Bitrix\Highloadblock\HighloadBlockTable::getList(array('filter' => array('TABLE_NAME' => $arFields['GRID_PIC']['USER_TYPE_SETTINGS']['TABLE_NAME'])))->fetch();
                $HLClass = Bitrix\Highloadblock\HighloadBlockTable::compileEntity($HLClass);
                $HLClass = $HLClass->getDataClass();

                if ($value = $HLClass::getList(array('filter' => array('UF_XML_ID' => $arFields['GRID_PIC']['VALUE'])))->fetch()) {

                    if(!empty($value['UF_FILE']))
                        $arFields['GRID_PIC'] = CFile::GetFileArray($value['UF_FILE']);
                    else
                        $arFields['GRID_PIC'] = false;
                }
            }
            $arr_sizes = array();

            foreach($arFields['SIZES_VALUES']['DESCRIPTION'] as $k => $size_value) {
                $i = 0;
                foreach($arFields['SIZES_VARIANTS']['SORT_VALUE'] as $size_variants) {
                    $size_nums = explode(',', $arFields['SIZES_VALUES']['VALUE'][$k]);

                    if($size_variants == 'pit to pit')
                        $size_variants = 'ptp';

                    $arr_sizes[$size_value][$size_variants]['cm'] = $size_nums[$i];
                    $arr_sizes[$size_value][$size_variants]['inch'] = intval(round($size_nums[$i]/2.54));
                    $i++;
                }
            }

            $arFields["ARR_SIZE_GRID_VALUES"] = $arr_sizes;

            return $arFields;
        }

        return false;

    }
}
