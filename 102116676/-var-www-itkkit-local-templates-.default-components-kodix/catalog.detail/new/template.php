<?php
/**
 * Created by:  KODIX 19.03.2015 14:24
 * Email:       support@kodix.ru
 * Web:         www.kodix.ru
 * Developer:   Kostin Denis
 */
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$i=0;
global $USER;
$APPLICATION->SetPageProperty("PAGE_CLASS", "product_page");
$arResult['OG_IMAGE'] = false;
?>

<ul class="breadcrumb breadcrumb--detail" itemscope="" itemtype="http://schema.org/BreadcrumbList">
    <li itemscope="" itemprop="itemListElement" itemtype="http://schema.org/ListItem"><a itemprop="item" href="/"><span itemprop="name"><?=LANGUAGE_ID == 'en' ? 'Home' : 'Главная';?></span><meta itemprop="position" content="1"></a></li>
    <li>&gt;</li>
    <?/*<li itemscope="" itemprop="itemListElement" itemtype="http://schema.org/ListItem"><a itemprop="item" href="/catalog/"><span itemprop="name"><?=LANGUAGE_ID == 'en' ? 'Catalog' : 'Каталог';?></span><meta itemprop="position" content="2"></a></li>
    <li>&gt;</li>*/?>
    <?$resSection = CIBlockSection::GetNavChain(false, $arResult['IBLOCK_SECTION_ID']);
    $nChain = 3;
    while ($arSection = $resSection->GetNext()){
        $resCurrentSection = CIBlockSection::GetList(array(), array('IBLOCK_ID'=> $arParams['IBLOCK_ID'], 'ID' => $arSection['ID']), true, array('ID', 'NAME', 'UF_EN_NAME'));
        if ($arCurrentSection = $resCurrentSection->GetNext()){
            if (LANGUAGE_ID == 'en'){
                $sectionTitle = $arCurrentSection['UF_EN_NAME'];
            } else {
                $sectionTitle = $arCurrentSection['NAME'];
            }
        }?>
        <li itemscope="" itemprop="itemListElement" itemtype="http://schema.org/ListItem"><a itemprop="item" href="<?=$arSection['SECTION_PAGE_URL']?>"><span itemprop="name"><?=$sectionTitle?></span><meta itemprop="position" content="<?=$nChain?>"></a></li>
        <li>&gt;</li>
        <?$nChain++;?>
    <?}?>
    <li><p><?=$arResult['NAME']?></p></li>
</ul>
<div class="grid-row">
<!--    <div class="ninja--sm ninja--md ninja--lg col-xl-1"></div>-->
    <div class="col-sm-12 col-md-7 col-lg-8 col-xl-7">
        <?if (!$arResult['HAVE_REAL_SIZES']){?>
            <?
            $APPLICATION->IncludeComponent("kodix:catalog.recommended", 'soldout', array(
                "IBLOCK_ID"=>KDXSettings::getSetting('CATALOG_IBLOCK_ID'),
                "ELEMENT_ID"=>$arResult['ID'],
                "ITEMS_COUNT"=>3,
                "RR_QUERY"=>'UpSellItemToItems',
                "RR_PARAMS"=>$arResult['ID'],
                "NOT_SHOW_SEPARATOR"=>'Y',
            ));
            ?>
        <?}?>
        <div class="">
            <div class="product-overview product-slider" data-slick="product">
                <?$k=$j=1;
                if(empty($arResult['GALLERY'])){
                    $file = CFile::ResizeImageGet($arResult['DETAIL_PICTURE'],array('width'=>737,'height'=>737),BX_RESIZE_IMAGE_PROPORTIONAL);
                    $file_big = CFile::ResizeImageGet($arResult['DETAIL_PICTURE'],array('width'=>1474,'height'=>1474),BX_RESIZE_IMAGE_PROPORTIONAL);
                    $arResult['OG_IMAGE'] = $file;?>
                    <div class="product-overview__img" data-product-id="00<?=$k;$k++;?>">
                        <picture>
                            <?/*<source srcset="<?=$file_big['src']?> 1x, <?=$file_big['src']?> 2x">*/?>
                            <img class="lazyload img--lazyload"
                                 src=""
                                 data-src="<?=$file['src']?>"
                                 alt=""
                                 data-object-fit="contain">
                        </picture>
                    </div>
                <?}?>
                <?foreach($arResult['GALLERY'] as $pic){
                    $file = CFile::ResizeImageGet($pic,array('width'=>737,'height'=>737),BX_RESIZE_IMAGE_PROPORTIONAL);
                    $file_big = CFile::ResizeImageGet($pic,array('width'=>1474,'height'=>1474),BX_RESIZE_IMAGE_PROPORTIONAL);
                    if(!$arResult['OG_IMAGE']){
                        $OG_IMAGE = CFile::ResizeImageGet($pic,array('width'=>280,'height'=>280));
                        $arResult['OG_IMAGE'] = $OG_IMAGE;
                    }?>
                    <div class="product-overview__img" data-product-id="00<?=$k;$k++;?>">
                        <picture>
                            <?/*<source srcset="<?=$file_big['src']?> 1x, <?=$file_big['src']?> 2x">*/?>
                            <img class="lazyload img--lazyload"
                                 src=""
                                 data-src="<?=$file['src']?>" alt="" data-object-fit="contain">
                        </picture>
                    </div>
                    <?if(++$i > 50){break;}
                }?>
            </div>
        </div>

        <?/*<div class="ninja--lg ninja--xl">
            <div class="product-slider" data-slick="product">
                <?$k=$j=1;
                if(empty($arResult['GALLERY'])){
                    $file = CFile::ResizeImageGet($arResult['DETAIL_PICTURE'],array('width'=>423,'height'=>423),BX_RESIZE_IMAGE_PROPORTIONAL);
                    $file_big = CFile::ResizeImageGet($arResult['DETAIL_PICTURE'],array('width'=>846,'height'=>846),BX_RESIZE_IMAGE_PROPORTIONAL);
                    $file_mob = CFile::ResizeImageGet($arResult['DETAIL_PICTURE'],array('width'=>393,'height'=>393),BX_RESIZE_IMAGE_PROPORTIONAL);
                    $arResult['OG_IMAGE'] = $file;?>
                    <div class="product__img" data-product-id="00<?=$k;$k++;?>">
                        <picture>
                            <source srcset="<?=$file['src']?> 1x, <?=$file_big['src']?> 2x">
                            <img class="lazyload img--lazyload"
                                 src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAGQAAABkAQMAAABKLAcXAAAAA1BMVEVMaXFNx9g6AAAAAXRSTlMAQObYZgAAABNJREFUGBljGAWjYBSMglFAVwAABXgAAdxFAhsAAAAASUVORK5CYII="
                                 data-src="<?=$file['src']?>"
                                 alt=""
                                 data-object-fit="contain">
                        </picture>
                    </div>
                <?}?>
                <?foreach($arResult['GALLERY'] as $pic){
                    $file = CFile::ResizeImageGet($pic,array('width'=>423,'height'=>423),BX_RESIZE_IMAGE_PROPORTIONAL);
                    $file_big = CFile::ResizeImageGet($pic,array('width'=>846,'height'=>846),BX_RESIZE_IMAGE_PROPORTIONAL);
                    $file_mob = CFile::ResizeImageGet($pic,array('width'=>393,'height'=>393),BX_RESIZE_IMAGE_PROPORTIONAL);

                    if(!$arResult['OG_IMAGE']){
                        $OG_IMAGE = CFile::ResizeImageGet($pic,array('width'=>280,'height'=>280));
                        $arResult['OG_IMAGE'] = $OG_IMAGE;
                    }?>
                    <div class="product__img" data-product-id="00<?=$k;$k++;?>">
                        <picture>
                            <source srcset="<?=$file['src']?> 1x, <?=$file_big['src']?> 2x">
                            <img class="lazyload img--lazyload"
                                 src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAGQAAABkAQMAAABKLAcXAAAAA1BMVEVMaXFNx9g6AAAAAXRSTlMAQObYZgAAABNJREFUGBljGAWjYBSMglFAVwAABXgAAdxFAhsAAAAASUVORK5CYII="
                                 data-src="<?=$file['src']?>" alt="" data-object-fit="contain">
                        </picture>
                    </div>
                    <?if(++$i > 50){break;}
                }?>


            </div>
        </div>*/?>
    </div>
    <div class="ninja--sm ninja--tm ninja--md ninja--lg col-xl-1">
        <div class="product__thumb-wrap js-sticky-2" data-product-wrap>
            <?if(empty($arResult['GALLERY'])){
                $file = CFile::ResizeImageGet($arResult['DETAIL_PICTURE'],array('width'=>120,'height'=>120),BX_RESIZE_IMAGE_PROPORTIONAL);
                $file_big = CFile::ResizeImageGet($arResult['DETAIL_PICTURE'],array('width'=>240,'height'=>240),BX_RESIZE_IMAGE_PROPORTIONAL);
                $arResult['OG_IMAGE'] = $file;?>
                <div class="product__thumb" data-product-for="00<?=$j?>" data-product-current="<?=$j==1 ? 'true' : 'false'; $j++;?>">
                    <picture>
                        <?/*<source srcset="<?=$file['src']?> 1x, <?=$file_big['src']?> 2x">*/?>
                        <img class="lazyload img--lazyload"
                             src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAGQAAABkAQMAAABKLAcXAAAAA1BMVEVMaXFNx9g6AAAAAXRSTlMAQObYZgAAABNJREFUGBljGAWjYBSMglFAVwAABXgAAdxFAhsAAAAASUVORK5CYII="
                             data-src="<?=$file['src']?>" alt="" data-object-fit="contain">
                    </picture>
                </div>
            <?}?>
            <?foreach($arResult['GALLERY'] as $pic){
                $file = CFile::ResizeImageGet($pic,array('width'=>120,'height'=>120),BX_RESIZE_IMAGE_PROPORTIONAL);
                $file_big = CFile::ResizeImageGet($pic,array('width'=>240,'height'=>240),BX_RESIZE_IMAGE_PROPORTIONAL);
                if(!$arResult['OG_IMAGE']){
                    $OG_IMAGE = CFile::ResizeImageGet($pic,array('width'=>240,'height'=>240));
                    $arResult['OG_IMAGE'] = $OG_IMAGE;
                }?>
                <div class="product__thumb" data-product-for="00<?=$j?>" data-product-current="<?=$j==1 ? 'true' : 'false'; $j++;?>">
                    <picture>
                        <?/*<source srcset="<?=$file['src']?> 1x, <?=$file_big['src']?> 2x">*/?>
                        <img class="lazyload img--lazyload"
                             src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAGQAAABkAQMAAABKLAcXAAAAA1BMVEVMaXFNx9g6AAAAAXRSTlMAQObYZgAAABNJREFUGBljGAWjYBSMglFAVwAABXgAAdxFAhsAAAAASUVORK5CYII="
                             data-src="<?=$file['src']?>" alt="" data-object-fit="contain">
                    </picture>
                </div>
                <?if(++$i > 50){break;}
            }?>
        </div>
    </div>
    <div class="col-sm-12 col-md-5 col-lg-4 col-xl-4">
        <div class="product__about<?=count($arResult['GALLERY']) > 1 ? ' js-sticky' : '';?>">
            <?
            $prefix = '';
            if (LANGUAGE_ID == 'ru'){
                include('seo_meta.php');
                $curPage = explode('?', $_SERVER['REQUEST_URI'], 2)[0];
                if (isset($arSeoDetail[$curPage])){
                    $prefix = $arSeoDetail[$curPage]['prefix'];
                }
            }
            ?>
            <a class="title--brand" href="<?=$arResult["CML2_MANUFACTURER"][0]["DETAIL_PAGE_URL"]?>"><?=$arResult["CML2_MANUFACTURER"][0]["NAME"]?></a>
            <h1 class="title--product">
                <?=$prefix?>
                <?/*<a href="><?=$arResult['CML2_MANUFACTURER']['DETAIL_PAGE_URL']?>"><?=$arResult['CML2_MANUFACTURER']['NAME']?></a>*/?>
                <?/*=str_replace($arResult["CML2_MANUFACTURER"]["NAME"], '<a class="link link--secondary" href="'.$arResult["CML2_MANUFACTURER"]["DETAIL_PAGE_URL"].'">'.$arResult["CML2_MANUFACTURER"]["NAME"].'</a>', $arResult["NAME"])*/?>
                <?/*=$pos!==false ? substr_replace($arResult["NAME"], '<a class="link link--secondary" href="'.$arResult["CML2_MANUFACTURER"]["DETAIL_PAGE_URL"].'">'.$arResult["CML2_MANUFACTURER"]["NAME"].'</a>', $pos, strlen($arResult["CML2_MANUFACTURER"]["NAME"])) : $arResult["NAME"]*/?>
                <?
                if (LANGUAGE_ID == 'en' && $arResult["PROPERTY_EN_NAME_VALUE"] !== ''){
                    $pordName = $arResult["PROPERTY_EN_NAME_VALUE"];
                } else {
                    $pordName = $arResult["NAME"];
                }
                ?>
                <?foreach ($arResult["CML2_MANUFACTURER"] as $brand){
                    if ($brand["NAME"] == 'Nike SB' || $brand["NAME"] == 'Nike ACG'){
                        $pordName = str_ireplace($brand["NAME"], '', $pordName);
                    } else {
                        $pos = stripos($arResult["NAME"], $brand["NAME"]);
                        if ($pos !== false){
                            $pordName = str_ireplace($brand["NAME"], '<a class="link link--secondary" href="'.$brand["DETAIL_PAGE_URL"].'">'.$brand["NAME"].'</a>', $pordName);
                        }
                    }
                    /*=$pos!==false ? str_ireplace($brand["NAME"], '<a class="link link--secondary" href="'.$brand["DETAIL_PAGE_URL"].'">'.$brand["NAME"].'</a>', $arResult["NAME"]) : $arResult["NAME"]*/?>
                <?}?>
                <?=$pordName;?>
            </h1>
            <?if (isBot() && LANGUAGE_ID == 'ru'){
                $curCurrency = 'RUB';
            } else {
                $curCurrency = KDXCurrency::$CurrentCurrency;
            }?>
            <div class="product__available">
                <?if (LANGUAGE_ID == 'ru'){
                    if ($arResult['HAVE_REAL_SIZES']){?>
                        В наличии
                    <?} else {
                        $resOffersList = CCatalogSKU::GetInfoByProductIBlock($arResult['IBLOCK_ID']);
                        if (is_array($resOffersList)){
                            $rsOffers = CIBlockElement::GetList(array('PRICE'=>'DESC'),array('IBLOCK_ID'=>$resOffersList['IBLOCK_ID'], 'PROPERTY_'.$resOffersList['SKU_PROPERTY_ID']=>$arResult["ID"]));
                            if ($arOffer = $rsOffers->GetNext()){
                                $arOfferPrice = GetCatalogProductPrice($arOffer["ID"], 1);?>
                                Был в наличии по цене:<br>
                                <div class="price__wrapper price__wrapper--product">
                                    <div class="price">
                                        <span class="price__block">
                                            <span class="price--current"><?printf("%3.2f",KDXCurrency::convert($useVAT=="N" ? $arOfferPrice['PRICE'] / 1.21 : $arOfferPrice['PRICE'], $curCurrency));?></span>
                                            <span class="price--currency"><?=KDXCurrency::GetCurrencyName($curCurrency)?></span>
                                        </span>
                                    </div>
                                </div>
                            <?}
                        }
                    }
                }?>
				<?if (LANGUAGE_ID == 'en'){
                    if ($arResult['HAVE_REAL_SIZES']){?>
                    <?} else {
                        $resOffersList = CCatalogSKU::GetInfoByProductIBlock($arResult['IBLOCK_ID']);
                        if (is_array($resOffersList)){
                            $rsOffers = CIBlockElement::GetList(array('PRICE'=>'DESC'),array('IBLOCK_ID'=>$resOffersList['IBLOCK_ID'], 'PROPERTY_'.$resOffersList['SKU_PROPERTY_ID']=>$arResult["ID"]));
                            if ($arOffer = $rsOffers->GetNext()){
                                $arOfferPrice = GetCatalogProductPrice($arOffer["ID"], 1);?>
                                Was:<br>
                                <div class="price__wrapper price__wrapper--product">
                                    <div class="price ">
                                        <span class="price__block">
                                            <span class="price--current"><?printf("%3.2f",KDXCurrency::convert($useVAT=="N" ? $arOfferPrice['PRICE'] / 1.21 : $arOfferPrice['PRICE'], $curCurrency));?></span>
                                            <span class="price--currency"><?=KDXCurrency::GetCurrencyName($curCurrency)?></span>
                                        </span>
                                    </div>
                                </div>
                            <?}
                        }
                    }
                }?>
            </div>
            <div class="pr_f_price <?if($arResult['HAVE_REAL_SIZES'] && $arResult['BASE_PRICE_MIN'] > $arResult['RETAIL_PRICE_MIN']){?>mod_sale<?}?>">
                <?if($arResult['HAVE_REAL_SIZES']){?>
                    <?//$cur_type = (LANGUAGE_ID == 'ru') ? 'RUB': KDXCurrency::$CurrentCurrency;?>
                    <?//$cur_name = (LANGUAGE_ID == 'ru') ? GetMessage('APPROX_RUB'): KDXCurrency::GetCurrencyName(KDXCurrency::$CurrentCurrency)?>
                    <?$arrCountries = getHlCountries();?>
                    <?$cur_type = ($_SESSION['LAST_COUNTRY'] == 'RU') ? 'RUB': KDXCurrency::$CurrentCurrency;?>
                    <?$cur_name = ($_SESSION['LAST_COUNTRY'] == 'RU') ? GetMessage('APPROX_RUB'): KDXCurrency::GetCurrencyName(KDXCurrency::$CurrentCurrency)?>
                    <?$useVAT = $arrCountries[$_SESSION['LAST_COUNTRY']]['UF_USE_VAT'];?>

                    <?foreach($arResult['REAL_SIZES'] as $arSKU){?>
                        <div data-product-price="<?=$arSKU['ID']?>" style="display: none">
                            <div class="price__wrapper price__wrapper--product">
                                <div class="price <?if($arSKU['BASE_PRICE'] > $arSKU['RETAIL_PRICE']){?>price--has-discount"<?}?>>
                                    <span class="price__block">
                                        <?$curPrice = KDXCurrency::convert($useVAT=="N" ? $arSKU['RETAIL_PRICE'] / 1.21 : $arSKU['RETAIL_PRICE'], KDXCurrency::$CurrentCurrency);?>
                                        <span class="price--current"><?printf("%3.2f",KDXCurrency::convert($useVAT=="N" ? $arSKU['RETAIL_PRICE'] / 1.21 : $arSKU['RETAIL_PRICE'], $curCurrency))?></span>
                                        <span class="price--currency"><?=KDXCurrency::GetCurrencyName($curCurrency)?></span>
                                    </span>
                                    <?if($arSKU['BASE_PRICE'] > $arSKU['RETAIL_PRICE']){?>
                                        <span class="price--old">
                                            <span><?printf("%3.2f",KDXCurrency::convert($arSKU['BASE_PRICE'], $curCurrency))?></span>
                                            <span class="price--currency"><?=KDXCurrency::GetCurrencyName($curCurrency)?></span>
                                        </span>
                                    <?}?>
                                </div>
                            </div>
                            <?$approx_rub_price = $arSKU['RETAIL_PRICE'] / 1.21;?>
                            <div class="txt--grey">
                                <?if(KDXCurrency::$CurrentCurrency != "EUR"){?>
                                    <div><?=GetMessage('CHARGE')?> <a href="/help/delivery-and-payment/"><?=GetMessage('CHARGE_DETAIL')?></a></div>
                                <?}?>
                                <div class="price__wrapper price__wrapper--product hidden">
                                    <div class="price">
                                    <span class="price__block">
                                        <span class="price--current"><?printf("%3.2f",KDXCurrency::convert($approx_rub_price, $cur_type))?></span>
                                        <span class="price--currency"><?=$cur_name?></span>
                                        <span><?=GetMessage('APPROX_EX_VAT')?></span>
                                    </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?}?>
                    <div data-product-price="ALL">
                        <div class="price__wrapper price__wrapper--product">
                            <div class="price <?if($arResult['BASE_PRICE_MIN'] > $arResult['RETAIL_PRICE_MIN']){?>price--has-discount<?}?>">
                                <span class="price__block">
                                    <span class="price--current"><?printf("%3.2f",KDXCurrency::convert($useVAT=="N" ? $arSKU['RETAIL_PRICE'] / 1.21 : $arSKU['RETAIL_PRICE'], $curCurrency))?></span>
                                    <span class="price--currency"><?=KDXCurrency::GetCurrencyName($curCurrency)?></span>
                                </span>
                                <?if($arResult['BASE_PRICE_MIN'] > $arResult['RETAIL_PRICE_MIN']){?>
                                    <span class="price--old">
                                        <span><?printf("%3.2f",KDXCurrency::convert($useVAT=="N" ? $arSKU['BASE_PRICE'] / 1.21 : $arSKU['BASE_PRICE'], $curCurrency))?></span>
                                        <span class="price--currency"><?=KDXCurrency::GetCurrencyName($curCurrency)?></span>
                                    </span>
                                <?}?>
                            </div>
                        </div>
                        <?$approx_rub_price = $arResult['RETAIL_PRICE_MIN'] / 1.21;?>
                        <div class="txt--grey hidden">
                            <div class="price__wrapper price__wrapper--product">
                                <div class="price">
                                    <span class="price__block">
                                        <span class="price--current"><?printf("%3.2f",KDXCurrency::convert($approx_rub_price, $cur_type))?></span>
                                        <span class="price--currency"><?=$cur_name?></span>
                                        <span><?=GetMessage('APPROX_EX_VAT')?></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?}/*else{?>
                    <span class="sold_out"><?=GetMessage('SOLD')?></span>
                <?}*/?>
            </div>

            <div class="helper--md-margin-top"></div>

            <div class="grid-row">
                    <?$productID='';
                    if(count($arResult['REAL_SIZES']) == 1){
                        $arSKU = reset($arResult['REAL_SIZES']);
                        $productID = $arSKU['ID'];
                        if(!in_array($arSKU['PROPERTY_SIZE_VALUE'],KDXSettings::getSetting('NOT_SHOW_SIZES'))){?>
                            <div class="col-sm-12 col-xl-12">
                                <div class="form-row">
                            <?if($arSKU['CATALOG_QUANTITY'] > 0){?>
                                    <select name="product_size" class="form_select_v1 chosen-select js-chosen-select form-input" data-placeholder="<?=$arSKU['PROPERTY_SIZE_VALUE']?>">
                                        <option value="<?=$arSKU['ID']?>" <?if($arSKU['CATALOG_QUANTITY'] <= 0){?>disabled="disabled" <?}?>><?=$arSKU['PROPERTY_SIZE_VALUE']?></option>
                                    </select>
                                <?}?>
                                </div>
                            </div>
                        <?}
                    }elseif(count($arResult['REAL_SIZES']) > 1){
                        $showBuyButton = false;
                        ?>
                        <div class="col-sm-12 col-xl-12">
                            <div class="form-row">
                                <select name="product_size" class="form_select_v1 chosen-select js-chosen-select form-input">
                                    <?foreach($arResult['REAL_SIZES'] as $arSKU){?>
                                        <?
                                        $skuSize = '';
                                        if($arSKU['CATALOG_QUANTITY'] > 0) $showBuyButton = true;
                                        if (/*$USER->isAdmin() && */!empty($arSKU['PROPERTY_SIZE_EU_VALUE'])){
                                            if ($arResult['DEFAULT_SIZE'] == 'US'){
                                                $skuSize = $arSKU['PROPERTY_SIZE_VALUE'].' / '.$arSKU['PROPERTY_SIZE_EU_VALUE'].' EU';
                                            } else {
                                                $skuSize = $arSKU['PROPERTY_SIZE_EU_VALUE'].' EU / '.$arSKU['PROPERTY_SIZE_VALUE'];
                                            }
                                        } else {
                                            $skuSize = $arSKU['PROPERTY_SIZE_VALUE'];
                                        }
                                        ?>
                                        <option value="<?=$arSKU['ID']?>" <?if($arSKU['CATALOG_QUANTITY'] <= 0){?>disabled="disabled" <?}?>><?=$skuSize?></option>
                                    <?}?>
                                </select>
                            </div>
                        </div>
                    <?}?>
                <div class="col-sm-12 col-xl-12">
                    <?if($arSKU['CATALOG_QUANTITY'] > 0 || $showBuyButton){?>
                        <?
                            $nav = CIBlockSection::GetNavChain(false, $arResult['IBLOCK_SECTION_ID']);
                            $path = '/catalog';
                            foreach ($nav as $arSectionPath){
                                foreach ($arSectionPath as $p){
                                    $path .= '/'.$p['CODE'];
                                }
                            }
                            $brandId = null;
                            $iterator = CIBlockElement::GetProperty(1, $arResult['ID'],array(),array('ID' => 1));
                            while ($row = $iterator->Fetch())
                            {
                              $brandId = $row['VALUE'];
                            }
                            if (!is_null($brandId)) {
                                $i = 0;
                                $brands = '';
                                $dbBrands = CIBlockElement::GetList(Array("SORT"=>"ASC"), Array("IBLOCK_ID"=>3,"ID"=>$brandId));
                                while($arBrand = $dbBrands->GetNext())
                                {
                                    // if ($i > 0) {
                                    //     $brands .= ','.$arBrand['NAME'];
                                    // } else {
                                    //     $brands .= $arBrand['NAME'];
                                    // }
                                    // $i++;
                                    $brands = $arBrand['NAME'];
                                    break;
                                }
                            }
                        ?>
                        <div class="form-row">
                            <a href="#" title="#" class="btn btn--primary js-ajax-btn kdxAddToCart" data-product-id="<?=$productID?>" data-quantity="1" data-ok-text="<?=GetMessage('IN_CART')?>" data-error-text="<?=GetMessage('ADD_ERROR')?>" data-origin-text="<?=GetMessage('TO_CART')?>" data-rr-id="<?=intval($arResult["ID"])?>" onclick="addProductToCartDataLayer()"><?=GetMessage('TO_CART')?></a>
                        </div>
                        <?
                        global $USER;
                        if ($USER->IsAdmin()){?>
                            <script src="https://www.paypal.com/sdk/js?client-id=AUqeUL94_UCABd5TnRBDSpJie58AWO4wOvjoj-PMaqY9hdw_ag8aIJM6gYUeccTRh0V_KjGH0TJ_UVMJ"></script>
                            <div id="paypal-button-container"></div>
                            <script>
                                paypal.Buttons({
                                    createOrder: function(data, actions) {
                                        // This function sets up the details of the transaction, including the amount and line item details.
                                        return actions.order.create({
                                            purchase_units: [{
                                                amount: {
                                                    value: '<?=$curPrice?>'
                                                }
                                            }]
                                        });
                                    },
                                    onApprove: function(data, actions) {
                                        return actions.order.capture().then(function(details) {
                                            console.log(details);
                                            if (details.status == 'COMPLETED'){
                                                $.post('/local/app/pp_fastOrder.php?productId=' + <?=$arResult['ID']?> + '&purchase=' + details.purchase_units[0]['amount']['value'] + '&name=' + details.payer['name']['given_name'] + '&surname=' + details.payer['name']['surname'] + '&email=' + details.payer['email_address'] + '&city=' + details.purchase_units[0]['shipping']['address']['admin_area_2'] + '&address1=' + details.purchase_units[0]['shipping']['address']['address_line_1'] + '&address2=' + details.purchase_units[0]['shipping']['address']['address_line_2'] + '&postal_code=' + details.purchase_units[0]['shipping']['address']['postal_code'], function(data){
                                                    console.log(data);
                                                });
                                            }
                                        });
                                    }
                                }).render('#paypal-button-container');
                            </script>
                        <?}?>
                    <?}?>
                </div>
            </div>
            <div class="helper--md-margin-top"></div>

            <div class="product__desc ninja--sm ninja--md ninja--lg mobile-hide">
                <div class="tab__head">
                    <a class="tab__link" href="#" data-tab-name="product" data-tab-for="1" data-tab-current="true"><?=GetMessage('DESCRIPTION')?></a>
                    <a class="tab__link" href="#" data-tab-name="product" data-tab-for="2"><?=GetMessage('DELIVERY')?></a>
                    <a class="tab__link js-popup-size <?if(empty($arResult['SIZE_GRID'])){?>disabled<?}?>" href="#" data-tab-name="product" <?if(!empty($arResult['SIZE_GRID'])){?> data-popup-for="size_<?=$arResult["SIZE_GRID"]["IMAGE_ID"]["VALUE_XML_ID"]?>"<?}?>><?=GetMessage('GIDE')?></a>
                </div>
                <div class="tab__body">
                    <div class="tab__content" data-tab-name="product" data-tab-id="1" data-tab-show="true">
                        <?if(!empty($arResult['~DETAIL_TEXT'])) {
                            global $USER;
                            if ($USER->IsAdmin()){
                                ?><script data-voiced="player">!function(e,n,i,t,o,c,r,s){if(void 0!==e[t])return c();r=n.createElement(i),s=n.getElementsByTagName(i)[0],r.id=t,r.src="https://widget.speechki.org/js/common.min.js",r.async=1,s.parentNode.insertBefore(r,s),r.onload=c}(window,document,"script","Speechki",0,function(){Speechki.init()});</script><?
                            }
                            echo htmlspecialchars_decode($arResult['~DETAIL_TEXT']);
                        }else {
                            $APPLICATION->IncludeComponent(
                                "bitrix:main.include",
                                ".default",
                                array(
                                    "AREA_FILE_SHOW" => "sect",
                                    "AREA_FILE_SUFFIX" => "contacts",
                                    "AREA_FILE_RECURSIVE" => "Y",
                                ),
                                false
                            );
                        }
                        if ($arResult['PROPERTY_DONT_USE_DISCOUNT_VALUE'] == 'Да'){?>
                            <p><b><?=GetMessage('DONT_USE_DISCOUNT')?></b></p>
                        <?}?>
                    </div>
                    <div class="tab__content" data-tab-name="product" data-tab-id="2">
                        <ul class="ul--reset">
                        <?
                        foreach($arResult['DELIVERY'] as $arDelivery){
                            foreach($arDelivery['PROFILES'] as $profile => $arProfileParams){
//                                printr($arProfileParams);
                                if($arDelivery['SID'] == 'kdx_self') continue;
                                ?>
                                <li>
                                    <div <?/* class="radio"*/?>>
<!--                                        <input class="radio__input" type="radio" name="payment-method" checked>-->
<!--                                        <div class="radio__icon"></div>-->
                                        <span <?/*class="radio__text"*/?>><?=$arDelivery['NAME']?> -
                                            <?printf("%3.2f",KDXCurrency::convert($arProfileParams['PRICE']['VALUE'],KDXCurrency::$CurrentCurrency))?> <?=KDXCurrency::GetCurrencyName(KDXCurrency::$CurrentCurrency)?>
                                        </span>

                                        <span class="radio__text">
                                        <?=GetMessage('DELIVERY_TITLE')?> <?=declension($arProfileParams['PRICE']['TRANSIT'], GetMessage('DAYS_1'),GetMessage('DAYS_2'),GetMessage('DAYS_5'))?>
                                        <?=$arDelivery['DESCRIPTION']?></span>
                                    </div>
                                </li>
                            <?}
                        }
                        ?>
                        </ul>
                        <?/*$APPLICATION->IncludeComponent(
                            "bitrix:main.include",
                            ".default",
                            array(
                                "AREA_FILE_SHOW" => "sect",
                                "AREA_FILE_SUFFIX" => "delivery",
                                "AREA_FILE_RECURSIVE" => "Y",
                            ),
                            false
                        );*/?>
                    </div>
                    <div class="tab__content" data-tab-name="product" data-tab-id="3">
                        <?if(!empty($arResult['SIZE_GRID'])) {?>
                            <table class="size_grid_box">
                                <tr>
                                    <?if(!empty($arResult['SIZE_GRID']['GRID_PIC']['SRC'])){?>
                                        <td><img src="<?=$arResult['SIZE_GRID']['GRID_PIC']['SRC']?>"></td>
                                    <?}?>
                                    <td valign="middle"><?=$arResult['SIZE_GRID']['DETAIL_TEXT']?></td>
                                </tr>
                            </table>
                        <?}?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?/*<div class="helper--md-margin-top ninja--xl"></div>*/?>
<div class="grid-row ninja--xl">
    <div class="col-sm-12">
        <?/*<div class="product__desc ninja--sm">
            <div class="tab__head">
                <a class="tab__link" href="#" data-tab-name="product" data-tab-for="1" data-tab-current="true"><?=GetMessage('DESCRIPTION')?></a>
                <a class="tab__link" href="#" data-tab-name="product" data-tab-for="2"><?=GetMessage('DELIVERY')?></a>
                <a class="tab__link <?if(empty($arResult['SIZE_GRID'])){?>disabled<?}?>" href="#" data-tab-name="product" <?if(!empty($arResult['SIZE_GRID'])){?> data-popup-for="size_<?=$arResult["SIZE_GRID"]["IMAGE_ID"]["VALUE_XML_ID"]?>"<?}?>><?=GetMessage('GIDE')?></a>
            </div>
            <div class="tab__body">
                <div class="tab__content" data-tab-name="product" data-tab-id="1" data-tab-show="true">
                    <?if(!empty($arResult['~DETAIL_TEXT'])) {
                        echo htmlspecialchars_decode($arResult['~DETAIL_TEXT']);
                    }else {
                        $APPLICATION->IncludeComponent(
                            "bitrix:main.include",
                            ".default",
                            array(
                                "AREA_FILE_SHOW" => "sect",
                                "AREA_FILE_SUFFIX" => "contacts",
                                "AREA_FILE_RECURSIVE" => "Y",
                            ),
                            false
                        );
                    }?>
                </div>
                <div class="tab__content" data-tab-name="product" data-tab-id="2">
 <ul class="ul--reset">
                        <?
                        foreach($arResult['DELIVERY'] as $arDelivery){
                            foreach($arDelivery['PROFILES'] as $profile => $arProfileParams){
//                                printr($arProfileParams);
                                if($arDelivery['SID'] == 'kdx_self') continue;
                                ?>
                                <li>
                                    <div>
                                        <span><?=$arDelivery['NAME']?> -
                                            <?printf("%3.2f",KDXCurrency::convert($arProfileParams['PRICE']['VALUE'],KDXCurrency::$CurrentCurrency))?> <?=KDXCurrency::GetCurrencyName(KDXCurrency::$CurrentCurrency)?>
                                        </span>

                                        <span class="radio__text">
                                        <?=GetMessage('DELIVERY_TITLE')?> <?=declension($arProfileParams['PRICE']['TRANSIT'], GetMessage('DAYS_1'),GetMessage('DAYS_2'),GetMessage('DAYS_5'))?>
                                        <?=$arDelivery['DESCRIPTION']?></span>
                                    </div>
                                </li>
                            <?}
                        }
                        ?>
                        </ul>

                </div>
                <div class="tab__content" data-tab-name="product" data-tab-id="3">
                    <?if(!empty($arResult['SIZE_GRID'])) {?>
                        <table class="size_grid_box">
                            <tr>
                                <?if(!empty($arResult['SIZE_GRID']['GRID_PIC']['SRC'])){?>
                                    <td><img src="<?=$arResult['SIZE_GRID']['GRID_PIC']['SRC']?>"></td>
                                <?}?>
                                <td valign="middle"><?=$arResult['SIZE_GRID']['DETAIL_TEXT']?></td>
                            </tr>
                        </table>
                    <?}?>
                </div>
            </div>
        </div>*/?>
        <div class="accordion product__desc ninja--md ninja--lg">
            <div class="accordion__item js-accordion__item accordion__item--opened">
                <div class="accordion__title js-accordion__title"><?=GetMessage('DESCRIPTION')?></div>
                <div class="accordion__content js-accordion__content">
                    <?if(!empty($arResult['~DETAIL_TEXT'])) {
                        echo htmlspecialchars_decode($arResult['~DETAIL_TEXT']);
                    }else {
                        $APPLICATION->IncludeComponent(
                            "bitrix:main.include",
                            ".default",
                            array(
                                "AREA_FILE_SHOW" => "sect",
                                "AREA_FILE_SUFFIX" => "contacts",
                                "AREA_FILE_RECURSIVE" => "Y",
                            ),
                            false
                        );
                    }?>
                    <?if ($arResult['PROPERTY_DONT_USE_DISCOUNT_VALUE'] == 'Да'){?>
                        <br><br><p><b><?=GetMessage('DONT_USE_DISCOUNT')?></b></p>
                    <?}?>
                </div>
            </div>
            <div class="accordion__item js-accordion__item">
                <div class="accordion__title js-accordion__title"><?=GetMessage('DELIVERY')?></div>
                <div class="accordion__content js-accordion__content">
 <ul class="ul--reset">
                        <?
                        foreach($arResult['DELIVERY'] as $arDelivery){
                            foreach($arDelivery['PROFILES'] as $profile => $arProfileParams){
//                                printr($arProfileParams);
                                if($arDelivery['SID'] == 'kdx_self') continue;
                                ?>
                                <li>
                                    <div <?/* class="radio"*/?>>
<!--                                        <input class="radio__input" type="radio" name="payment-method" checked>-->
<!--                                        <div class="radio__icon"></div>-->
                                        <span <?/*class="radio__text"*/?>><?=$arDelivery['NAME']?> -
                                            <?printf("%3.2f",KDXCurrency::convert($arProfileParams['PRICE']['VALUE'],KDXCurrency::$CurrentCurrency))?> <?=KDXCurrency::GetCurrencyName(KDXCurrency::$CurrentCurrency)?>
                                        </span>

                                        <span class="radio__text">
                                        <?=GetMessage('DELIVERY_TITLE')?> <?=declension($arProfileParams['PRICE']['TRANSIT'], GetMessage('DAYS_1'),GetMessage('DAYS_2'),GetMessage('DAYS_5'))?>
                                        <?=$arDelivery['DESCRIPTION']?></span>
                                    </div>
                                </li>
                            <?}
                        }
                        ?>
                        </ul>

                </div>
            </div>
            <?if(!empty($arResult['SIZE_GRID'])) {?>
                <div class="accordion__item js-accordion__item">
                    <div class="accordion__title js-accordion__title" data-popup-for="size_<?=$arResult["SIZE_GRID"]["IMAGE_ID"]["VALUE_XML_ID"]?>">
                        <?=GetMessage('GIDE')?>
                    </div>
                </div>
            <?}?>
        </div>
    </div>
</div>
<div class="helper--md-margin-top"></div>

<?/*<div class="grid-row">
    <div class="col-sm-12">
        <div class="tab__head tab__head-mobile">
            <a class="tab__link font-title title--additionally" href="#" data-tab-name="additionally" data-tab-for="1" data-tab-current="true"><?=GetMessage('RECOMMENDED')?></a>
            <a class="tab__link font-title title--additionally" href="#" data-tab-name="additionally" data-tab-for="2"><?=GetMessage('YOU_WATCHED')?></a>
            <a class="tab__link font-title title--additionally" href="#" data-tab-name="additionally" data-tab-for="3"><?=GetMessage('MORE_PRODUCTS')?></a>
            <a class="tab__link font-title title--additionally" href="#" data-tab-name="additionally" data-tab-for="4"><?=GetMessage('REVIEWS')?></a>
        </div>
        <div class="tab__body tab__body-mobile">
            <div class="tab__content" data-tab-name="additionally" data-tab-id="1" data-tab-show="true">
                <div class="tab__content-accordion-title active"><?=GetMessage('RECOMMENDED')?></div>
                <div class="tab__content-accordion-content active related_items_detail_page"></div>
            </div>
            <div class="tab__content" data-tab-name="additionally" data-tab-id="2"  data-tab-show="true"  data-tab-hide-slick='true'>
                <div class="tab__content-accordion-title"><?=GetMessage('YOU_WATCHED')?></div>
                <div class="tab__content-accordion-content">
                    <?$APPLICATION->IncludeComponent(
                        "kodix:catalog.viewed.products",
                        "new",
                        array(
                            "PAGE_ELEMENT_COUNT" => "4",
                            "SECTION_ELEMENT_ID" => $arResult["ID"]
                        ),
                        false
                    );?>
                </div>
            </div>
            <div class="tab__content" data-tab-name="additionally" data-tab-id="3" data-tab-show="true" data-tab-hide-slick='true'>
                <div class="tab__content-accordion-title"><?=GetMessage('MORE_PRODUCTS')?></div>
                <div class="tab__content-accordion-content">
                    <?
                    $APPLICATION->IncludeComponent(
                        "kodix:catalog.list",
                        "more_products",
                        array(
                            "IBLOCK_TYPE" => "kodix_catalog",
                            "CATALOG_IBLOCK_ID" => KDXSettings::getSetting('CATALOG_IBLOCK_ID'),
                            "SORT_BY1" => "SORT",
                            "SORT_ORDER1" => "ASC",
                            "SORT_BY2" => "DATE_ACTIVE_FROM",
                            "SORT_ORDER2" => "DESC",
                            "LIMIT_ITEMS" => "4",
                            "PAGE_ITEMS_COUNT" => "4",
                            "FILTER_PRICE_PROP" => "RETAIL_PRICE_MIN",
                            'FILTER' => array("ACTIVE"=>"Y","PROPERTY_CML2_MANUFACTURER"=>$arResult["PROPERTY_CML2_MANUFACTURER_VALUE"][0]["ID"],"!ID"=>$arResult["ID"]),
                            'NOT_SHOW_ACTION' => 'N'
                        ),
                        false
                    );
                    ?>
                </div>
            </div>
            <div class="tab__content" data-tab-name="additionally" data-tab-id="4" data-tab-show="false">
                <div class="tab__content-accordion-title"><?=GetMessage('REVIEWS')?></div>
                <div class="tab__content-accordion-content">
                    <div class="product-comment">
                        <?if (!empty($arResult['REVIEWS'])){?>
                            <div class="comment-list">
                                <?foreach ($arResult['REVIEWS'] as $review){?>
                                    <div class="comment-list__item">
                                        <div class="comment-content">
                                            <div class="content-inner">
                                                <?if ($review['PROPS']['RATING']['VALUE'] > 0 && $review['PROPS']['RATING']['VALUE'] !== ''){?>
                                                    <div class="product-comment-list-rating">
                                                        <span class="_empty"></span>
                                                        <span class="_full" style="width: <?=(float)$review['PROPS']['RATING']['VALUE']*18?>px"></span>
                                                    </div>
                                                <?}?>
                                                <div class="name"><?=$review['NAME']?></div>
                                                <div class="text"><?=$review['~PREVIEW_TEXT']?></div>
                                                <div class="date">
                                                    <span><?=ConvertDateTime($review['ACTIVE_FROM'], "DD-MM-YYYY", "ru")?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?}?>
                            </div>
                        <?}?>
                        <?if ($USER->IsAuthorized()){?>
                            <h3><?=GetMessage('ADD_REVIEWS')?></h3>
                            <?
                            global $USER;
                            $rsUser = CUser::GetByID($USER->GetParam('USER_ID'));
                            $arUser = $rsUser->Fetch();
                            ?>
                            <form class="review-form js-review-form" action="<?=POST_FORM_ACTION_URI?>" method="post" enctype="multipart/form-data">
                                <input type="hidden" name="act" value="addProductReview">
                                <input type="hidden" name="product_id" value="<?=$arResult['ID']?>">
                                <div class="product-comment-rating js-rating">
                                    <span class="_empty"></span>
                                    <span class="_full" style="width: 0"></span>
                                    <input type="hidden" class="js-rating-val" name="rating_all" value="0">
                                </div>
                                <div class="form-row form-row--half form-row--lg-gap">
                                    <input type="text" name="f_name" class="f_name form-input required" placeholder="<?=GetMessage('YOUR_NAME')?>" value="">
                                    <input type="text" name="f_email" class="f_email form-input required" placeholder="E-mail *" value="">
                                </div>
                                <div class="form-row form-row--lg-gap">
                                    <textarea name="f_comment" class="f_comment form-input required" placeholder="<?=GetMessage('YOUR_COMMENT')?>"></textarea>
                                </div>
                                <input type="submit" value="<?=GetMessage('R_SEND')?>" class="btn btn--secondary btn--inline btn--block-md js-review-btn">
                            </form>
                        <?} else {?>
                            <p><?=GetMessage('AUTHORISE')?></p>
                        <?}?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>*/?>
<?/*<div class="grid-row ninja--xl">
    <div class="col-sm-12 mobile_related_items_detail_page"></div>

    <div class="col-sm-12">
        <?$APPLICATION->IncludeComponent(
            "kodix:catalog.viewed.products",
            "mobile_new",
            array(
                "PAGE_ELEMENT_COUNT" => "8",
                "SECTION_ELEMENT_ID" => $arResult["ID"]
            ),
            false
        );?>
    </div>
    <div class="col-sm-12">
        <?
        $APPLICATION->IncludeComponent(
            "kodix:catalog.list",
            "more_products_mobile",
            array(
                "IBLOCK_TYPE" => "kodix_catalog",
                "CATALOG_IBLOCK_ID" => KDXSettings::getSetting('CATALOG_IBLOCK_ID'),
                "SORT_BY1" => "SORT",
                "SORT_ORDER1" => "ASC",
                "SORT_BY2" => "DATE_ACTIVE_FROM",
                "SORT_ORDER2" => "DESC",
                "LIMIT_ITEMS" => "8",
                "PAGE_ITEMS_COUNT" => "8",
                "FILTER_PRICE_PROP" => "RETAIL_PRICE_MIN",
                'FILTER' => array("ACTIVE"=>"Y","PROPERTY_CML2_MANUFACTURER"=>$arResult["PROPERTY_CML2_MANUFACTURER_VALUE"][0]["ID"],"!ID"=>$arResult["ID"]),
                'NOT_SHOW_ACTION' => 'Y'
            ),
            false
        );
        ?>
    </div>
    <div class="col-sm-12">
        <div class="accordion additionally__desc ninja--md ninja--lg">
            <div class="accordion__item js-accordion__item">
                <div class="accordion__title js-accordion__title title--additionally"><?=GetMessage('REVIEWS')?></div>
                <div class="accordion__content js-accordion__content">
                    <div class="grid-row">
                        <div class="col-sm-12">
                            <div class="product-comment">
                                <?if (!empty($arResult['REVIEWS'])){?>
                                    <div class="comment-list">
                                        <?foreach ($arResult['REVIEWS'] as $review){?>
                                            <div class="comment-list__item">
                                                <div class="comment-content">
                                                    <div class="content-inner">
                                                        <?if ($review['PROPS']['RATING']['VALUE'] > 0 && $review['PROPS']['RATING']['VALUE'] !== ''){?>
                                                            <div class="product-comment-list-rating">
                                                                <span class="_empty"></span>
                                                                <span class="_full" style="width: <?=(float)$review['PROPS']['RATING']['VALUE']*18?>px"></span>
                                                            </div>
                                                        <?}?>
                                                        <div class="name"><?=$review['NAME']?></div>
                                                        <div class="text"><?=$review['~PREVIEW_TEXT']?></div>
                                                        <div class="date">
                                                            <span><?=ConvertDateTime($review['ACTIVE_FROM'], "DD-MM-YYYY", "ru")?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?}?>
                                    </div>
                                <?}?>
                                <?if ($USER->IsAuthorized()){?>
                                    <h3><?=GetMessage('ADD_REVIEWS')?></h3>
                                    <?
                                    global $USER;
                                    $rsUser = CUser::GetByID($USER->GetParam('USER_ID'));
                                    $arUser = $rsUser->Fetch();
                                    ?>
                                    <form class="review-form js-review-form" action="<?=POST_FORM_ACTION_URI?>" method="post" enctype="multipart/form-data">
                                        <input type="hidden" name="act" value="addProductReview">
                                        <input type="hidden" name="product_id" value="<?=$arResult['ID']?>">
                                        <div class="product-comment-rating js-rating">
                                            <span class="_empty"></span>
                                            <span class="_full" style="width: 0"></span>
                                            <input type="hidden" class="js-rating-val" name="rating_all" value="0">
                                        </div>
                                        <div class="form-row form-row--half form-row--lg-gap">
                                            <input type="text" name="f_name" class="f_name form-input required" placeholder="<?=GetMessage('YOUR_NAME')?>" value="">
                                            <input type="text" name="f_email" class="f_email form-input required" placeholder="E-mail *" value="">
                                        </div>
                                        <div class="form-row form-row--lg-gap">
                                            <textarea name="f_comment" class="f_comment form-input required" placeholder="<?=GetMessage('YOUR_COMMENT')?>"></textarea>
                                        </div>
                                        <input type="submit" value="<?=GetMessage('R_SEND')?>" class="btn btn--secondary btn--inline btn--block-md js-review-btn">
                                    </form>
                                <?} else {?>
                                    <p><?=GetMessage('AUTHORISE')?></p>
                                <?}?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>*/?>

<script>
BX.message['CHOOSE_SIZE']='<?=GetMessage('CHOOSE_SIZE')?>';
BX.message['SOLD']='<?=GetMessage('SOLD')?>';
BX.message['TO_CART']='<?=GetMessage('TO_CART')?>';
BX.message['IN_CART']='<?=GetMessage('IN_CART')?>';
BX.message['IN_CART_LINK']='<?=GetMessage('IN_CART_LINK')?>';
BX.message['MAX_QUANTITY_ADDED']='<?=GetMessage('MAX_QUANTITY_ADDED')?>';
</script>

<script type="text/javascript">
    KDXSale.PROD_ID = <?=$arResult['ID']?>;
    rrApiOnReady.push(function() {
        try{ rrApi.view(KDXSale.PROD_ID); } catch(e) {}
    });

    $(document).ready(function(){
        $.ajax({
            type: "post",
            url: "/ajax/RetailRocket.php",
            data: {query: 'UpSellItemToItems', rr_params: KDXSale.PROD_ID, temp:'new'},
            success: function(data){
                $('.related_items_detail_page').html(data);
            }
        });
        $.ajax({
            type: "post",
            url: "/ajax/RetailRocket.php",
            data: {query: 'UpSellItemToItems', rr_params: KDXSale.PROD_ID, temp:'mobile_new'},
            success: function(data){
                $('.mobile_related_items_detail_page').html(data);
            }
        });

        $('.tab__content-accordion-title').on('click', function(e){
            e.preventDefault();
            if ($(this).hasClass('active')){
                $(this).removeClass('active');
                $(this).next('.tab__content-accordion-content').slideUp(200);
            } else {
                $(this).addClass('active');
                $(this).next('.tab__content-accordion-content').slideDown(200);
            }
        })
    })
</script>

<?ob_start()?>
<?if(!empty($arResult["SIZE_GRID"])) {?>
    <div class="popup" data-popup-show="init" data-popup-id="size_<?=$arResult["SIZE_GRID"]["IMAGE_ID"]["VALUE_XML_ID"]?>">
        <div class="popup__overlay" data-popup-bg></div>
        <div class="js_customscroll popup__content <?if($arResult["SIZE_GRID"]['SIZES_VALUES']['VALUE']) {?>popup__content--size<?}?>">
            <div class="popup__inner">
                <div class="popup__close" data-popup-close>
                    <svg class="icon icon-cross_pop-up">
                        <use xlink:href="<?=SITE_TEMPLATE_PATH?>/resources/svg/app.svg#cross_pop-up"></use>
                    </svg>
                </div>
                <h2 class="auth__heading"><?=GetMessage('SCHEME_TITLE')?></h2>
                <?if($arResult["SIZE_GRID"]['SIZES_VALUES']['VALUE']){?>
                    <div class="size__wrap" data-size-id="0332" data-size-units="cm" data-size-size="xs" data-size-type="<?=$arResult["SIZE_GRID"]["IMAGE_ID"]["VALUE_XML_ID"]?>">
                        <div class="size__left">
                            <div class="size__units-on-img">
                                <div class="size__units">
                                    <span class="size__units-title"><?=GetMessage('SCHEME_UNITS')?></span>
                                    <div class="size__units-item">
                                        <label class="radio">
                                            <input class="radio__input" type="radio" value="cm" name="size-units" checked>
                                            <div class="radio__icon"></div>
                                            <div class="radio__label">
                                                <span><?=GetMessage('SCHEME_UNIT_CM')?></span>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="size__units-item">
                                        <label class="radio">
                                            <input class="radio__input" type="radio" value="inch" name="size-units">
                                            <div class="radio__icon"></div>
                                            <div class="radio__label">
                                                <span><?=GetMessage('SCHEME_UNIT_INCH')?></span>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="size__labels">
                            <?foreach(current($arResult["SIZE_GRID"]["ARR_SIZE_GRID_VALUES"]) as $k => $arr_sizes) {?>
                                <div class="size__label size__label--<?=$k?>">
                                    <div class="size__label-num" data-size-<?=$k?>><?=$arr_sizes['cm']?>cm</div>
                                    <div class="size__label-txt"><?=GetMessage('SCHEME_'.strtoupper($k))?:$k?></div>
                                </div>
                            <?}?>
                            </div>
                            <div class="size__background size__background--top" <?if(!empty($arResult["SIZE_GRID"]["IMAGE_ID"]["CUSTOM_IMG"])):?>style="background-image: url(<?=$arResult["SIZE_GRID"]["IMAGE_ID"]["CUSTOM_IMG"]?>)"<?endif?>></div>
                        </div>
                        <div class="size__right">
                            <div class="size__select">
                                <div class="size__title"><?=GetMessage('SCHEME_SELECT_SIZE')?></div>
                                <div class="size__select-wrap">
                                    <?foreach($arResult["SIZE_GRID"]['SIZES_VALUES']['DESCRIPTION'] as $size_value){?>
                                        <div class="size__select-item">
                                            <label class="radio">
                                                <input class="radio__input" type="radio" value="<?=$size_value?>" name="size-select" checked>
                                                <div class="radio__icon"></div>
                                                <div class="radio__label">
                                                    <span><?=$size_value?></span>
                                                </div>
                                            </label>
                                        </div>
                                    <?}?>
                                </div>
                            </div>
                            <div class="accordion">
                                <div class="accordion__item js-accordion__item accordion__item--opened">
                                    <div class="accordion__title js-accordion__title"><?=GetMessage('SCHEME_MEASUREMENT')?></div>
                                    <div class="accordion__content js-accordion__content">
                                        <table class="size__table" data-size-table>
                                            <thead>
                                            <tr>
                                                <th>&nbsp;</th>
                                                <?foreach($arResult["SIZE_GRID"]['SIZES_VARIANTS']['SORT_VALUE'] as $key => $size_variants) {?>
                                                    <th><?=GetMessage('SCHEME_'.strtoupper($key))?:$size_variants?></th>
                                                <?}?>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?foreach($arResult["SIZE_GRID"]["ARR_SIZE_GRID_VALUES"] as $k => $arr_sizes) {?>
                                                <tr data-size-tr="<?=$k?>">
                                                    <th><?=$k?></th>
                                                    <?foreach($arr_sizes as $kk => $arr_sizes_variants) {?>
                                                        <td data-size-td="<?=$kk?>">1</td>
                                                    <?}?>
                                                </tr>
                                            <?}?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <?if(!empty($arResult["SIZE_GRID"]["DETAIL_TEXT"])) {?>
                                    <div class="accordion__item js-accordion__item">
                                        <div class="accordion__title js-accordion__title"><?=GetMessage('SCHEME_DESCRIPTION')?></div>
                                        <div class="accordion__content js-accordion__content">
                                            <?=$arResult["SIZE_GRID"]["DETAIL_TEXT"]?>
                                        </div>
                                    </div>
                                <?}?>
                            </div>
                        </div>
                    </div>
                </div>
                <?} else {?>
                    <?if(!empty($arResult['SIZE_GRID']['GRID_PIC']['SRC'])){?>
                        <td><img src="<?=$arResult['SIZE_GRID']['GRID_PIC']['SRC']?>"></td>
                    <?}else{?>
                        <?=$arResult['SIZE_GRID']['DETAIL_TEXT']?>
                    <?}?>
                <?}?>
            </div>
        </div>
    </div>
<?}?>
<?
$protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === 0 ? 'https://' : 'http://';
$domain = $protocol.$_SERVER['SERVER_NAME'];
$newEndingDate = date("Y-m-d", strtotime(date("Y-m-d", strtotime(date('Y-m-d'))) . " + 1 year"));
?>
<script type="application/ld+json">
{
  "@context": "https://schema.org/",
  "@type": "Product",
  "name": "<?=$arResult['NAME']?>",
  "image": [
    <?echo '"'.CFile::ResizeImageGet($arResult['GALLERY'][0],array('width'=>737,'height'=>737),BX_RESIZE_IMAGE_PROPORTIONAL)['src'].'"';?>
   ],
  "description": "<?=$APPLICATION->GetPageProperty('description')?>",
  "brand": {
    "@type": "Thing",
    "name": "<?=$arResult["CML2_MANUFACTURER"][0]["NAME"]?>"
  },
  "offers": {
    "@type": "Offer",
    "url": "<?=$domain.$arResult['DETAIL_PAGE_URL']?>",
    <?if (LANGUAGE_ID == 'ru'){?>
        "priceCurrency": "RUB",
        <?if ($arResult['HAVE_REAL_SIZES']){?>
            "price": "<?printf("%3.2f",KDXCurrency::convert($useVAT=="N" ? $arSKU['RETAIL_PRICE'] / 1.21 : $arSKU['RETAIL_PRICE'], 'RUB'))?>",
        <?} else {
            $resOffersList = CCatalogSKU::GetInfoByProductIBlock($arResult['IBLOCK_ID']);
            if (is_array($resOffersList)){
                $rsOffers = CIBlockElement::GetList(array('PRICE'=>'DESC'),array('IBLOCK_ID'=>$resOffersList['IBLOCK_ID'], 'PROPERTY_'.$resOffersList['SKU_PROPERTY_ID']=>$arResult["ID"]));
                if ($arOffer = $rsOffers->GetNext()){
                    $arOfferPrice = GetCatalogProductPrice($arOffer["ID"], 1);?>
                    "price": "<?printf("%3.2f",KDXCurrency::convert($useVAT=="N" ? $arOfferPrice['PRICE'] / 1.21 : $arOfferPrice['PRICE'], 'RUB'));?>",
                <?}
            }
        }
    } else {?>
        "priceCurrency": "<?=KDXCurrency::$CurrentCurrency?>",
        "price": "<?printf("%3.2f",KDXCurrency::convert($useVAT=="N" ? $arSKU['RETAIL_PRICE'] / 1.21 : $arSKU['RETAIL_PRICE'], KDXCurrency::$CurrentCurrency))?>",
    <?}?>
    "priceValidUntil": "<?=$newEndingDate?>",
    "availability": "https://schema.org/InStock",
    "seller": {
      "@type": "Organization",
      "name": "Itk KIT"
    }
  },
  <?if (!empty($arResult['REVIEWS'])){
    $rating = 0;
    foreach ($arResult['REVIEWS'] as $review){
        $rating += $review['PROPS']['RATING']['VALUE'];?>
        "review": {
            "@type": "Review",
            "reviewRating": {
              "@type": "Rating",
              "ratingValue": "<?=$review['PROPS']['RATING']['VALUE']?>",
              "bestRating": "5"
            },
            "author": {
              "@type": "Person",
              "name": "<?=$review['NAME']?>"
            }
          },
    <?}?>
  <?}?>
  "aggregateRating": {
    "@type": "AggregateRating",
    "ratingValue": "<?=$rating / count($arResult['REVIEWS']);?>",
    "reviewCount": "<?=count($arResult['REVIEWS'])?>"
  }
}
</script>

<script>
    var name = "<?=$arResult["NAME"]?>";
    var newName = name.replace("'", "");

    function addProductToCartDataLayer() {
        dataLayer.push({
            event: 'eec.add',
            ecommerce: {
            add: {
              actionField: {
               list: 'Shopping cart'
              },
            products: [{
                id: '<?=$arResult["ID"]?>',
                price: '<?=$arSKU['RETAIL_PRICE']?>',
                name: newName,
                category: '<?=$path?>',
                brand: '<?=$brands?>',
                quantity: 1,
            }]
            }
          }
        });
    }

    function addProductInfoDataLayer() {
        dataLayer.push({ ecommerce: null });
        dataLayer.push({
              event: 'eec.detail',
              ecommerce: {
                detail: {
                  actionField: {
                    list: 'Product Card'
                  },
                  products: [{
                    id: '<?=$arResult['ID'];?>',
                    name: newName,
                    category: '<?=$path;?>',
                    brand: '<?=$brands;?>'
                  }]
                }
              }
            }
        );
    }
    addProductInfoDataLayer();
</script>

<? if ($arResult["SIZE_GRID"]["ARR_SIZE_GRID_VALUES"]) { ?>
<script>
$(document).ready(function(){
  var promise = {
    'el'  : '[data-size-id=0332]',
    'data': <?=json_encode($arResult["SIZE_GRID"]["ARR_SIZE_GRID_VALUES"])?>,
    'index': 1
  };
  new SizeITK(promise);
})
</script>
<? } ?>
<?$GLOBALS['product_size_grid'] = ob_get_clean();?>
