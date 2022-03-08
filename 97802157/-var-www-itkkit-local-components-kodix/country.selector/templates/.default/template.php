<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?
global $USER;
$hash = $USER->GetParam("PASSWORD_HASH");

$loginUrl = '';
if($hash && $USER->GetLogin())
{
    $loginUrl = '?user='.base64_encode($USER->GetLogin()).'&hash='.md5($hash);
}
?>

<script>
    console.info('$_SESSION[\'LAST_COUNTRY\']','<?=$_SESSION['LAST_COUNTRY']?>');
    console.info('$arResult["LAST_COUNTRY"]','<?=$arResult["LAST_COUNTRY"]?>');
    console.info('$_SESSION["KDX_CURRENCY"]','<?=$_SESSION["KDX_CURRENCY"]?>');
</script>

<div class="header-action__select">
    <div class="header-action__dd-for">
        <?/*<span class="header-action__dd-for-title"><?=LANGUAGE_ID == 'en' ? 'Ship to:' : 'Доставка в:';?></span>*/?>
        <span class="icon-flag icon-flag-<?=$arResult["LAST_COUNTRY"]?>"></span>
        <span><?=$arResult["LAST_COUNTRY"]?></span>
    </div>
    <div class="header-action__dd-id header-action__dd-id--big js_customscroll">
        <?foreach($arResult["MAIN_COUNTRIES"] as $arCountry){?>
            <a id="<?=$arCountry["UF_CONTRY_CODE"]?>" class="link link--primary header-nav__dd-item country-item" href="#<?=$arCountry["UF_CONTRY_CODE"]?>"
<?if($arCountry["UF_CONTRY_CODE"] == 'RU'){?>data-url="http://<?=$arResult["SITES"]['ru']['SERVER_NAME'].$_SERVER['REQUEST_URI'].''.$loginUrl?>"<?}elseif($arCountry["UF_CONTRY_CODE"] == 'UK'){?>data-url="http://<?=$arResult["SITES"]['en']['SERVER_NAME'].$_SERVER['REQUEST_URI'].''.$loginUrl?>"<?}?>>
                <span class="icon-flag icon-flag-<?=$arCountry["UF_CONTRY_CODE"]?>"></span>
                <span><?=LANGUAGE_ID == 'en' ? $arCountry["UF_NAME"] : $arCountry["UF_NAME_RU"]?></span>
            </a>
        <?}?>
        <select class="form-input country-item-select">
            <option value=""><?=GetMessage('ALL_COUNTRIES')?></option>
            <?foreach($arResult["ALL_COUNTRIES"] as $arAllCountry){?>
                <option value="#<?=$arAllCountry["UF_CONTRY_CODE"]?>" <?if($arAllCountry["UF_CONTRY_CODE"] == $_SESSION['LAST_COUNTRY']){?>selected="selected"<?}?>><?=LANGUAGE_ID == 'en' ? $arAllCountry["UF_NAME"] : $arAllCountry["UF_NAME_RU"]?></option>
            <?}?>
        </select>
    </div>
</div>

<?ob_start()?>
<?if (LANGUAGE_ID == 'ru') echo '<!--noindex-->';?>
<div class="popup" data-popup-show="init" data-popup-id="geo_conf">
    <div class="popup__overlay" data-popup-bg></div>
    <div class="js_customscroll popup__content">
        <div class="popup__inner">
            <div class="popup__close" data-popup-close>
                <svg class="icon icon-cross_pop-up">
                    <use xlink:href="<?=SITE_TEMPLATE_PATH?>/resources/svg/app.svg#cross_pop-up"></use>
                </svg>
            </div>
            <div class="auth__heading"><?=GetMessage('REGION')?></div>
            <div class="popup__2column">

                    <?
                    foreach($arResult["MAIN_COUNTRIES"] as $arCountry){?>
                            <a data-id="<?=$arCountry["UF_CONTRY_CODE"]?>" class="link link--primary popup__link country-item" href="#<?=$arCountry["UF_CONTRY_CODE"]?>"
<?if($arCountry["UF_CONTRY_CODE"] == 'RU'){?>data-url="http://<?=$arResult["SITES"]['ru']['SERVER_NAME'].$_SERVER['REQUEST_URI'].''.$loginUrl?>"<?}elseif($arCountry["UF_CONTRY_CODE"] == 'UK'){?>data-url="http://<?=$arResult["SITES"]['en']['SERVER_NAME'].$_SERVER['REQUEST_URI'].''.$loginUrl?>"<?}?>>
                                <span class="icon-flag icon-flag-<?=$arCountry["UF_CONTRY_CODE"]?>"></span>
                                <span><?=LANGUAGE_ID == 'en' ? $arCountry["UF_NAME"] : $arCountry["UF_NAME_RU"]?></span>
                            </a>
                        <?
                    }?>

            </div>
            <div class="grid-row">
                <div class="col-md-6 col-md-push-3 col-lg-8 col-lg-push-2">
                    <div class="helper--sm-margin-bottom"></div>
                    <select class="form-input country-item-select">
                        <option value=""><?=GetMessage('ALL_COUNTRIES')?></option>
                        <?foreach($arResult["ALL_COUNTRIES"] as $arAllCountry){?>
                            <option value="#<?=$arAllCountry["UF_CONTRY_CODE"]?>" <?if($arAllCountry["UF_CONTRY_CODE"] == $_SESSION['LAST_COUNTRY']){?>selected="selected"<?}?>><?=LANGUAGE_ID == 'en' ? $arAllCountry["UF_NAME"] : $arAllCountry["UF_NAME_RU"]?></option>
                        <?}?>
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>
<?if (LANGUAGE_ID == 'ru') echo '<!--/noindex-->';?>
<?$GLOBALS['geo_conf'] = ob_get_clean();?>
<?
$ufName = 'UF_NAME';
if(SITE_ID !== 'en')
    $ufName = 'UF_NAME_RU';

?>
<?if($_SESSION["SHOW_GEO_POPUP"] != "Y" && $arResult["SHOW_GEO_POPUP"]){?>
    <script>
        $(document).ready(function(){

            var popup_id = 'geo';
            var country_name = '<?=!empty($arResult["MAIN_COUNTRIES"][$arResult["LAST_COUNTRY"]]) ? $arResult["MAIN_COUNTRIES"][$arResult["LAST_COUNTRY"]][$ufName] : $arResult["ALL_COUNTRIES"][$arResult["LAST_COUNTRY"]][$ufName]?>';

            if(country_name.length > 0) {
                $('.country-name').text(country_name);
                $('.country__name').attr('data-country-name', '<?=$arResult["LAST_COUNTRY"]?>');
            }

            openPopup(popup_id);

            $(document).trigger('kdxInitPlugins');

            //event.preventDefault();
        })
    </script>

    <?$_SESSION["SHOW_GEO_POPUP"] = "Y";?>
<?}?>

<script>
    $(document).ready(function(){
    var country_name_ =
    $('.country-name_').text('<?=!empty($arResult["MAIN_COUNTRIES"][$arResult["LAST_COUNTRY"]]) ? $arResult["MAIN_COUNTRIES"][$arResult["LAST_COUNTRY"]][$ufName] : $arResult["ALL_COUNTRIES"][$arResult["LAST_COUNTRY"]][$ufName]?>');
    })
</script>

