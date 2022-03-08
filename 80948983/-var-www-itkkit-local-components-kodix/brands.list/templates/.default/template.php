<?
if (SITE_ID === 's1') {
  $APPLICATION->SetPageProperty('title', 'Бренды, представленные в интернет-магазине itk');
  $APPLICATION->SetPageProperty('description', 'В интернет-магазине itk представлены тщательно отобранные мировые бренды. &#9989; Бесплатная доставка на все заказы свыше €350 &#9989; Быстрая доставка &#9989; 14 дневная политика возврата товаров &#9989; 100% оригинальный товар');
}
?>
<?if(!isAjax()){?>
    <div class="grid-row">
        <div class="col-sm-12"><h1 class="title--page"><?=GetMessage('KODIX_BRANDS')?></h1></div>
    </div>
    <div class="grid-row kdx_brands_ajax_wrapper">
<?}?>
    <?if (is_array($arResult['BRANDS']) && count($arResult['BRANDS']) > 0) {?>
        <?
        $i = 0; $first_russian = true;
        foreach($arResult['UPPERCASE_ALPHABET'] as $alpha) {
            if (is_array($arResult['BRANDS'][$alpha]) && count($arResult['BRANDS'][$alpha]) > 0) {?>
                <div class="col-lg-3 col-md-4 col-tm-6 col-sm-12">
                    <div class="brand-item">
                        <h2 id="alpha_<?=$alpha?>" class="brand-item__title title--product"><?=$alpha?></h2>
                        <ul class="ul--reset txt--lg-line">
                            <?foreach($arResult['BRANDS'][$alpha] as $brand) {?>
							<?
							print_r('<pre class="skdebug" style="display:none;">');
							print_r($brand);
							print_r('</pre>');
							?>
                                <li>
									<?if ($brand['available']) {?>
                                    <a href="<?=$brand['DETAIL_PAGE_URL']?>" class="link link--primary"><?=$brand['NAME']?></a>
									<?} else {?>
									<span class="unavailable_brand"><?=$brand['NAME']?></span>
									<?}?>
                                </li>
                            <?}?>
                        </ul>
                    </div>
                </div>
            <?}?>
        <?}?>
    <?}/*else{?>
         <?$APPLICATION->IncludeComponent(
            "bitrix:main.include",
            ".default",
            array(
                "AREA_FILE_SHOW"   => "file",
                "AREA_FILE_SUFFIX" => "",
                "EDIT_TEMPLATE"    => "",
                "PATH"             => "/include/zero_items_info.php"
            ),
            false
        ); ?>
    <?}*/?>

<?if(!isAjax()){?>
    </div>
<?}?>
