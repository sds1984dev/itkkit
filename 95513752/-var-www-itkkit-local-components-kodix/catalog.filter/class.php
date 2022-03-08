<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/**
 * Created by:  KODIX 07.07.14 12:49
 * Email:       support@kodix.ru
 * Web:         www.kodix.ru
 * Developer:   Salnikov Dmitry
 */

//use Kodix\Comments as KDX_C;
use Bitrix\Highloadblock as HB;

class CKodixCatalogFilterComponent extends CBitrixComponent    //CBitrixCatalogSmartFilter
{
    protected $SectionFilter;
    protected $needCache = true;


    function getSections()
    {
        return $this->getParent()->getSections();
    }

    public function onPrepareComponentParams($arParams)
    {
        parent::onPrepareComponentParams($arParams);
        if(!isset($arParams["CACHE_TIME"]) || $arParams["CACHE_TIME"] <= -1)	$arParams["CACHE_TIME"] = 3600;
        $arParams["CATALOG_IBLOCK_ID"] = (intval($arParams["CATALOG_IBLOCK_ID"]));
        $arParams["SECTION_ID"] = intval($arParams["SECTION_ID"]);
        $arParams['FILTER_PRICE']=intval($arParams['FILTER_PRICE']);
        return $arParams;
    }

    public function includeAdditionalHeaders(){
        global $APPLICATION;
        $APPLICATION->AddHeadScript('/bitrix/js/kodix.main/kodix_libs/jquery.range.slider/jQAllRangeSliders-withRuler-min.js');
    }

    public function executeComponent()
    {
        //сбрасываем кеш так как данные выборка getPropertyValues не должна кешироваться, а выборка getPropertyList кешируется напрямую
        $this->abortResultCache();

        $this->getPropertyList();
        $this->getChildrenSections();
        $this->getPropertyValues();
        $this->GetSelections();

        ArrayMegaSORT($this->arResult['PROPERTIES'],array('SORT'));

        $this->collectFilter();
        $this->getPriceFork();
        $this->includeAdditionalHeaders();

        $this->includeComponentTemplate();

    }

    /**
     * @return array
     * собираем свойства учавствующие в умном фильтре
     */
    public function getPropertyList()
    {
        $obCache = new CPHPCache;
        if($obCache->InitCache($this->arParams["CACHE_TIME"], 'simple_id', 'kcache/kodix.sale/catalog.filter/props')){
            $this->arResult['PROPERTIES']  = $obCache->GetVars();
        }
        else{
            $this->arResult['PROPERTIES'] = array();
            foreach(CIBlockSectionPropertyLink::GetArray($this->arParams['CATALOG_IBLOCK_ID']) as $PID => $arLink)
            {
                if($arLink["SMART_FILTER"] !== "Y")
                    continue;

                $rsProperty = CIBlockProperty::GetByID($PID);
                $arProperty = $rsProperty->Fetch();
                if($arProperty && $arProperty['ACTIVE']=='Y')
                {
                    switch($arProperty['PROPERTY_TYPE']){
                        case 'E':
                            if( intval($arProperty['LINK_IBLOCK_ID'])) { // если это свойство можно сразу загружать
                                $ar_order = array(
                                    'NAME' => 'ASC',
                                    'SORT' => 'ASC',
                                );

                                $ar_filter = array(
                                    'IBLOCK_ID' => $arProperty['LINK_IBLOCK_ID'],
                                    'PROPERTY_ID' => $arProperty['ID'],
                                );

                                $ar_select = array(
                                    'ID',
                                    'IBLOCK_ID',
                                    'NAME',
                                    'DETAIL_PAGE_URL',
                                    'PREVIEW_PICTURE',
                                    'XML_ID'
                                );

                                $Els = CIBlockElement::GetList($ar_order, $ar_filter, false, false, $ar_select);
                                while($el = $Els->Fetch()) {
                                    $arProperty['VALUES'][$el['ID']] = array(
                                        'NAME' => $el['NAME'],
                                        'XML_ID' => $el['XML_ID']
                                    );
                                }
                            }
                            break;
                        case 'L':
                            $ar_filter = array(
                                'IBLOCK_ID' => $arProperty['IBLOCK_ID'],
                                'PROPERTY_ID' => $arProperty['ID'],
                            );
                            /*if (isset($this->arParams['SELECTION_CODE'])) {
                                $ar_filter['ID'] = $this->GetProductsBySelectionCode($this->arParams['SELECTION_CODE']);
                            }*/

                            $Values = CIBlockPropertyEnum::GetList(
                                array(
                                    'SORT' => 'ASC',
                                    'VALUE' => 'ASC'
                                ),
                                $ar_filter
                            );
                            while($value = $Values->Fetch())
                                $arProperty['VALUES'][$value['ID']] = array('NAME' => $value['VALUE'], 'XML_ID' => $value['XML_ID']);
                            break;
                        default;
                            break;
                    }

                    if($arProperty['USER_TYPE']=='directory'){
                        $res_hb = HB\HighloadBlockTable::getList(array('filter'=>array('TABLE_NAME'=>$arProperty['USER_TYPE_SETTINGS']['TABLE_NAME'])));
                        if($fields = $res_hb->fetch()){
                            $hb_entity = HB\HighloadBlockTable::compileEntity($fields);
                            //dump(class_exists($fields['NAME'].'Table')); - сгенерированный класс сущность
                            $classname = $fields['NAME'].'Table';
                            $current_hb_entity =new $classname();
                            $res_current_hb = $current_hb_entity->getList();
                            while($fields_current_hb = $res_current_hb->fetch()){
                                $arProperty['VALUES'][$fields_current_hb['UF_XML_ID']]=array('NAME'=>$fields_current_hb['UF_NAME']);
                            }
                        }
                    }
                    if($arProperty['USER_TYPE']=='ElementXmlID' && $arProperty['CODE']=='CML2_MANUFACTURER'){
                        $arProperty['VALUES']=KDXSaleDataCollector::getBrands();
                    }
                    $this->arResult['PROPERTIES'][$arProperty['ID']]=$arProperty;
                }
            }


            if(!CModule::IncludeModule('kodix.sale')) die('kodix.sale module');

            if($obCache->StartDataCache()){
                if(empty($this->arResult['PROPERTIES'])){
                    $obCache->AbortDataCache();
                }
                $obCache->EndDataCache($this->arResult['PROPERTIES']);
            }
        }
    }

    /**
     * Берем подборки из инфоблока
     **/
    function GetSelections(){
        $ar_order = array(
            'SORT' => 'ASC',
            'NAME' => 'ASC'
        );

        $ar_select = array(
            'ID',
            'IBLOCK_ID',
            'NAME',
            'PROPERTY_NAME_EN',
            'CODE',
            'DETAIL_PAGE_URL'
        );

        $ar_filter = array(
            'IBLOCK_ID' => intval($this->arParams["SELECTIONS_IBLOCK_ID"]),
            'ACTIVE' => 'Y',
            'ACTIVE_DATE' => 'Y'
        );
        
        $dbSelections=[];
        $dbSelections = CIBlockElement::GetList($ar_order, $ar_filter, false, false, $ar_select);

        while($el = $dbSelections->GetNext()) {
            
             $value = array(
                'NAME' => $el['NAME'],
                'NAME_EN' => $el['PROPERTY_NAME_EN_VALUE'],
                'ID' => $el['ID'],
                'CODE' => $el['CODE'],
                'DETAIL_PAGE_URL' => $el['DETAIL_PAGE_URL'],
            );
            if(isset($this->arParams['SELECTION_CODE'])) {
                if ($value['CODE'] == $this->arParams['SELECTION_CODE']) $value['CLASS'] = 'active';
            }
                
            $this->arResult['PROPERTIES']['SELECTIONS']['VALUES'][] = $value;
            $this->arResult['PROPERTY_VALUES']['SELECTIONS'][$el['ID']]  = $value;
        }

        $this->arResult['PROPERTIES']['SELECTIONS']['CODE'] = 'SELECTIONS';
        $this->arResult['PROPERTIES']['SELECTIONS']['NAME'] = "FEATURES";
        $this->arResult['PROPERTIES']['SELECTIONS']['PROPERTY_TYPE'] = "P";
    }

    function makeURL(&$arSection)
    {


        if($this->arParams['BRAND_PAGE'] == 'Y')
        {
            $this->getParent()->makeURL($arSection,'brand');
        }

        if($this->arParams['SALE'] == 'Y')
        {
            $this->getParent()->makeURL($arSection,'sale');
        }

        if($this->arParams['JUST_NEW'] == 'Y')
        {
            $this->getParent()->makeURL($arSection,'new');
        }
    }

    /**
     * получаем дочерние категории
     */
    public function getChildrenSections(){

        $arSections = $this->getSections();

        $this->arResult['CHILDREN_SECTIONS']=array();

        if(empty($this->arParams['FILTER']))
        {
            foreach($arSections as $arSection)
            {
                if($arSection['DEPTH_LEVEL'] == 1)
                {
                    if($arSection['ID'] == $this->arParams['SECTION_ID'])
                        $arSection['OPENED'] = 'Y';

                    $arSubSections = array();

                    foreach($arSection['SUBSECTIONS'] as $sID)
                    {
                        if($arSections[ $sID ]['CNT'] > 0 && $arSections[ $sID ]['DEPTH_LEVEL'] == 2)
                        {
                            if($sID == $this->arParams['SECTION_ID'])
                            {
                                $arSections[$sID]['CLASS'] = 'active';
                                $arSection['OPENED'] = 'Y';
                            }
                            $arSubSections[ $sID ] = $arSections[$sID];
                        }
                    }

                    $arSection['SUBSECTIONS'] = $arSubSections;

                    $this->arResult['CHILDREN_SECTIONS'][$arSection['ID']] = $arSection;
                }

            }

            if($this->arParams['SECTION_ID'])
            {
                $this->SectionFilter=array(
                    'SECTION_ID'=>intval($this->arParams['SECTION_ID']),
                    'INCLUDE_SUBSECTIONS'=>'Y',
                    'SECTION_GLOBAL_ACTIVE '=>'Y',
                );
            }

            return;
        }

        if(!CModule::IncludeModule('iblock')) die('iblock module');

        $global_filter = getGlobalFilterForSite();

        $arOrder = array(
            'left_margin' => 'asc',
            'SORT' => 'asc',
        );

        $arFilter =array(
            'IBLOCK_ID' => KDXSettings::getSetting('CATALOG_IBLOCK_ID'),
            'ACTIVE' => 'Y'
        );

        if(!empty($this->arParams['FILTER']))
            $arFilter = array_merge( $arFilter, $this->arParams['FILTER']);

        if($this->arParams['SECTION_ID'])
        {
            $this->SectionFilter=array(
                'SECTION_ID'=>intval($this->arParams['SECTION_ID']),
                'INCLUDE_SUBSECTIONS'=>'Y',
                'SECTION_GLOBAL_ACTIVE '=>'Y',
            );
        }

        if(is_array($global_filter)){
            $arFilter=array_merge($arFilter,$global_filter);
        }

        $arSelect = array(
            'ID',
            'IBLOCK_ID',
            'NAME',
            'DETAIL_PAGE_URL',
            'PREVIEW_PICTURE',
        );

        $res = CIBlockElement::GetList($arOrder, $arFilter, array('IBLOCK_SECTION_ID'), false, $arSelect);

        $arFirst = array();
        $arSecond = array();

        while ($fields = $res->Fetch())
        {
            $arSection = $arSections[ $fields['IBLOCK_SECTION_ID'] ];


            if($arSection['DEPTH_LEVEL'] == 1)
            {
                $arFirst[] = $arSection['ID'];
            }
            elseif($arSection['DEPTH_LEVEL'] == 2)
            {
                $arSecond[] = $arSection['ID'];
                $arFirst[] = $arSection['IBLOCK_SECTION_ID'];
            }
            elseif($arSection['DEPTH_LEVEL'] == 3)
            {
                $arSecond[] = $arSection['IBLOCK_SECTION_ID'];
                $arFirst[] = $arSections[ $arSection['IBLOCK_SECTION_ID'] ]['IBLOCK_SECTION_ID'];
            }
        }

        foreach($arSections as $arSection)
        {
            if($arSection['DEPTH_LEVEL'] == 1 && in_array($arSection['ID'],$arFirst))
            {
                if($arSection['ID'] == $this->arParams['SECTION_ID'])
                    $arSection['OPENED'] = 'Y';

                $this->makeURL($arSection);

                $arSubSections = array();

                foreach($arSection['SUBSECTIONS'] as $sID)
                {
                    $arSubSection = $arSections[ $sID ];
                    if($arSubSection['CNT'] > 0 && $arSubSection['DEPTH_LEVEL'] == 2 && in_array($sID,$arSecond))
                    {
                        if($sID == $this->arParams['SECTION_ID'])
                        {
                            $arSubSection['CLASS'] = 'active';
                            $arSection['OPENED'] = 'Y';
                        }
                        $this->makeURL($arSubSection);
                        $arSubSections[ $sID ] = $arSubSection;
                    }
                }

                $arSection['SUBSECTIONS'] = $arSubSections;

                $this->arResult['CHILDREN_SECTIONS'][$arSection['ID']] = $arSection;
            }

        }

    }

    /**
     * собираем различные варианты значений свойств
     */
    public function getPropertyValues(){
        $this->arResult['PROPERTY_VALUES']=array();
        if(!empty($this->arParams["FILTER"])){
            $this->needCache=false;
        }
        $obCache = new CPHPCache();
        $cacheId = md5(serialize(array($this->arResult['PROPERTIES'],$this->SectionFilter)));
        $cachePath = 'kcache/kodix.sale/catalog.filter/propvalues';
        if($this->needCache && $obCache->InitCache($this->arParams["CACHE_TIME"],$cacheId,$cachePath)){
            $this->arResult['PROPERTY_VALUES'] = $obCache->GetVars();
        }
        else{
            foreach ($this->arResult['PROPERTIES'] as $prop) {
                if(isset($this->arParams['SELECTION_CODE'])) break;

                if($prop['CODE']==$this->arParams['FILTER_PRICE_PROP']){
                    continue;
                }
                $arOrder = array();
                $arFilter = array(
                    'IBLOCK_ID' => $this->arParams["CATALOG_IBLOCK_ID"],
                    'ACTIVE' => 'Y',
                );
                /*if (isset($this->arParams['SELECTION_CODE'])) {
                    print_r($this->GetProductsBySelectionCode($this->arParams['SELECTION_CODE']));
                    $arFilter['ID'] = $this->GetProductsBySelectionCode($this->arParams['SELECTION_CODE']);
                }*/

                if($global_filter = KDXSettings::getSetting('GLOBAL_SALE_FILTER')){
                    $arFilter = array_merge($global_filter,$arFilter);
                }
                $arSelect = array(
                    'ID',
//                    'IBLOCK_ID',
//                    'DETAIL_PAGE_URL',
//                    'PREVIEW_PICTURE',
                );
                if(!empty($this->SectionFilter) && is_array($this->SectionFilter)){
                    $arFilter=array_merge($arFilter,$this->SectionFilter);
                }
                if(!empty($this->arParams["FILTER"])){
                    $arFilter=array_merge($arFilter, $this->arParams["FILTER"]);
                }
                //Если тип свойства число, то имеет смысл собирать максимальную и минимальную границу диапазона
                if($prop['PROPERTY_TYPE'] == 'N'){
                    $arOrder=array('PROPERTY_'.$prop['CODE']=>'DESC');
                    $arSelect[]='PROPERTY_'.$prop['CODE'];
                    $res = CIBlockElement::GetList($arOrder, $arFilter, false, array('nTopCount'=>1), $arSelect);
                    if($fields = $res->Fetch()){
                        $this->arResult['PROPERTY_VALUES'][$prop['CODE']][$fields['PROPERTY_'.$prop['CODE'].'_VALUE']]='MIN';
                    }
                    $arOrder=array('PROPERTY_'.$prop['CODE']=>'ASC');
                    $res = CIBlockElement::GetList($arOrder, $arFilter, false, array('nTopCount'=>1), $arSelect);
                    if($fields = $res->Fetch()){
                        $this->arResult['PROPERTY_VALUES'][$prop['CODE']][$fields['PROPERTY_'.$prop['CODE'].'_VALUE']]='MAX';
                    }
                }
                else{
                    $cur_request=explode('?',$_SERVER['REQUEST_URI'])[0];
                    $cache_filename='/var/www/itkkit/local/cache_sql/local_components_kodix_catalog.filter_class_445'.$prop['CODE'].'.obj';
                    if (($cur_request=='/catalog/sale/') and (file_exists($cache_filename))) {
                        $objData = file_get_contents($cache_filename);
                        $res=unserialize($objData);
                        $res = CIBlockElement::GetList($arOrder, $arFilter, array('PROPERTY_'.$prop['CODE']), false, $arSelect);
                    } else {
                        $res = CIBlockElement::GetList($arOrder, $arFilter, array('PROPERTY_'.$prop['CODE']), false, $arSelect);
                        if ($cur_request=='/catalog/sale/') {
                            $objData = serialize($res);
                            $t=file_put_contents($cache_filename, $objData);
                        }
                    }
                    if($res->SelectedRowsCount()>1){
                        //нас интересуют только те свойства где больше одного варианта значения
                        while ($fields = $res->Fetch()) {
                            $vval='';
                            foreach ($fields as $key => $val) {
                                if(preg_match('/^PROPERTY_\w+_ENUM_ID$/i',$key)){
                                    $vval=$val;
                                    break;
                                }
                                if(preg_match('/^PROPERTY_\w+_VALUE$/i',$key)){
                                    $vval=$val;
                                }
                            }
                            $this->arResult['PROPERTY_VALUES'][$prop['CODE']][$vval]=$fields['CNT'];
                        }
                    }
                    
                }

            }

            if(isset($this->arResult['PROPERTY_VALUES']['SIZES_CLOTHING'])){
                if(isset($this->arResult['PROPERTY_VALUES']['SIZES_TRAINERS'])){
                    $arClothing = array_diff_key($this->arResult['PROPERTY_VALUES']['SIZES_CLOTHING'], $this->arResult['PROPERTY_VALUES']['SIZES_TRAINERS']);
                    $this->arResult['PROPERTY_VALUES']['SIZES_CLOTHING'] = $arClothing;
                }
                uksort($this->arResult['PROPERTY_VALUES']['SIZES_CLOTHING'],'KDXDataCollector::sortSizes');
            }

            if(isset($this->arResult['PROPERTY_VALUES']['SIZES_TRAINERS'])){
                uksort($this->arResult['PROPERTY_VALUES']['SIZES_TRAINERS'],'KDXDataCollector::sortSizes');
            }

            if(isset($this->arResult['PROPERTY_VALUES']['SIZES_TRAINERS_EU'])){
                uksort($this->arResult['PROPERTY_VALUES']['SIZES_TRAINERS_EU'],'KDXDataCollector::sortSizes');
            }

            if(isset($this->arResult['PROPERTY_VALUES']['SIZES_ACCESSORIES'])){
                uksort($this->arResult['PROPERTY_VALUES']['SIZES_ACCESSORIES'],'KDXDataCollector::sortSizes');
            }

            if ($this->needCache && $obCache->StartDataCache() )
            {
                $obCache->EndDataCache( $this->arResult['PROPERTY_VALUES'] );
            }
        }


    }

    /**
     * получаем ценовую вилку доступных предложений
     */
    public function getPriceFork(){
        if($this->arParams['FILTER_PRICE_PROP']){
            if(!CModule::IncludeModule('iblock')) die('iblock module');

            $this->arResult['CURRENCY']=KdxCurrency::get(KDXCurrency::$CurrentCurrency);

            $arOrder = array(
                'PROPERTY_'.$this->arParams['FILTER_PRICE_PROP'] => 'ASC',
            );
            $arFilter = array(
                'IBLOCK_ID' => $this->arParams["CATALOG_IBLOCK_ID"],
                'ACTIVE' => 'Y',
            );

            if($global_filter = getGlobalFilterForSite()){
                $arFilter = array_merge($global_filter,$arFilter);
            }

            if(!empty($this->SectionFilter) && is_array($this->SectionFilter)){
                $arFilter=array_merge($arFilter,$this->SectionFilter);
            }

            if(is_array($this->arParams['FILTER']) && !empty($this->arParams['FILTER'])){
                $arFilter = array_merge($arFilter, $this->arParams['FILTER']);
                $this->needCache = false;
            }

            $arSelect = array(
//                'ID',
//                'IBLOCK_ID',
//                'NAME',
//                'DETAIL_PAGE_URL',
//                'PREVIEW_PICTURE',
                'PROPERTY_'.$this->arParams['FILTER_PRICE_PROP']
            );
            $obCache = new CPHPCache();
            $cacheId = serialize(array($arOrder,$arFilter,$arSelect,KDXCurrency::$CurrentCurrency));
            $cachePath = 'kcache/trk.catalog.filter/price';
            if($this->needCache && $obCache->InitCache($this->arParams["CACHE_TIME"],$cacheId,$cachePath)){
                $this->arResult['PRICE_VALUES'] = $obCache->GetVars();
            }
            else{
                $res = CIBlockElement::GetList($arOrder, $arFilter, false, array('nTopCount'=>1), $arSelect);
                if ($fields = $res->Fetch()) {
                    $this->arResult['PRICE_VALUES']['MIN']=$fields['PROPERTY_'.$this->arParams['FILTER_PRICE_PROP'].'_VALUE'];
                }
                $arOrder = array(
                    'PROPERTY_'.$this->arParams['FILTER_PRICE_PROP'] => 'DESC',
                );
                $res = CIBlockElement::GetList($arOrder, $arFilter, false, array('nTopCount'=>1), $arSelect);
                if ($fields = $res->Fetch()) {
                    $this->arResult['PRICE_VALUES']['MAX']=$fields['PROPERTY_'.$this->arParams['FILTER_PRICE_PROP'].'_VALUE'];
                }

                if(KDXCurrency::$CurrentCurrency != KDXSettings::getSetting('DEFAULT_CURRENCY') && !is_null(KDXCurrency::$CurrentCurrency)){

                    if(intval($this->arResult['PRICE_VALUES']['MIN'])){
                        $this->arResult['PRICE_VALUES']['MIN']=(KDXCurrency::convert($this->arResult['PRICE_VALUES']['MIN'],KDXCurrency::$CurrentCurrency));
                    }
                    if(intval($this->arResult['PRICE_VALUES']['MAX'])){
                        $this->arResult['PRICE_VALUES']['MAX']=(KDXCurrency::convert($this->arResult['PRICE_VALUES']['MAX'],KDXCurrency::$CurrentCurrency));
                    }
                }

                if ($this->needCache && $obCache->StartDataCache() )
                {
                    $obCache->EndDataCache( $this->arResult['PRICE_VALUES'] );
                }
            }
        }

    }


    /**
     * получаем id фильтра
     */
    protected function collectFilter(){
        global $selection_name;

        if(!empty($this->arParams['SECTION_ID']))
            $this->arResult['FILTRATED_VALUES']['SECTION'][] = $this->arParams['SECTION_ID'];

        if(is_array($_REQUEST['FILTER'])){
            if(!CModule::IncludeModule('kodix.sale')) die('kodix.sale module');

            $arCatalogFilter=array();
            $arSizesFilter = array();
            if(isset($_POST['FILTER']['PROP']) && !isset($this->arParams['SELECTION_CODE'])){
                foreach ($_POST['FILTER']['PROP'] as $code=>$vals) {
                    /*if ($code == "SELECTIONS" && is_array($vals)) {
                        // Если мы выбираем подборку, то сбрасываем фильтр по секции
                        unset($_POST['FILTER']['SECTION']);
                        unset($this->arResult['FILTRATED_VALUES']);
                        $this->arResult['FILTRATED_VALUES']['PROP']['SELECTION']=$vals;

                        foreach($vals as $val) {
                            $products = $this->GetProductsBySelection($val);

                            $catalog_filter = &$arCatalogFilter['ID'];
                            if (isset($catalog_filter) && is_array($catalog_filter)) {
                                $catalog_filter = array_merge($catalog_filter, $products);
                            } else {
                                $catalog_filter = $products;
                            }
                            unset($catalog_filter);

                            $product_filter = &$this->arResult['FILTRATED_VALUES']['PROP']['ID'];
                            if (isset($product_filter) && is_array($product_filter)) {
                                $product_filter = array_merge($product_filter, $products);
                            } else {
                                $product_filter = $products;
                            }
                            unset($product_filter);
                            
                        }
                        $selection_name = ($this->GetSelectionName($vals[0]));
                        break;
                    } else {*/
                        if (in_array($code, ['SIZES_CLOTHING', 'SIZES_TRAINERS', 'SIZES_ACCESSORIES'])) {
                            $arSizesFilter[] = ['PROPERTY_'.$code => $vals];
                        } else {
                            $arCatalogFilter['PROPERTY_'.$code] = $vals;
                        }

                        $this->arResult['FILTRATED_VALUES']['PROP'][$code]=$vals;
                    //}
                }
                if (!empty($arSizesFilter)) {
                    $arSizesFilter['LOGIC'] = 'OR';
                    $arCatalogFilter[] = $arSizesFilter;
                }
            }

            if(isset($_POST['FILTER']['SECTION']))
            {
                $arCatalogFilter['SECTION_ID'] = $_POST['FILTER']['SECTION'];
                $arCatalogFilter['INCLUDE_SUBSECTIONS'] = 'Y';
                $this->arResult['FILTRATED_VALUES']['SECTION'] = $_POST['FILTER']['SECTION'];
            }

            if(isset($_POST['FILTER']['PRICE'])){
                foreach ($_POST['FILTER']['PRICE'] as $code=>$val) {
                    if(floatval($val) && ($code=='MIN' || $code=='MAX')){

                        $default_currency_val = floatval($val);
                        //если текущая валюта отлична от валюты по умолчанию конвертируем данные
                        if(KDXSettings::getSetting('DEFAULT_CURRENCY') != KDXCurrency::$CurrentCurrency && !is_null(KDXCurrency::$CurrentCurrency)){
                            $default_currency_val = call_user_func(
                                ($code=='MIN'?'intval':'ceil'),
                                KDXCurrency::convert(floatval($val),KDXSettings::getSetting('DEFAULT_CURRENCY'),KDXCurrency::$CurrentCurrency)
                            );
                        }
                        /**
                         * в базу записываем цену в стандартной валюте (обычно это рубли)
                         * а в шаблон отдаем цену в текущей валюте, забитой пользователем
                         */
                        $arCatalogFilter[($code=='MIN'?'>=':'<=').'PROPERTY_RETAIL_PRICE_MIN']= $default_currency_val;
                        $this->arResult['FILTRATED_VALUES']['PRICE'][$code]=$val;
                    }
                }
            }
            $this->arResult['FILTER']=$arCatalogFilter;

            $this->getParent()->arResult['CHILDREN_COMPONENT_BUFFER']['FILTER_ARRAY']=$arCatalogFilter;
            $res = Kodix\Sale\Filter\FilterTable::getList(array(
                'filter'=>array('FILTER'=>serialize($arCatalogFilter))
            ));
            if($fields = $res->fetch()){
                $this->getParent()->arResult['CHILDREN_COMPONENT_BUFFER']['FILTER']=$fields['ID'];
                $this->arResult['FILTER']=$fields['ID'];
                $result = Kodix\Sale\Filter\FilterTable::update($fields['ID'],array('COUNT_USE'=>$fields['COUNT_USE']+1));
                if(!$result->isSuccess()){
                    printr($result->getErrorMessages());
                }
            }
            else{
                $result = Kodix\Sale\Filter\FilterTable::add(array(
                    'FILTER'=>serialize($arCatalogFilter)
                ));
                if($result->isSuccess()){
                    $id = $result->getId();
                    $this->arResult['FILTER']= $id;
                    $this->getParent()->arResult['CHILDREN_COMPONENT_BUFFER']['FILTER']= $id;
                }
                else{
                    printr($result->getErrorMessages());
                }
            }
        }

        elseif(intval($_REQUEST['FILTER'])){
            if(!CModule::IncludeModule('kodix.sale')) die('odix.sale module');
            $res = Kodix\Sale\Filter\FilterTable::getById(intval($_GET['FILTER']));
            if($fields = $res->fetch()){

                $UserFilter=unserialize($fields['FILTER']);

                $this->arResult['FILTER']=$UserFilter;
                if(is_array($UserFilter) && !empty($UserFilter)){
                    foreach ($UserFilter as $code => $vals) {
                        if(preg_match('/^PROPERTY_(\w+)$/i',$code,$matches)){
                            $this->arResult['FILTRATED_VALUES']['PROP'][$matches[1]]=$vals;
                        }

                        if(preg_match('/^([\<=\>]+)PROPERTY_(\w+)$/i',$code,$matches)){
                            if($matches[1]=='<=')
                            {
                                if(strpos($matches[2],'RETAIL_PRICE') !== false)
                                    $this->arResult['FILTRATED_VALUES']['PRICE']['MAX']=$vals;
                                else
                                    $this->arResult['FILTRATED_VALUES']['PROP'][$matches[2]]['MAX']=$vals;
                            }
                            if($matches[1]=='>=')
                            {
                                if(strpos($matches[2],'RETAIL_PRICE') !== false)
                                    $this->arResult['FILTRATED_VALUES']['PRICE']['MIN']=$vals;
                                else
                                    $this->arResult['FILTRATED_VALUES']['PROP'][$matches[2]]['MIN']=$vals;
                            }
                        }

                        if(preg_match('/^([\<=\>]+)CATALOG_PRICE_\d+$/i',$code,$matches)){
                            if($matches[1]=='<=')
                                $this->arResult['FILTRATED_VALUES']['PRICE']['MAX']=$vals;
                            if($matches[1]=='>=')
                                $this->arResult['FILTRATED_VALUES']['PRICE']['MIN']=$vals;
                        }

                        if($code == 'SECTION_ID')
                        {
                            $this->arResult['FILTRATED_VALUES']['SECTION'] = $vals;
                        }
                    }

                    if(KDXCurrency::$CurrentCurrency != KDXSettings::getSetting('DEFAULT_CURRENCY') && !is_null(KDXCurrency::$CurrentCurrency)){

                        if(intval($this->arResult['FILTRATED_VALUES']['PRICE']['MIN'])){
                            $this->arResult['FILTRATED_VALUES']['PRICE']['MIN']=intval(KDXCurrency::convert($this->arResult['FILTRATED_VALUES']['PRICE']['MIN'],KDXCurrency::$CurrentCurrency));
                        }
                        if(intval($this->arResult['FILTRATED_VALUES']['PRICE']['MAX'])){
                            $this->arResult['FILTRATED_VALUES']['PRICE']['MAX']=ceil(KDXCurrency::convert($this->arResult['FILTRATED_VALUES']['PRICE']['MAX'],KDXCurrency::$CurrentCurrency));
                        }
                    }

                }
            }
        }
    }

    private function GetProductsBySelectionCode($Code) {
        $ar_order = array(
            'NAME' => 'ASC',
            'SORT' => 'ASC',
        );
        $ar_filter = array(
            'CODE' => $Code,
            'ACTIVE' => 'Y',
        );
        $ar_select = array(
            'ID',
            'IBLOCK_ID',
            'NAME',
            'PROPERTY_PRODUCTS'
        );
        $dbSelections = CIBlockElement::GetList($ar_order, $ar_filter, false, false, $ar_select);

        if($el = $dbSelections->GetNextElement()) {
            $products = $el->GetProperty("PRODUCTS");
            return $products['VALUE'];
        }

        return false;
    }

}
