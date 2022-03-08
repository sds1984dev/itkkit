<?php
/**
 * Created by:  KODIX 17.03.2015 11:19
 * Email:       support@kodix.ru
 * Web:         www.kodix.ru
 * Developer:   Kostin Denis
 */
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?
  if (LANGUAGE_ID == 'en' && !empty($arResult['SELECTION_DATA']['DATA']['PROPERTY_DETAIL_TEXT_EN_VALUE'])){
    $sel_desc = $arResult['SELECTION_DATA']['DATA']['PROPERTY_DETAIL_TEXT_EN_VALUE'];
  } else if (!empty($arResult['SELECTION_DATA']['DATA']['DETAIL_TEXT'])) {
    $sel_desc = $arResult['SELECTION_DATA']['DATA']['DETAIL_TEXT'];
  }

?>
<?if(!isAjax() || isRestoreHistory('ALL') || isset($_POST['FILTER'])){?>
<section class="catalog-section">
    <div class="catalog_block ssa">
<?}?>
<?if(!isAjax()){?>
    <div class="tags-list">
        <?
        // $getCurUrl = explode('?', $_SERVER['REQUEST_URI'], 2)[0];
        // $resTag = CIBlockElement::GetList(array(),array('IBLOCK_ID'=>21,'ACTIVE'=>'Y','=PROPERTY_SHOW'=>$getCurUrl,'=PROPERTY_SITE_VALUE'=>LANGUAGE_ID,'!=PROPERTY_FILTER'=>false),false,false,array('*','PROPERTY_*'));
        // while ($arTag = $resTag->Fetch()){
        //     echo '<a href="'.$getCurUrl.'tag/'.$arTag['CODE'].'/">'.$arTag['NAME'].'</a>';
        // }
        // if (isset($arParams['BRANDS_FILTER']) && !empty($arParams['BRANDS_FILTER'])){
        //     $getColors = getAllColors(array('=SECTION_ID'=>$arParams['SECTION_ID'], '=PROPERTY_CML2_MANUFACTURER'=>$arParams['BRANDS_FILTER']));
        //     foreach ($getColors as $color){
        //         echo '<a href="'.$getCurUrl.$color.'/">'.ucfirst($color).'</a>';
        //     }
        // }
        ?>
    </div>
    <?
    // Для подборок выводим описание
    if (isset($arResult['DETAIL_TEXT']) && $arResult['DETAIL_TEXT']){?>
        <?
        //$this->AddEditAction($arResult['SELECTION_ID'], $arResult['SELECTION_EDIT_LINK'], 'Редактировать подборку');
        //$this->AddDeleteAction($arResult['SECTION']['ID'], $arResult['SECTION']['DELETE_LINK'], $strSectionDelete, $arSectionDeleteParams);
        ?>

        <div class="additional-content-wrapper"> <?/*id="<?=$this->GetEditAreaId($arResult['SELECTION_ID']);?>"*/?>
          <div class="grid-row">
                    <div class="col-md-6 detail-text-wrap">
                      <div class="detail-text">
                        <?=$sel_desc?>
                      </div>
                    </div>
                    <div class="col-md-6">
                    </div>
          </div>
            <?#=$arResult['SELECTION_ID']?>
            <?#=$arResult['SELECTION_EDIT_LINK']?>

        </div>
    <?}?>
<?}?>
        <?
        // Для подборок выводим описание
        if (isset($arParams['SELECTION_CODE']) && $arParams['SELECTION_CODE']){?>
            <?$this->AddEditAction($arResult['SELECTION_ID'], $arResult['SELECTION_EDIT_LINK'], 'Редактировать подборку');?>
            <div class="catalog-section__grid" id="<?=$this->GetEditAreaId($arResult['SELECTION_ID']);?>">
        <? } else { ?>
            <div class="catalog-section__grid">
        <? } ?>
                <?/*if(!empty($component->getParent()->arParams['FILTER_RESULT']['FILTRATED_VALUES'])){?>
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
                <?}*/?>


            <div class="products_summary ajax_remove_in_new_before_append">
                <?/*
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
                }*/?>
            </div>

            <?if($arParams['FROM_SEARCH'] == 'Y'){?>
                <div class="search_res_head ajax_remove_in_new_before_append">
                    <?if(empty($arResult['ITEMS']) && empty($arParams['SEARCH'])){?>
                        <?$APPLICATION->AddViewContent('NO_RESULTS_SEARCH', '<div class="search-form__text">'.GetMessage('EMPTY_QUERY').'</div>');?>
<!--                        <h1 class="search_title">--><?//=GetMessage('EMPTY_QUERY')?><!--</h1>-->
                    <?}elseif(!empty($arResult['ITEMS'])){?>
<!--                        <h1 class="search_title">--><?//=GetMessage('RESULTS_FOR')?><!--<span class="object">--><?//=htmlspecialchars($_REQUEST['q'])?><!--</span></h1>-->
                    <?}else{?>
                        <?$APPLICATION->AddViewContent('NO_RESULTS_SEARCH', '<div class="search-form__text">'.GetMessage("RESULTS_FOR").' <span class="search-form__query">'.htmlspecialchars($_REQUEST["q"]).'</span> '.GetMessage("NO_FOUND_RESULTS_FOR").'</div>');?>
<!--                        <h1 class="search_title">--><?//=GetMessage('NO_RESULTS_FOR')?><!--<span class="object">--><?//=htmlspecialchars($_REQUEST['q'])?><!--</span></h1>-->
                    <?}?>
                </div>
            <?}?>

            <?if(empty($arResult['ITEMS']) && $arParams['FROM_SEARCH'] != 'Y'){?>
                <div class="catalog-section__msg">
                    <svg class="icon icon-error_message">
                        <use xlink:href="/local/templates/kit_new/resources/svg/app.svg#error_message"></use>
                    </svg>
                    <h1 class="heading--h1"><?=GetMessage('MORE_PRODUCTS_COMING_SOON')?></h1>
                    <!-- <h1 class="heading--h1"><?=GetMessage('TRY_CHANGE')?></h1> -->
                    <!-- <p><?=GetMessage('NO_RESULTS_RES')?></p> -->
                </div>
            <?}?>

            <?if(!empty($arResult['ITEMS'])){?>
            <div class="grid-row">
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
                    $i = 0;
                    $countries = getHlCountries();
                    foreach ($arResult['ITEMS'] as $arProduct) {
                        showCatalogItem($arProduct, ['countries' => $countries]);
                    }
                }?>
            </div>
            <?}?>
        </div>

        <?//if(!empty($arResult['PAGINATION'])){?>
            <div class="grid-row head_bar ajax_remove_in_base_before_append helper--margin-top-auto">
                <div class="col-xl-4 ninja--sm-lg">
                    <?//$APPLICATION->IncludeComponent('bitrix:breadcrumb','catalog_new');?>
                </div>
                <?if(!empty($arResult['PAGINATION'])){?>
                    <div class="head_b_func col-xl-8">
                        <?=$arResult['PAGINATION']?>
                    </div>
                <?}?>
            </div>
        <?//}?>
         <?if(!empty($arResult['PAGINATION'])){?>
        <div class="foot_row hidden ajax_remove_in_base_before_append">
            <?=$arResult['PAGINATION']?>
        </div>
        <?}?>

<?if(!isAjax() || isRestoreHistory('ALL') || isset($_POST['FILTER'])){?>
    </div>
    <div class="related_items_category">
    </div>
    <div class="related_items_search">
    </div>
    <div class="related_items_search_empty">
    </div>
    <?//$APPLICATION->IncludeComponent('kodix:blog.interesting','',array('CACHE_TYPE' => 'Y', 'CACHE_TIME' => 3600));?>
</section>
<?}?>

<script>
    $('.head_bar .head_b_func').html( $('.foot_row').html() )
</script>
<?if(intval($arParams['SECTION_ID'])){?>
<script type="text/javascript">
    rrApiOnReady.push(function() {
        try { rrApi.categoryView(<?=intval($arParams['SECTION_ID'])?>); } catch(e) {}
    });
</script>
<?}?>
