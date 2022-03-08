<?php
/**
 * Created by:  KODIX 17.03.2015 11:19
 * Email:       support@kodix.ru
 * Web:         www.kodix.ru
 * Developer:   Kostin Denis
 */

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?if(!isAjax() || isRestoreHistory('ALL')){?>
<div class="content_row">
    <div class="catalog_block">
<?}?>
        <div class="head_bar ajax_remove_in_new_before_append">
            <?//$APPLICATION->IncludeComponent('bitrix:breadcrumb','catalog');?>
            <?if(!empty($arResult['PAGINATION'])){?>
            <div class="head_b_func">
                <?=$arResult['PAGINATION']?>
            </div>
            <?}?>
        </div>

        <?
        // Для подборок выводим описание
        if (isset($arParams['SELECTION_CODE']) && $arParams['SELECTION_CODE']){?>
            <?$this->AddEditAction($arResult['SELECTION_ID'], $arResult['SELECTION_EDIT_LINK'], 'Редактировать подборку');?>
            <div class="products_wrapper" id="<?=$this->GetEditAreaId($arResult['SELECTION_ID']);?>">
        <? } else { ?>
            <div class="products_wrapper">
        <? } ?>
                <?if(!empty($component->getParent()->arParams['FILTER_RESULT']['FILTRATED_VALUES'])){?>
                     <?if((!empty($component->getParent()->arParams['FILTER_RESULT']['FILTRATED_VALUES']['PROP'])
                        || !empty($component->getParent()->arParams['FILTER_RESULT']['FILTRATED_VALUES']['PRICE']))):?>

                          <ul class="filter_results ajax_remove_in_new_before_append">
                            <?foreach($component->getParent()->arParams['FILTER_RESULT']['FILTRATED_VALUES']['PROP'] as $type => $arValues){?>

                                <?foreach($arValues as $value){?>
                                    <?if($type == 'CML2_MANUFACTURER'){
                                        $valueName = $component->getParent()->arParams['FILTER_RESULT']['PROPERTIES']['CML2_MANUFACTURER']['VALUES'][$value]['NAME'];
                                        ?>
                                        <li class="filter_r_item" data-type="PROP" data-filter="[name='FILTER[PROP][<?=$type?>][]'][value='<?=$value?>']"><?=$valueName?><span class="delete_btn"></span></li>
                                    <?}else{?>
                                        <li class="filter_r_item" data-type="PROP" data-filter="[name='FILTER[PROP][<?=$type?>][]'][value='<?=$value?>']"><?=$value?><span class="delete_btn"></span></li>
                                    <?}?>
                                <?}?>
                            <?}?>
                                <?foreach($component->getParent()->arParams['FILTER_RESULT']['FILTRATED_VALUES']['PRICE'] as $type => $value){?>
                                    <li class="filter_r_item" data-type="PRICE" data-filter="[name='FILTER[PRICE][<?=$type?>]']"><?if($type == 'MIN'){echo GetMessage('FILTER_PRICE_FROM');}else{echo GetMessage('FILTER_PRICE_TO');}?><?=KDXCurrency::format($value, KDXCurrency::$CurrentCurrency)?><span class="delete_btn"></span></li>
                                <?}?>
                          </ul>
                    <?endif;?>
                <?}?>
            <h1 class="products_title ajax_remove_in_new_before_append"><?$APPLICATION->ShowTitle()?></h1>

            <?
            // Для подборок выводим описание
            if (isset($arResult['DETAIL_TEXT']) && $arResult['DETAIL_TEXT']){?>
                <?
                //$this->AddEditAction($arResult['SELECTION_ID'], $arResult['SELECTION_EDIT_LINK'], 'Редактировать подборку');
                //$this->AddDeleteAction($arResult['SECTION']['ID'], $arResult['SECTION']['DELETE_LINK'], $strSectionDelete, $arSectionDeleteParams);
                ?>

                <div class="additional-content-wrapper"> <?/*id="<?=$this->GetEditAreaId($arResult['SELECTION_ID']);?>"*/?>
                    <?#=$arResult['SELECTION_ID']?>
                    <?#=$arResult['SELECTION_EDIT_LINK']?>
                    <?=$arResult['DETAIL_TEXT']?>
                </div>
            <?}?>

            <div class="products_summary ajax_remove_in_new_before_append">
                <?
                    if(!$arResult['DESCRIPTION_NOT_VIEW'] && !empty($arResult['ITEMS'])){
                        $APPLICATION->ShowProperty('description');
                    }
                ?>
                <?if($arParams['SALE'] =='Y'){
                    $APPLICATION->IncludeComponent(
                        "bitrix:main.include",
                        ".default",
                        array(
                            "AREA_FILE_SHOW"   => "file",
                            "AREA_FILE_SUFFIX" => "",
                            "EDIT_TEMPLATE"    => "",
                            "PATH"             => "/catalog/sect_sale_description.php"
                        ),
                        false
                    );
                }?>
                <?if($arParams['BRANDS_INCLUDE_AREA'] =='Y'){
                    $APPLICATION->IncludeComponent(
                        "bitrix:main.include",
                        ".default",
                        array(
                            "AREA_FILE_SHOW"   => "file",
                            "AREA_FILE_SUFFIX" => "",
                            "EDIT_TEMPLATE"    => "",
                            "PATH"             => "/catalog/sect_brands_description.php"
                        ),
                        false
                    );
                }?>
                <?if($arParams['ITKCHOICE_INCLUDE_AREA'] =='Y'){
                    $APPLICATION->IncludeComponent(
                        "bitrix:main.include",
                        ".default",
                        array(
                            "AREA_FILE_SHOW"   => "file",
                            "AREA_FILE_SUFFIX" => "",
                            "EDIT_TEMPLATE"    => "",
                            "PATH"             => "/catalog/sect_itkchoice_description.php"
                        ),
                        false
                    );
                }?>
            </div>

            <?if($arParams['FROM_SEARCH'] == 'Y'){?>
                <div class="search_res_head ajax_remove_in_new_before_append">
                    <?if(empty($arResult['ITEMS']) && empty($arParams['SEARCH'])){?>
                        <h1 class="search_title"><?=GetMessage('EMPTY_QUERY')?></h1>
                    <?}elseif(!empty($arResult['ITEMS'])){?>
                        <h1 class="search_title"><?=GetMessage('RESULTS_FOR')?><span class="object"><?=htmlspecialchars($_REQUEST['q'])?></span></h1>
                    <?}else{?>
                        <h1 class="search_title"><?=GetMessage('NO_RESULTS_FOR')?><span class="object"><?=htmlspecialchars($_REQUEST['q'])?></span></h1>
                    <?}?>
                </div>
            <?}?>

            <?if(empty($arResult['ITEMS']) && $arParams['FROM_SEARCH'] != 'Y'){?>
                <div class="search_res_head">
                    <h1 class="search_title"><?=GetMessage('MORE_PRODUCTS_COMING_SOON')?></h1>
                </div>
            <?}?>

            <?if(!empty($arResult['ITEMS'])){?>
            <ul class="products_list">
                <!-- Только для этого товара - вывдоим в бренде его 9 раз -->
                <?$exclusive = 85232;?>
                <?if (count($arResult['ITEMS'] == 1) &&
                      (intval($arResult['ITEMS'][85232]['ID'])==$exclusive) &&
                      ($APPLICATION->GetCurPage() == "/catalog/brand/itk/")
                ){
                    for ($i=0;$i<9;$i++) {
                        showCatalogItem($arResult['ITEMS'][85232]);
                    }
                } else {
                    foreach ($arResult['ITEMS'] as $arProduct) {
                        showCatalogItem($arProduct);
                    }
                }?>
            </ul>
            <?}?>
            <?$APPLICATION->ShowViewContent('APPEND_MENU')?>
        </div>
        <?if(!empty($arResult['PAGINATION'])){?>
        <div class="foot_row ajax_remove_in_base_before_append">
            <?=$arResult['PAGINATION']?>
        </div>
        <?}?>
<?if(!isAjax() || isRestoreHistory('ALL')){?>
    </div>
    <div class="related_items_category">
    </div>
    <div class="related_items_search">
    </div>
    <div class="related_items_search_empty">
    </div>
    <?$APPLICATION->IncludeComponent('kodix:blog.interesting','',array('CACHE_TYPE' => 'Y', 'CACHE_TIME' => 3600));?>
</div>
<?}?>
<script>
    $('.head_bar .head_b_func').html( $('.foot_row').html() )
</script>
<?if(intval($arParams['SECTION_ID'])){?>
<script type="text/javascript">
    rrApiOnReady.push(function() {
        try { rrApi.categoryView(<?=intval($arParams['SECTION_ID'])?>); } catch(e) {}
    });
    $(document).ready(function(){
        $.ajax({
            type: "post",
            url: "/ajax/RetailRocket.php",
            data: {query: 'CategoryToItems', rr_params: "<?=$arParams['SECTION_ID']?>"},
            success: function(data){
                $('.related_items_category').html(data);
            }
        });
    })
</script>
<?}?>
<?if($arParams['FROM_SEARCH'] == 'Y' && !empty($arResult['ITEMS'])){?>
    <?$keyword = trim(strip_tags($_REQUEST['q']))?>
<script type="text/javascript">
    $(document).ready(function(){
        $.ajax({
            type: "post",
            url: "/ajax/RetailRocket.php",
            data: {query: 'SearchToItems', rr_params: "<?=$keyword?>"},
            success: function(data){
                $('.related_items_search').html(data);
            }
        });
    })
</script>
<?}?>
<?if($arParams['FROM_SEARCH'] == 'Y' && (empty($arResult['ITEMS']) || empty($arParams['SEARCH']))){?>
<script type="text/javascript">
    $(document).ready(function(){
        $.ajax({
            type: "post",
            url: "/ajax/RetailRocket.php",
            data: {query: 'ItemsToMain'},
            success: function(data){
                $('.related_items_search_empty').html(data);
            }
        });
    })
</script>
<?}?>