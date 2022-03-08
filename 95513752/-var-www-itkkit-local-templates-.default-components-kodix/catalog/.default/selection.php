<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$APPLICATION->SetPageProperty('WEBPACK_JS','catalog');
?>

<?
$sort = 'sort_date';

if (isset($_COOKIE['sectionItemsSort']) &&
isset($_COOKIE['sectionItemsOrder']) &&
  $_COOKIE['sectionItemsSort'] !== '' &&
  $_COOKIE['sectionItemsOrder'] !== ''
){
  switch ($_COOKIE['sectionItemsSort']) {
    case 'low_price':
    case 'high_price':
    $sort = $_COOKIE['sectionItemsSort'];
    $sectionSort = 'property_RETAIL_PRICE_MIN';
    $sectionOrder = $_COOKIE['sectionItemsOrder'];
    break;

    default:
    $sectionSort = 'DATE_ACTIVE_FROM';
    $sectionOrder = 'desc';
    break;
  }

} else {
  $sectionSort = 'DATE_ACTIVE_FROM';
  $sectionOrder = 'desc';
}

if(!isAjax())
{
  ?>
  <div class="section-bar">
    <div class="sort">
      <div class="sort-btn js-btn-sort"><?=LANGUAGE_ID == 'en' ? 'Sort by' : 'Сортировка';?></div>
      <ul class="sort-wrap js-wrap-sort">
        <li><a href="#"<?=$sort == 'sort_date' ? ' class="_active"' : '';?> data-type="sort_date" data-order="desc"><?=LANGUAGE_ID == 'en' ? 'New in' : 'По умолчанию'?></a></li>
        <li><a href="#"<?=$sort == 'low_price' ? ' class="_active"' : '';?> data-type="low_price" data-order="asc"><?=LANGUAGE_ID == 'en' ? 'Price (Low)' : 'Возрастанию цены'?></a></li>
        <li><a href="#"<?=$sort == 'high_price' ? ' class="_active"' : '';?> data-type="high_price" data-order="desc"><?=LANGUAGE_ID == 'en' ? 'Price (High)' : 'Убыванию цены'?></a></li>
      </ul>
    </div>
    <?/*<select class="section-bar__sort js-sort-btn">
      <option <?=$sort == 'sort_date' ? ' selected' : '';?> disabled hidden><?=LANGUAGE_ID == 'en' ? 'Sort by' : 'Сортировка'?></option>
      <option data-type="sort_date" data-order="desc" value="">
        <?=LANGUAGE_ID == 'en' ? 'New in' : 'По умолчанию'?>
      </option>

      <option <?=$sort == 'low_price' ? ' selected' : '';?> data-type="low_price" data-order="asc" value="">
        <?=LANGUAGE_ID == 'en' ? 'Price (Low)' : 'Возрастанию цены'?>
      </option>

      <option <?=$sort == 'high_price' ? ' selected' : '';?> data-type="high_price" data-order="desc" value="">
        <?=LANGUAGE_ID == 'en' ? 'Price (High)' : 'Убыванию цены'?>
      </option>

    </select>*/?>
  </div>
<?
    ?><div id="catalog"><?
}

if(!isRestoreHistory('ALL'))
{
    ?><div class="empty_div"><?
}?>

<?
$selectionId = CIBlockElement::GetList(array(),array('IBLOCK_ID'=>$arParams["SELECTIONS_IBLOCK_ID"], 'CODE'=>$arResult["VARIABLES"]["SELECTION"]),false,array(),array('ID'))->Fetch()['ID'];
// Get ajax used for paging and sorting
if(SITE_TEMPLATE_ID != 'ajax' || ($_SERVER['REQUEST_METHOD'] == 'POST' && is_array($_POST['FILTER']))){
    /*$APPLICATION->IncludeComponent("kodix:catalog.filter", "new", array(
        "CATALOG_IBLOCK_ID"=>$arParams["CATALOG_IBLOCK_ID"],
        "FILTER_PRICE"=>$arParams["FILTER_PRICE"],
        "SELECTIONS_IBLOCK_ID"=>$arParams["SELECTIONS_IBLOCK_ID"],
        "SECTION_CODE"=>$arResult["VARIABLES"]["SECTION_CODE"],
        "SELECTION_CODE"=>$arResult["VARIABLES"]["SELECTION"],
    ),$component);*/
    $APPLICATION->IncludeComponent(
      "kodix:catalog.filter",
      "new",
      array(
          "CATALOG_IBLOCK_ID" => "1",
          "SELECTIONS_IBLOCK_ID"=>$arParams["SELECTIONS_IBLOCK_ID"],
          "CACHE_TYPE" => "A",
          "CACHE_TIME" => "3600",
          "FILTER_PRICE" => "1",
          "FILTER_PRICE_PROP" => $arParams['FILTER_PRICE_PROP'],
          "SECTION_ID" => $arResult['VARIABLES']['SECTION_ID'],
          'FILTER' => array('=PROPERTY_SELECTION'=>$selectionId),
      ),
      $component
  );
}

$GLOBALS["NavNum"] = 0;
$APPLICATION->IncludeComponent("kodix:catalog.list", "new", array(
    "IBLOCK_TYPE" =>$arParams["IBLOCK_TYPE"],
    "CATALOG_IBLOCK_ID"=>$arParams["CATALOG_IBLOCK_ID"],
    "SELECTIONS_IBLOCK_ID"=>$arParams["SELECTIONS_IBLOCK_ID"],
    "SKU_IBLOCK_ID"=>$arParams["SKU_IBLOCK_ID"],
    "BRANDS_IBLOCK_ID"=>$arParams["BRANDS_IBLOCK_ID"],
    "GRIDS_IBLOCK_ID"=>$arParams["GRIDS_IBLOCK_ID"],
    "PAGE_ITEMS_COUNT"=>$arParams["PAGE_ITEMS_COUNT"],
    "SORT_BY1" => $sectionSort,
    "SORT_ORDER1" => $sectionOrder,
    "SORT_BY2" => $arParams["SORT_BY2"],
    "SORT_ORDER2" => $arParams["SORT_ORDER2"],
    "SORT_VARIANTS" => $arParams["SORT_VARIANTS"],
    "SELECTION_CODE"=>$arResult["VARIABLES"]["SELECTION"],
    "FILTER_PRICE"=>$arParams["FILTER_PRICE"],
    "JUST_NEW" => "N",
    "SALE" => "N",
    "BRANDS_FILTER" => array(
    ),
    "CACHE_TYPE" => $arParams['CACHE_TYPE'],
    "CACHE_TIME" => $arParams['CACHE_TIME'],
), $component);
?>


<?if(!isRestoreHistory('ALL'))
{
    ?></div><?
}

if(!isAjax())
{
    ?></div><?
}
