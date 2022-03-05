<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>

<?
//printr($arResult);

?>
<div id="ajax_wrapper">
    <div class="paging">
        <div class="models">
            <?=$arResult["PAGINATION"]?>
        </div>

        <div class="sort">
            <label for="sort-by">Сортировать по:</label>
            <?/*<select name="sort-by" id="sort-by">
                <?$price_sort="CATALOG_PRICE_".KDXSettings::getSetting("RETAIL_PRICE_ID");?>
                <option value="">Выбрать</option>
                <option value="<?=$price_sort?>::DESC" <?=($_GET["SORT"]."::".$_GET["SORT_DIRECTION"])==($price_sort."::DESC") ? "selected" : ""?>>Сначала дорогое</option>
                <option value="<?=$price_sort?>::ASC" <?=($_GET["SORT"]."::".$_GET["SORT_DIRECTION"])==($price_sort."::ASC") ? "selected" : ""?>>Сначала дешёвое</option>
                <option value="3">По популярности</option>
            </select>*/?>
            <select name="sort-by" class="ajax_load push_history" data-ajax-response-wrapper="<?=$arParams["PAGINATION_WRAPPER"]?>">
                <?
                $arPagen=array();
                foreach($_GET as $key=>$val){
                    if(preg_match('/^PAGEN_\d+$/i',$key)){
                        $arPagen[]=$key;
                    }
                }
                foreach($arResult['SORT'] as $code=>$name){
                    $page = $APPLICATION->GetCurPageParam('SORT='.$code,array_merge($arPagen,array('SORT')));
                    ?>
                    <option value="<?=$page?>" <?if($_GET["SORT"]==$code){echo 'selected';}?>><?=$name?></option>
                <?
                }
                ?>
            </select>
            <a href="<?=$APPLICATION->GetCurPageParam('ORDER=ASC',array_merge($arPagen,array('ORDER')))?>"  class="ajax_load push_history" data-ajax-response-wrapper="<?=$arParams["PAGINATION_WRAPPER"]?>">&darr;</a>
            <a href="<?=$APPLICATION->GetCurPageParam('ORDER=DESC',array_merge($arPagen,array('ORDER')))?>" class="ajax_load push_history" data-ajax-response-wrapper="<?=$arParams["PAGINATION_WRAPPER"]?>">&uarr;</a>
        </div>

    </div>
    <!-- //paging -->
123
    <ul class="catalog">
        <?foreach($arResult["ITEMS"] as $id=>$item){?>
            <li>
                <h2><a href="<?=$item["DETAIL_PAGE_URL"]?>"><?=$item["NAME"]?></a></h2>
                <div class="price"><strong><?=$item["RETAIL_PRICE_CONVERTED_FORMATED"];?></strong></div>
                <div><span data-href="/ajax/catalog.list/fastview.php?CODE=<?=$item['CODE']?>" class="ajax_load ajax_popup"><?=GetMessage('KODIX_CATALOG_LIST_FAST_VIEW')?></span></div>
                <div class="img">
                    <?$img=kdxCFile::ResizeImageGet(array_shift(array_values($item["PICTURES"])), array('width'=>180, 'height'=>265), BX_RESIZE_IMAGE_PROPORTIONAL, true);?>
                    <?$detail=CFile::GetPath($item["DETAIL_PICTURE"]);?>
                    <?$preview=CFile::GetPath($item["PREVIEW_PICTURE"]);?>
                    <a href="<?=$item["DETAIL_PAGE_URL"]?>"><img class="changePicOnHover preloadable" width="180" height="250" data-original="<?=$preview;?>"  data-outpicture="<?=$preview;?>" data-overpicture="<?=$detail;?>"  /></a>
                </div>
            </li>
        <?}?>
    </ul>

    <div class="paging">
        <?=$arResult["PAGINATION"]?>
    </div>
    <!-- //paging -->
</div>