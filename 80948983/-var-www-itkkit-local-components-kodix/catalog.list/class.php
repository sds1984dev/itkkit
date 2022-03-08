<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

class CKodixCatalogListComponent extends CBitrixComponent
{
    protected $filter;
    protected $nav;
    protected $sort;
    protected $select;
    protected $needCache = true;
    public function onPrepareComponentParams($arParams)
    {
        if(!isset($arParams["CACHE_TIME"]) || $arParams["CACHE_TIME"] <= -1)	$arParams["CACHE_TIME"] = 3600;
        $arParams["PAGE_ITEMS_COUNT"] = $_REQUEST['SIZEN']?:(intval($arParams["PAGE_ITEMS_COUNT"]));
        if($this->getParent()){
            if(!$arParams["PAGINATION_TEMPLATE"]){
                $arParams["PAGINATION_TEMPLATE"]=$this->getParent()->arParams["PAGINATION_TEMPLATE"];
            }
            if(!$arParams["PAGINATION_WRAPPER"]){
                $arParams["PAGINATION_WRAPPER"]=$this->getParent()->arParams["PAGINATION_WRAPPER"];
            }
        }
        $arParams['LIMIT_ITEMS']=intval($arParams['LIMIT_ITEMS']);
        return $arParams;
    }

    function addSectionsChain($sID)
    {
        $arSection = KDXDataCollector::getSection($sID);

        if($arSection['IBLOCK_SECTION_ID'])
            $this->addSectionsChain($arSection['IBLOCK_SECTION_ID']);

        global $APPLICATION;

        if(LANGUAGE_ID == 'en')
            $arSection['NAME'] = $arSection['UF_EN_NAME']?$arSection['UF_EN_NAME']:$arSection['NAME'];

        if(LANGUAGE_ID == 'en')
            $arSection['DESCRIPTION'] = $arSection['UF_DESCRIPTION_EN'];

        if(!empty($this->arParams['BRANDS_FILTER']))
        {
            $this->getParent()->makeURL($arSection,'brand');
        }
        elseif($this->arParams['JUST_NEW'] == 'Y')
        {
            $this->getParent()->makeURL($arSection,'sale');
        }
        elseif($this->arParams['SALE'] == 'Y')
        {
            $this->getParent()->makeURL($arSection,'sale');
        }
        elseif($this->arParams['GIFT'] == 'Y')
        {
            $this->getParent()->makeURL($arSection,'gift');
        }

        if(empty($this->arParams['COLORS_FILTER'])){
            $APPLICATION->AddChainItem($arSection['NAME'],$arSection['SECTION_PAGE_URL']);
        }
    }

    public function executeComponent()
    {
        CPageOption::SetOptionString("main", "nav_page_in_session", "N");
        global $APPLICATION;
        $APPLICATION->SetPageProperty("BODY_CLASS", "catalog");

        if($APPLICATION->getProperty("SHOW_BANNER", "N") == "Y" && $this->arParams['NOT_SHOW_ACTION'] != "Y")
        {
            ob_start();
            $APPLICATION->IncludeComponent("kodix:promotional.offer", "", array(
                "IBLOCK_ID" => "17",
                "HIDE_TIME" => KDXSettings::getSetting('PROMO_HIDE_TIME'),
                //"ELEMENT_ID"=>"110632",
            ), false);
            $APPLICATION->AddViewContent('ACTION_DESCR',ob_get_clean());
        }

        $this->prepareFilter();
        $this->prepareNav();
        $this->prepareSort();
        $this->prepareSelect();
        $this->prepareResult();
        $this->preparePagination();

        $title = '';

        if (isset($this->arParams['SELECTION_CODE'])) {
            $selection = $this->GetSelectionData($this->arParams['SELECTION_CODE']);

            $this->arResult['SELECTION_DATA'] = $selection;

            $title = $selection['NAME'];
            $this->arResult['DETAIL_TEXT'] = $selection['DETAIL_TEXT'];
            $this->arResult['SELECTION_ID'] = $selection['SELECTION_ID'];
            $this->arResult['SELECTION_EDIT_LINK'] = $selection['SELECTION_EDIT_LINK'];

            $APPLICATION->AddChainItem($title);
        } elseif($this->arParams['FROM_SEARCH'] == 'Y') {
            $title = GetMessage('SEARCH_TITLE');
            $APPLICATION->AddChainItem(GetMessage('SEARCH_TITLE'));
        } elseif($this->arParams['JUST_NEW'] == 'Y') {
            $title = GetMessage('NEW_TITLE');
        } elseif($this->arParams['SALE'] == 'Y') {
            $title = GetMessage('SALE_TITLE');
        } elseif($this->arParams['GIFT'] == 'Y') {
            $title = GetMessage('GIFT_TITLE');
        } elseif(!empty($this->arParams['BRANDS_FILTER']) && empty($this->arParams['COLORS_FILTER'])) {
            $arBrands = KDXSaleDataCollector::getBrands();
            $arBrand = $arBrands[ $this->arParams['BRANDS_FILTER'] ];
            prepareLangFields($arBrand);
            $title = GetMessage('BRAND_CHAIN',array('BRAND' => $arBrand['NAME']));

            $APPLICATION->SetPageProperty('description',$arBrand['DETAIL_TEXT']);


            if ((int)$_GET['PAGEN_1'] < 2 || empty($_GET['PAGEN_1'])) {

                $page = $APPLICATION->GetCurPage(false);
                if (file_exists(__DIR__ . '/seo.csv')) {
                    $file = fopen(__DIR__ . '/seo.csv', 'r');
                    $data = array();
                    while (($buffer = fgets($file, 4096)) !== false) {
                        $line = explode('|', $buffer);
                        $data[trim($line[0])] = trim($line[1]);
                    }
                    fclose($file);

                    if ($this->arParams['BRANDS_INCLUDE_AREA'] == 'Y'){
                        if (array_key_exists($page, $data) && SITE_SERVER_NAME == 'www.itkkit.com') {
                            $APPLICATION->AddViewContent('SECTION_DESCR', '<div class="catalog-section__brand-text">' . $data[$page] . '</div>');
                        } else {
                            if (strripos($page, '/catalog/brand/') !== false) {
                                if (mobileDetect()){
                                    $maxLen = LANGUAGE_ID == 'en' ? 120 : 90;
                                } else {
                                    $maxLen = LANGUAGE_ID == 'en' ? 600 : 520;
                                }
                                $brandText = htmlspecialchars_decode($arBrand['DETAIL_TEXT']);
                                $brandText = strip_tags($brandText);
                                $textLength = mb_strlen(str_replace(PHP_EOL, ' ', $brandText));
                                if ($textLength > $maxLen){
                                    $brandText = htmlspecialchars_decode($arBrand['DETAIL_TEXT']);
                                    $cutBrandText = cutBrandText($brandText, $maxLen);
                                    $APPLICATION->AddViewContent('SECTION_DESCR', '<div class="catalog-section__brand-text">'.$cutBrandText.'</div>');
                                } else {
                                    $APPLICATION->AddViewContent('SECTION_DESCR', '<div class="catalog-section__brand-text">' . htmlspecialchars_decode($arBrand['DETAIL_TEXT']) . '</div>');
                                }
                            }
                        }
                    }
                }



            }
            if(!$arBrand['DETAIL_TEXT'])
            {
                $url = $this->getParent()->arParams['SEF_FOLDER'] . CComponentEngine::makePathFromTemplate($this->getParent()->arParams['SEF_URL_TEMPLATES']['brand'],array('BRAND' => $arBrand['CODE']));
                $APPLICATION->AddChainItem(GetMessage('BRAND_CHAIN',array('BRAND' => $arBrand['NAME'])),$url);

                if(empty($arSection['DESCRIPTION']))
                {
                    $arSection['DESCRIPTION'] = GetMessage('DESCRIPTION_TEMP_BRAND', array(
                        '#брэнд#' => $arBrand['NAME']
                    ));

                    $this->arResult['DESCRIPTION_NOT_VIEW'] = 'Y';
                    $APPLICATION->SetPageProperty("description", $arSection['DESCRIPTION']);
                }
            }
        } elseif($this->arParams["SECTION_ID"]) {
            $this->addSectionsChain($this->arParams["SECTION_ID"]);

            $arSection = KDXDataCollector::getSection($this->arParams["SECTION_ID"]);
            if(LANGUAGE_ID == 'en')
                $arSection['NAME'] = $arSection['UF_EN_NAME']?$arSection['UF_EN_NAME']:$arSection['NAME'];

            if(LANGUAGE_ID == 'en')
                $arSection['DESCRIPTION'] = $arSection['UF_DESCRIPTION_EN'];

            if(!empty($arSection['NAME']))
            {
                $title = !empty($title) ? $title.' - '.$arSection['NAME'] : $arSection['NAME'];
            }
        }

        /*global $USER;
        if ($USER->isAdmin()){*/
            $resultTags = '';
            $curPage = explode('?', $_SERVER['REQUEST_URI'], 2)[0];
            $arTags = [];
            $resTagSectionId = CIBlockSection::GetList(array(), array('IBLOCK_ID'=>25, 'ACTIVE'=>'Y', 'NAME'=>$curPage), false, array('ID'))->Fetch()['ID'];
            if ($resTagSectionId > 0){
                $resTags = CIBlockElement::GetList(array('sort'=>'asc'), array('IBLOCK_ID'=>25, '=SECTION_ID'=>$resTagSectionId, 'ACTIVE'=>'Y', '=PROPERTY_SITE_VALUE'=>LANGUAGE_ID), false, false, array('ID', 'IBLOCK_ID', 'NAME', 'PROPERTY_SHOW_TABLET', 'PROPERTY_SHOW_MOBILE', 'PROPERTY_LINK', 'PROPERTY_PERCENT'));
                while ($row = $resTags->Fetch()){
                    $arTags[] = $row;
                }
            }
            if (!empty($arTags)){
                $resultTags .= '<div class="catalog-section__tags">';
                foreach ($arTags as $tag){
                    if (!empty($tag['PROPERTY_PERCENT_VALUE'])){
                        if (isset($_GET['percent']) && $_GET['percent'] == $tag['PROPERTY_PERCENT_VALUE']){
                            $resultTags .= '<p class="'.($tag['PROPERTY_SHOW_TABLET_VALUE'] == 'Да' ? '_show-tablet' : '').($tag['PROPERTY_SHOW_MOBILE_VALUE'] == 'Да' ? ' _show-mobile' : '').'">'.$tag['NAME'].'</p>';
                        } else {
                            $resultTags .= '<a class="'.($tag['PROPERTY_SHOW_TABLET_VALUE'] == 'Да' ? '_show-tablet' : '').($tag['PROPERTY_SHOW_MOBILE_VALUE'] == 'Да' ? ' _show-mobile' : '').'" href="'.$curPage.'?percent='.$tag['PROPERTY_PERCENT_VALUE'].'">'.$tag['NAME'].'</a>';
                        }
                    } else {
                        $tagUrl = $tag['PROPERTY_LINK_VALUE'];
                        parse_str(parse_url($tagUrl, PHP_URL_QUERY), $arTagUrl);
                        if ($_GET['FILTER'] == $arTagUrl['FILTER']){
                            $resultTags .= '<p class="'.($tag['PROPERTY_SHOW_TABLET_VALUE'] == 'Да' ? '_show-tablet' : '').($tag['PROPERTY_SHOW_MOBILE_VALUE'] == 'Да' ? ' _show-mobile' : '').'">'.$tag['NAME'].'</p>';
                        } else {
                            $resultTags .= '<a class="'.($tag['PROPERTY_SHOW_TABLET_VALUE'] == 'Да' ? '_show-tablet' : '').($tag['PROPERTY_SHOW_MOBILE_VALUE'] == 'Да' ? ' _show-mobile' : '').'" href="'.$tag['PROPERTY_LINK_VALUE'].'">'.$tag['NAME'].'</a>';
                        }
                    }
                }
                $resultTags .= '</div>';
            }
            $APPLICATION->AddViewContent('SECTION_DESCR', '<div class="catalog-section__brand-text">' . $resultTags . '</div>');
        //}

        if(!empty($title)){
            $APPLICATION->SetTitle($title);
        }

        $this->IncludeComponentTemplate();
    }

    protected function prepareFilter(){
        $filter=array();
        global $DB;

        foreach($this->arParams as $code=>$value){
            switch ($code){
                case "IBLOCK_TYPE":
                    $filter["IBLOCK_TYPE"]=$value;
                    break;
                case "CATALOG_IBLOCK_ID":
                    $filter["IBLOCK_ID"]=intval($value);
                    break;
                case "JUST_NEW":
                    //if($value=="Y") $filter["PROPERTY_BADGE_VALUE"]='New';
                    break;
                case "SALE":
                    if($value=="Y")
                        $filter["PROPERTY_BADGE_VALUE"]='Sale';
                    break;
                case "GIFT":
                    if($value="Y")
                        $filter['PROPERTY_BADGE_VALUE'] = 'GIFT';
                    break;
                case "BRANDS_FILTER":
                        $filter["PROPERTY_CML2_MANUFACTURER"]= array($value);
                    break;
                case "COLORS_FILTER":
                        $filter["PROPERTY_COLORS"]= $value;
                    break;
                case "SECTION_ID":
                    if(trim($value))
                        $filter["SECTION_ID"]=$value;
                    break;
                /*case "SELECTION_CODE":
                    $filter['ID'] = $this->GetProductsBySelectionCode($value);
                    break;*/
            }
        }
        $filter["INCLUDE_SUBSECTIONS"]="Y";

        if($global_filter = getGlobalFilterForSite()){
            $filter = array_merge($global_filter,$filter);
        }

        if(is_array($this->arParams['FILTER']) && !empty($this->arParams['FILTER'])){
            $filter = array_merge($filter, $this->arParams['FILTER']);
            if($this->arParams['STABLE_FILTER'] != 'Y')
                $this->needCache=false;
        }

        if (isset($_GET['percent']) && $_GET['percent'] !== ''){
            $filter['=PROPERTY_PERCENT'] = $_GET['percent'];
        }

        /**
         * MAGIC! Копия родительского компонента в данном случае доступена только из дочерних компонентов.
         * в catalog.filter устанавливается CHILDREN_COMPONENT_BUFFER
         */
        if(!is_null($this->getParent())){
            $UserFilter = $this->getParent()->arResult['CHILDREN_COMPONENT_BUFFER']['FILTER_ARRAY'];
            if(is_array($UserFilter) && !empty($UserFilter)){
                $filter=array_merge($filter,$UserFilter);
                $this->needCache=false;
            }
            elseif(intval($_GET['FILTER'])){
                if(!CModule::IncludeModule('kodix.sale')) die('kodix.sale module');
                $res = Kodix\Sale\Filter\FilterTable::getById(intval($_GET['FILTER']));
                if($fields = $res->fetch()){
                    $UserFilter=unserialize($fields['FILTER']);
                    if(is_array($UserFilter) && !empty($UserFilter)){
                        $filter=array_merge($filter,$UserFilter);
                        $this->needCache=false;
                    }
                }
            }

            $UserFilterID= $this->getParent()->arResult['CHILDREN_COMPONENT_BUFFER']['FILTER'];
            if(intval($UserFilterID)){
                $_GET['FILTER'] = intval($UserFilterID);
            }
        }

        if($this->arParams["SELECTION_CODE"]) {
            $filter['ID'] = $this->GetProductsBySelectionCode($this->arParams["SELECTION_CODE"]);
        }
        $this->filter=$filter;
    }

    protected function prepareNav(){
        $this->nav=array();
        if($this->arParams["PAGE_ITEMS_COUNT"]){
            $this->nav["nPageSize"]=$this->arParams["PAGE_ITEMS_COUNT"];
        }
        if($this->arParams["LIMIT_ITEMS"]){
            $this->nav["nTopCount"]=$this->arParams["LIMIT_ITEMS"];
        }
        if(empty($this->nav))
            $this->nav=false;
    }

    protected function prepareSort(){
        if($_GET["SORT"]){
            $this->sort=array($_GET["SORT"]=>$_GET["ORDER"]);
            $this->needCache=false;
        }else{
            $this->sort=array_filter(array(
                $this->arParams["SORT_BY1"]=>$this->arParams["SORT_ORDER1"],
                $this->arParams["SORT_BY2"]=>$this->arParams["SORT_ORDER2"]
            ));
        }
        if(empty($this->sort))
            $this->sort=array("SORT"=>"ASC");
    }

    protected function prepareSelect(){
        $select=array(
            "ID",
            "IBLOCK_ID",
            "NAME",
            "PROPERTY_EN_NAME",
            "CODE",
            "XML_ID",
            "PREVIEW_TEXT",
            "PROPERTY_EN_PREVIEW_TEXT",
            "DETAIL_TEXT",
            "PROPERTY_EN_DETAIL_TEXT",
            "PREVIEW_PICTURE",
            "DETAIL_PICTURE",
            "PROPERTY_CML2_MANUFACTURER",
            "PROPERTY_SIZES_CLOTHING",
            "PROPERTY_SIZES_TRAINERS",
            "PROPERTY_SIZES_TRAINERS_EU",
            "PROPERTY_SIZES_ACCESSORIES",
            "PROPERTY_COLOR",
            "PROPERTY_GALLERY",
            "PROPERTY_GRID",
            "PROPERTY_CML2_ARTICLE",
            "PROPERTY_CODE",
            "PROPERTY_GROUP_SORT",
            "PROPERTY_SEARCH_NAME",
            "PROPERTY_SALE",
            "PROPERTY_SELECTION",
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
        $this->select=$select;
    }

    /**
     * подготавливаем массив для сортировки
     */
    protected function prepareSortVariant(){
        /**
         * подготавливаем наименование для свойств
         */

        $arNames=array();
        if(!CModule::IncludeModule('catalog')) die('catalog module');
        $dbResultList = CCatalogGroup::GetList(
            array('SORT'=>'ASC'),
            array('CAN_ACCESS'=>'Y','CAN_BUY'=>'Y',)
        );
        while($fields = $dbResultList ->Fetch()){
            $arNames["CATALOG_PRICE_".$fields['ID']]=$fields['NAME_LANG'];
        }
        $arNames["ACTIVE_FROM"]=GetMessage("KDX_SORT_BY_ACTIVE_FROM");
        if(intval($this->arParams["CATALOG_IBLOCK_ID"])){
            $res = CIBlockProperty::GetList(array('SORT'=>'ASC'),array('ACTIVE'=>'Y','IBLOCK_ID'=>$this->arParams["CATALOG_IBLOCK_ID"]));
            while($fields = $res->Fetch()){
                $arNames['PROPERTY_'.$fields['CODE']]=$fields['NAME'];
            }
        }

        $this->arResult['SORT']=array('SORT'=>'Выбрать');
        foreach($this->arParams['SORT_VARIANTS'] as $sort_variant){
            $this->arResult['SORT'][$sort_variant]=$arNames[$sort_variant];
        }
    }



    protected function prepareResult(){

        if($this->arParams['FROM_SEARCH'] == 'Y' && empty($this->arParams['SEARCH']))
            return;

        global $APPLICATION;
        //$APPLICATION->SetTitle($section["NAME"]);
        if((intval($this->filter['SECTION_ID']) || is_array($this->filter['SECTION_ID']))){
            $section_id = is_array($this->filter['SECTION_ID'])?reset($this->filter['SECTION_ID']):$this->filter['SECTION_ID'];
            $ipropValues = new \Bitrix\Iblock\InheritedProperty\SectionValues($this->arParams["CATALOG_IBLOCK_ID"], $section_id);
            $meta = $ipropValues->getValues();

//            $APPLICATION->SetTitle($meta["SECTION_META_TITLE"]);
            if($meta["SECTION_META_KEYWORDS"])
                $APPLICATION->SetPageProperty("keywords", $meta["SECTION_META_KEYWORDS"]);

            $arSection = KDXDataCollector::getSection($this->arParams["SECTION_ID"]);

            $meta["SECTION_META_DESCRIPTION"] = $arSection['DESCRIPTION'];

            if(LANGUAGE_ID == 'en') $meta["SECTION_META_DESCRIPTION"] = $arSection['UF_DESCRIPTION_EN'];

            if(!$meta["SECTION_META_DESCRIPTION"])
            {
                if(LANGUAGE_ID == 'en')
                    $arSection['NAME'] = $arSection['UF_EN_NAME']?$arSection['UF_EN_NAME']:$arSection['NAME'];

                $this->arResult['DESCRIPTION_NOT_VIEW'] = 'Y';
                $meta["SECTION_META_DESCRIPTION"] = GetMessage('DESCRIPTION_TEMP_CATALOG', array(
                    '#категория#' => $arSection['NAME']
                ));
            }

            if($meta["SECTION_META_DESCRIPTION"])
            {
                $APPLICATION->SetPageProperty("description", $meta["SECTION_META_DESCRIPTION"]);
            }
            if (((int)$_GET['PAGEN_1'] < 2 || empty($_GET['PAGEN_1'])) && !empty($meta["SECTION_META_DESCRIPTION"])) {
                $APPLICATION->AddViewContent('SECTION_DESCR','<div class="catalog-section__brand-text">'.$meta["SECTION_META_DESCRIPTION"].'</div>');
            }

        }


        $items=array();
        $brand_xml_ids=array();
        $obCache = new CPHPCache();
        $nav_params = CDBResult::GetNavParams($this->nav);
        $cachceId= serialize(array($this->sort, $this->filter, $this->nav, $this->select,$nav_params,KDXCurrency::$CurrentCurrency,LANGUAGE_ID));
        $cachePath = 'kcache/catalog/list';
        if($this->needCache && $obCache->InitCache($this->arParams["CACHE_TIME"],$cachceId, $cachePath)){
            $this->arResult = $obCache->GetVars();
        } else {
            $res=CIblockElement::GetList($this->sort, $this->filter, false, $this->nav, $this->select);
            $res->NavStart($nav_params['SIZEN'], $nav_params['SHOW_ALL'], $nav_params['PAGEN']);
            $this->arResult['RES'] = $res;
            while($item=$res->GetNext()){
                $brand_xml_ids[]=$item["PROPERTY_CML2_MANUFACTURER_VALUE"];
                $item["BASE_PRICE"]=$item["PROPERTY_BASE_PRICE_MIN_VALUE"];
                $item["RETAIL_PRICE"]=$item["PROPERTY_RETAIL_PRICE_MIN_VALUE"];

                $item["BASE_PRICE_CONVERTED_FORMATTED"]=KdxCurrency::convertAndFormat($item['BASE_PRICE'],KDXCurrency::$CurrentCurrency);
                $item["RETAIL_PRICE_CONVERTED_FORMATTED"]=KdxCurrency::convertAndFormat($item['RETAIL_PRICE'],KDXCurrency::$CurrentCurrency);

                $item['PROPERTY_SIZES_VALUE'] = array();
                if(!empty($item['PROPERTY_SIZES_CLOTHING_VALUE']))
                    $item['PROPERTY_SIZES_VALUE'] = $item['PROPERTY_SIZES_CLOTHING_VALUE'];
                elseif(!empty($item['PROPERTY_SIZES_TRAINERS_VALUE']))
                    $item['PROPERTY_SIZES_VALUE'] = $item['PROPERTY_SIZES_TRAINERS_VALUE'];
                elseif(!empty($item['PROPERTY_SIZES_TRAINERS_EU_VALUE']))
                    $item['PROPERTY_SIZES_EU_VALUE'] = $item['PROPERTY_SIZES_TRAINERS_EU_VALUE'];
                elseif(!empty($item['PROPERTY_SIZES_ACCESSORIES_VALUE']))
                    $item['PROPERTY_SIZES_VALUE'] = $item['PROPERTY_SIZES_ACCESSORIES_VALUE'];

                usort($item['PROPERTY_SIZES_VALUE'],'KDXDataCollector::sortSizes');
                usort($item['PROPERTY_SIZES_EU_VALUE'],'KDXDataCollector::sortSizes');

                if(!empty($item['PROPERTY_GALLERY_VALUE']))
                {
                    $item['DETAIL_PICTURE'] = reset($item['PROPERTY_GALLERY_VALUE']);
                }

                prepareLangFields($item);

                //$items[$item["ID"]]=$item;
                $items[] = $item;
            }

            $this->arResult["ITEMS"]=$items;

            $this->prepareSortVariant();

            if ($this->needCache && $obCache->StartDataCache() )
            {
                $obCache->EndDataCache( $this->arResult );
            }
        }

        $criteoView = array('event'=>'viewList', 'item'=>array_keys($this->arResult["ITEMS"]));
        $APPLICATION->SetPageProperty("CRITEOVIEW", ','.json_encode($criteoView));

    }

    function preparePagination()
    {
        if(empty($this->arResult['ITEMS']))
            return;
        global $APPLICATION;
        ob_start();
        $APPLICATION->IncludeComponent(
            "bitrix:system.pagenavigation",
            $this->arParams["PAGINATION_TEMPLATE"],
            array(
                "NAV_TITLE"=> "",
                "NAV_RESULT" => $this->arResult['RES'],
                "SHOW_ALWAYS" => 1,
                "AJAX_WRAPPER"=>$this->arParams["PAGINATION_WRAPPER"]
            ),
            "",
            array(
                "HIDE_ICONS" => "Y"
            )
        );
        $this->arResult["PAGINATION"] = ob_get_clean();
    }

    private function GetProductsBySelectionCode($Code) {
        $ar_order = array(
            'NAME' => 'ASC',
            'SORT' => 'ASC',
        );
        $ar_filter = array(
            'CODE' => $Code,
            'IBLOCK_ID' => intval($this->arParams["SELECTIONS_IBLOCK_ID"]),
            'ACTIVE' => 'Y',
            'ACTIVE_DATE' => 'Y',
        );
        $ar_select = array(
            'ID',
            'IBLOCK_ID',
            'NAME',
            'PROPERTY_PRODUCTS',
            'PROPERTY_BADGE',
            'PROPERTY_RETAIL_PRICE_MIN',
            'PROPERTY_RETAIL_PRICE_MAX',
            'PROPERTY_SECTION',
            'PROPERTY_CML2_MANUFACTURER',
        );
        $dbSelections = CIBlockElement::GetList($ar_order, $ar_filter, false, false, $ar_select);

        if($el = $dbSelections->GetNextElement()) {
            $fields = $el->GetFields();
            $properties = $el->GetProperties();

            if($products = $properties['PRODUCTS']['VALUE'])
                return $products;
            else {
                $arFilter = array(
                    'IBLOCK_ID' => intval($this->arParams["CATALOG_IBLOCK_ID"]),
                    'ACTIVE' => 'Y',
                    'PROPERTY_SELECTION' => $fields['ID']
                );
                if ($properties['BADGE']['VALUE']) {
                    $arFilter['PROPERTY_BADGE_VALUE'] = $properties['BADGE']['VALUE'];
                }
                // if ($properties['CML2_MANUFACTURER']['VALUE']) {
                //     $arFilter['PROPERTY_CML2_MANUFACTURER'] = $properties['CML2_MANUFACTURER']['VALUE'];
                // }
                if ($properties['SECTION']['VALUE']) {
                    $arFilter['SECTION_ID'] = $properties['SECTION']['VALUE'];
                }

                if(isset($properties['RETAIL_PRICE_MIN']['VALUE'], $properties['RETAIL_PRICE_MAX']['VALUE'])
                    && $properties['RETAIL_PRICE_MIN']['VALUE'] < $properties['RETAIL_PRICE_MAX']['VALUE']) {
                    $arFilter["><PROPERTY_RETAIL_PRICE_MIN"] = array($properties['RETAIL_PRICE_MIN']['VALUE'], $properties['RETAIL_PRICE_MAX']['VALUE']);
                } elseif(isset($properties['RETAIL_PRICE_MIN']['VALUE']) && !isset($properties['RETAIL_PRICE_MAX']['VALUE'])) {
                    $arFilter[">=PROPERTY_RETAIL_PRICE_MIN"] = $properties['RETAIL_PRICE_MIN']['VALUE'];
                } elseif(isset($properties['RETAIL_PRICE_MAX']['VALUE']) && !isset($properties['RETAIL_PRICE_MIN']['VALUE'])) {
                    $arFilter["<=PROPERTY_RETAIL_PRICE_MIN"] = $properties['RETAIL_PRICE_MAX']['VALUE'];
                }



                $products = CIBlockElement::GetList($ar_order, $arFilter, false, false, $ar_select);

                while($product = $products->Fetch()) {
                    $selectionID[] = $product['ID'];
                }

                return $selectionID;
            }
        }

        return false;
    }

    private function GetSelectionData($selection_code) {
        $ar_order = array(
            'NAME' => 'ASC',
            'SORT' => 'ASC',
        );

        $ar_select = array(
            'ID',
            'IBLOCK_ID',
            'NAME',
            "PROPERTY_NAME_EN",
            'DETAIL_TEXT',
            'PROPERTY_DETAIL_TEXT_EN',
            'PROPERTY_BACKGROUND_COLOR',
            'PROPERTY_BACKGROUND_IMAGE',
            'PROPERTY_IMAGE_STYLE',
            'PROPERTY_SELECTION_STYLE',
            'PROPERTY_PRODUCT_LINES_STYLE',
            'PROPERTY_TITLE_RU',
            'PROPERTY_TITLE_EN',
            'PROPERTY_DESCRIPTION_RU',
            'PROPERTY_DESCRIPTION_EN',
            'PROPERTY_KEYWORDS_RU',
            'PROPERTY_KEYWORDS_EN',
            'EDIT_LINK',
        );

        $ar_filter = array(
            'IBLOCK_ID' => intval($this->arParams["SELECTIONS_IBLOCK_ID"]),
            'CODE' => $selection_code,
            'ACTIVE' => 'Y',
        );

        $dbSelections = CIBlockElement::GetList($ar_order, $ar_filter, false, false, $ar_select);

        if($el = $dbSelections->Fetch()) {
            $arButtons = CIBlock::GetPanelButtons(
                intval($this->arParams["SELECTIONS_IBLOCK_ID"]),
                $el["ID"],
                0,
                array("SECTION_BUTTONS"=>false, "SESSID"=>false)
            );

            $result = array(
                'SELECTION_ID' => $el['ID'],
                'SELECTION_EDIT_LINK' => $arButtons["edit"]["edit_element"]["ACTION_URL"],
            );

            if(LANGUAGE_ID=='en') {
                $result['NAME'] = $el['PROPERTY_NAME_EN_VALUE'];
                $result['DETAIL_TEXT'] = $el["PROPERTY_DETAIL_TEXT_EN_VALUE"];
            } else {
                $result['NAME'] = $el['NAME'];
                $result['DETAIL_TEXT'] = $el["DETAIL_TEXT"];
            }

            $result['DATA'] = $el;

            return $result;
        }
    }
}
