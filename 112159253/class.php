<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/**
 * Created by Dmitry Salnikov.
 * Date: 24.04.14
 */

class CKodixCatalogComplexComponent extends CBitrixComponent
{
    private $arSections = false;

    public function onPrepareComponentParams($arParams)
    {
        $GLOBALS["NavNum"] = 0;
        if(!isset($arParams["CACHE_TIME"]) || $arParams["CACHE_TIME"] <= -1)	$arParams["CACHE_TIME"] = 3600;
        $arParams["CATALOG_IBLOCK_ID"] = (intval($arParams["CATALOG_IBLOCK_ID"]));
        /*
         * Чтобы работало определение раздела каталога по SECTION_CODE_PATH, обазательно должен существовать $arParams["IBLOCK_ID"]
         * */
        $arParams["IBLOCK_ID"]=$arParams["CATALOG_IBLOCK_ID"];
        return $arParams;
    }
    /*
    public function getCacheID($additionalCacheID = false)
    {
        $cache_id = serialize(array($arParams));

    }*/

    public function executeComponent()
    {
        $arDefaultUrlTemplates404 = array(
            "new" => "new/",
            "sale" => "sale/",
            "detail" => "product/#CODE#/",
            "brand" => "brand/#BRAND#/",
            //"brand_section" => "brand/#BRAND#/#SECTION_CODE_PATH#/",
            //"section_brand" => "#SECTION_CODE_PATH#/brand/#BRAND#/",
            "section_brand_color" => "#SECTION_CODE_PATH#/brand/#BRAND#/#COLOR#/",
            "section_tag" => "#SECTION_CODE_PATH#/tag/#TAG#/",
            "section_tag_brand" => "#SECTION_CODE_PATH#/tag/#TAG#/brand/#BRAND#/",
            "section" => "#SECTION_CODE_PATH#/",
            "selection" => "selection/#SELECTION#/"
        );

        $arDefaultVariableAliases404 = array();

        $arComponentVariables = array();
        $arVariables = array();
        $engine = new CComponentEngine($this);
        if (\Bitrix\Main\Loader::includeModule('iblock'))
        {
            $engine->addGreedyPart("#SECTION_CODE_PATH#");
            $engine->setResolveCallback(array("CIBlockFindTools", "resolveComponentEngine"));
        }
        $arUrlTemplates = CComponentEngine::MakeComponentUrlTemplates($arDefaultUrlTemplates404, $this->arParams["SEF_URL_TEMPLATES"]);
        $arVariableAliases = CComponentEngine::MakeComponentVariableAliases($arDefaultVariableAliases404, $this->arParams["VARIABLE_ALIASES"]);
        $componentPage = $engine->guessComponentPath(
            $this->arParams["SEF_FOLDER"],
            $arUrlTemplates,
            $arVariables
        );

        if(!$componentPage)
        {
            if($this->arParams['SHOW_404'] == 'Y')
                show404();
            else
                $componentPage = "list";
        }

        CComponentEngine::InitComponentVariables($componentPage, $arComponentVariables, $arVariableAliases, $arVariables);
        $this->arResult = array(
            "FOLDER" => $this->arParams["SEF_FOLDER"],
            "URL_TEMPLATES" => $arUrlTemplates,
            "VARIABLES" => $arVariables,
            "ALIASES" => $arVariableAliases
        );
        $this->IncludeComponentTemplate($componentPage);
    }

    function getSections()
    {
        if(!$this->arSections)
            $this->arSections = KDXDataCollector::getSectionsArray();

        return $this->arSections;
    }

    public function makeURL(&$arSection,$type)
    {
        $arSections = $this->getSections();

        unset($arSections[ $arSection['ID'] ]['~SECTION_PAGE_URL']);

        $sectionCode = trim(KDXDataCollector::makeSectionUrl($arSections,$arSection['ID'],true),'/');

        /*if($type == 'brand')
        {
            $brandCode = $this->arResult['VARIABLES']['BRAND'];
            $page = $this->arParams['SEF_URL_TEMPLATES']['brand'];
            $arSection['SECTION_PAGE_URL'] = $this->arParams['SEF_FOLDER'] . CComponentEngine::makePathFromTemplate($page,array('SECTION_CODE_PATH' => $sectionCode,'BRAND' => $brandCode));
        }*/

        if($type == 'section')
        {
            $brandCode = $this->arResult['VARIABLES']['BRAND'];
            $page = $this->arParams['SEF_URL_TEMPLATES']['section_brand'];
            $arSection['SECTION_PAGE_URL'] = $this->arParams['SEF_FOLDER'] . CComponentEngine::makePathFromTemplate($page,array('SECTION_CODE_PATH' => $sectionCode,'BRAND' => $brandCode));
        }

        if($type == 'sale')
        {
            $page = $this->arParams['SEF_URL_TEMPLATES']['sale_section'];
//            echo '<pre>';
//            print_r($this->arParams['SEF_URL_TEMPLATES']);
//            echo '</pre>';die();
            $arSection['SECTION_PAGE_URL'] = $this->arParams['SEF_FOLDER'] .CComponentEngine::makePathFromTemplate($page,array('SECTION_CODE_PATH' => $sectionCode));
        }

        if($type == 'new')
        {
            $page = $this->arParams['SEF_URL_TEMPLATES']['new_section'];
            $arSection['SECTION_PAGE_URL'] = $this->arParams['SEF_FOLDER'] .CComponentEngine::makePathFromTemplate($page,array('SECTION_CODE_PATH' => $sectionCode));
        }
    }
}
