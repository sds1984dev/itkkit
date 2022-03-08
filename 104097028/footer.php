<?Bitrix\Main\Localization\Loc::loadLanguageFile(__FILE__);?>
<?if($APPLICATION->GetProperty('NOT_SHOW_LEFT_MENU','N') !== 'Y'){
    $GLOBALS['CAN_SHOW_LEFT_MENU'] = true;
    ob_start();
    $APPLICATION->IncludeComponent(
        "bitrix:menu",
        "kit_left_new",
        array(
            "ROOT_MENU_TYPE" => "left",
            "MENU_CACHE_TYPE" => "A",
            "MENU_CACHE_TIME" => "3600",
            "MENU_CACHE_USE_GROUPS" => "Y",
            "MENU_CACHE_GET_VARS" => array(
            ),
            "MAX_LEVEL" => "1",
            "CHILD_MENU_TYPE" => "left",
            "USE_EXT" => "N",
            "DELAY" => "N",
            "ALLOW_MULTI_SELECT" => "N"
        ),
        false
    );
    $GLOBALS['LEFT_MENU'] = ob_get_clean();
}?>
<?if($APPLICATION->GetProperty('NOT_SHOW_PAGE_WRAPPER') !== 'Y'){
ob_start();?>
<div class="<?=$APPLICATION->GetProperty('BLOCK_CLASS','grid-row')?>">
    <?=$GLOBALS['LEFT_MENU']?>
    <div class="col-lg-12 col-xl-9 <?if($APPLICATION->GetProperty('NOT_SHOW_LEFT_MENU') == 'Y'){?>full_size<?}?>">
<? $PAGE_WRAPPER = ob_get_clean();?>

    </div><!-- col-lg-12 col-xl-9 -->
</div><!-- grid-row -->
<!--<div class="recommend_personal"></div>-->
<?}?>
<?$APPLICATION->ShowViewContent('BREADCRUMB')?>
    <?if($APPLICATION->GetCurPage()!=SITE_DIR){?>
        </div><!-- grid-container -->
    <?}?>
</main>
        <?if($APPLICATION->GetProperty('NOT_SHOW_FOOTER','N') !== 'Y'){?>
            <footer class="footer <?/*if($APPLICATION->GetCurPage()==SITE_DIR){?>footer--fixed<?}*/?>">
                <div class="grid-container grid-container--fluid">
                    <div class="grid-row">
                        <?$APPLICATION->IncludeComponent(
                            "bitrix:menu",
                            "kit_bottom_new",
                            array(
                                "ROOT_MENU_TYPE" => "bottom",
                                "MENU_CACHE_TYPE" => "A",
                                "MENU_CACHE_TIME" => "3600",
                                "MENU_CACHE_USE_GROUPS" => "Y",
                                "MENU_CACHE_GET_VARS" => array(
                                ),
                                "MAX_LEVEL" => "1",
                                "CHILD_MENU_TYPE" => "left",
                                "USE_EXT" => "N",
                                "DELAY" => "N",
                                "ALLOW_MULTI_SELECT" => "N"
                            ),
                            false
                        );?>
                        <div id="subscribe_block" class="col-xl-4 col-lg-4 ninja--sm ninja--md">
                            <div class="subscribe_block__form">
                                <div class="footer__input subscribe_form">
                                    <div class="form-row form-row--flex">
                                        <div class="form-row__block fadein_after_5s">
                                            <?echo bitrix_sessid_post();?>
                                            <input type="text" class="form-input js-email-subscribe" name="EMAIL" size="20" value="" title="E-mail" placeholder="<?=LANGUAGE_ID == 'en' ? 'Enter your e-mail' : 'Введите ваш e-mail'?>">
                                            <button class="btn btn--arrow-right js-open-subscribe" type="button"><?=LANGUAGE_ID == 'en' ? 'Subscribe' : 'Подписаться'?></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <script>
                            function isEmail(email){
                                var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
                                return regex.test(email);
                            }

                            var openSubscribePopup = function(e){
                                e.preventDefault();
                                var popup_id = 'subscribe',
                                    email = $('.js-email-subscribe').val()
                                ;

                                $('.js-email-subscribe').removeClass('_error');

                                if (email == '' || !isEmail(email)){
                                    $('.js-email-subscribe').addClass('_error');
                                } else {
                                    $.get('/local/app/subscribeForm.php', function(data){
                                        $('.js-subscribe-content').html(data);
                                        $('.js-subscribe-content').find('#subscribe_email_popup').val($('.js-email-subscribe').val());
                                        openPopup(popup_id);
                                    });
                                }
                            }
                            $(document).on('click', '.js-open-subscribe', openSubscribePopup);
                        </script>
                        <?/*$APPLICATION->IncludeComponent(
                            "kodix:mailchimp.subscribe",
                            "footer_new",
                            array(
                                "PAGE" => "/ajax/subscribe/index.php",
                                "SHOW_HIDDEN" => "N",
                                "AJAX_MODE" => "N",
                                "AJAX_OPTION_JUMP" => "N",
                                "AJAX_OPTION_STYLE" => "N",
                                "AJAX_OPTION_HISTORY" => "N",
                                "CACHE_TYPE" => "A",
                                "CACHE_TIME" => "3600",
                                "ALLOW_ANONYMOUS" => "Y",
                                "SHOW_AUTH_LINKS" => "N",
                                "SET_TITLE" => "N",
                                "AJAX_OPTION_ADDITIONAL" => ""
                            ),
                            false
                        );*/?>
                        <div class="col-xl-4 col-lg-4 col-md-6 align-center">
                            <div class="footer-nav footer-nav--right" style="display: flex; flex-direction: column;">
							<div style="display: flex; align-self: end;">
                                <?$APPLICATION->IncludeComponent(
                                    "bitrix:main.include",
                                    ".default",
                                    array(
                                        "AREA_FILE_SHOW" => "sect",
                                        "AREA_FILE_SUFFIX" => "socials",
                                        "AREA_FILE_RECURSIVE" => "Y",
                                    ),
                                    false
                                );?>
								</div>
								<div class="footer-nav__tab" style="display: flex; align-self: end; margin: -15px 20px 20px 0;">
                                    <?$APPLICATION->IncludeComponent(
                                        "bitrix:main.include",
                                        ".default",
                                        array(
                                            "AREA_FILE_SHOW" => "sect",
                                            "AREA_FILE_SUFFIX" => "phone",
                                            "AREA_FILE_RECURSIVE" => "Y",
                                        ),
                                        false
                                    );?>
                                </div>
                            </div>
                        </div>
<!--                        <div class="copyrights_block">-->
<!--                            <div class="copyright">&copy;&nbsp;--><?//=date('Y')?><!--&nbsp;ITK<br /><a href="http://kodix.ru/" target="_blank">Сделано в KODIX</a></div>-->
<!--                            <ul class="contacts_data">-->
<!--                                --><?//$APPLICATION->IncludeComponent(
//                                    "bitrix:main.include",
//                                    ".default",
//                                    array(
//                                        "AREA_FILE_SHOW" => "sect",
//                                        "AREA_FILE_SUFFIX" => "address",
//                                        "AREA_FILE_RECURSIVE" => "Y",
//                                    ),
//                                    false
//                                );?>
<!--                            </ul>-->
<!--                        </div>-->
                        <?/*$APPLICATION->IncludeComponent(
                                "bitrix:menu",
                                "kit_bottom",
                                array(
                                    "ROOT_MENU_TYPE" => "bottom",
                                    "MENU_CACHE_TYPE" => "N",
                                    "MENU_CACHE_TIME" => "3600",
                                    "MENU_CACHE_USE_GROUPS" => "Y",
                                    "MENU_CACHE_GET_VARS" => array(
                                    ),
                                    "MAX_LEVEL" => "1",
                                    "CHILD_MENU_TYPE" => "left",
                                    "USE_EXT" => "N",
                                    "DELAY" => "N",
                                    "ALLOW_MULTI_SELECT" => "N"
                                ),
                                false
                            );?>
                        <?$APPLICATION->IncludeComponent(
                            "kodix:mailchimp.subscribe",
                            "footer",
                            array(
                                "PAGE" => "/ajax/subscribe/index.php",
                                "SHOW_HIDDEN" => "N",
                                "AJAX_MODE" => "N",
                                "AJAX_OPTION_JUMP" => "N",
                                "AJAX_OPTION_STYLE" => "N",
                                "AJAX_OPTION_HISTORY" => "N",
                                "CACHE_TYPE" => "A",
                                "CACHE_TIME" => "3600",
                                "ALLOW_ANONYMOUS" => "Y",
                                "SHOW_AUTH_LINKS" => "N",
                                "SET_TITLE" => "N",
                                "AJAX_OPTION_ADDITIONAL" => ""
                            ),
                            false
                        );*/?>
<!--                        <div class="payment_list_hold">-->
<!--                            <ul class="payment_list">-->
<!--                                --><?//$APPLICATION->IncludeComponent(
//                                    "bitrix:main.include",
//                                    ".default",
//                                    array(
//                                        "AREA_FILE_SHOW" => "sect",
//                                        "AREA_FILE_SUFFIX" => "payment",
//                                        "AREA_FILE_RECURSIVE" => "Y",
//                                    ),
//                                    false
//                                );?>
<!--                            </ul>-->
<!--                        </div>-->
                    </div>
                </div>
            </footer>
        <?}?>

<div class="cookies js-close-container">
    <div class="grid-container">
        <div class="grid-row grid-row--center">
            <div class="col-sm-12 col-lg-10">
                <div class="cookies__text">
                    <?=GetMessage('COOKIE_NOTE')?>
                </div>
            </div>
            <div class="col-sm-12 col-lg-2">
                <button class="btn btn--primary js-close-trigger" type="button">Ok</button>
            </div>
        </div>
    </div>
</div>

<?if(!$USER->IsAuthorized()){?>

    <?ob_start()?>
    <?$APPLICATION->IncludeComponent('bitrix:system.auth.forgotpasswd','popup',
        array(),
        false
    );?>
    <?showPopupHtml(ob_get_clean(),'forgotpasswd',false,GetMessage('FORGOT_TITLE_POPUP'),true);?>


    <?ob_start()?>
    <?$APPLICATION->IncludeComponent('bitrix:system.auth.authorize','popup_new',
        array(),
        false
    );?>
    <?showPopupHtml(ob_get_clean(),'enter',false,GetMessage('AUTH_SIGNIN'),true);?>

    <?/*ob_start()?>
    <?$APPLICATION->IncludeComponent('kodix:personal.registration','popup_new',
        array(
            'FIELDS'=>array(
                'NAME',
                'LAST_NAME',
                'EMAIL',
                'PERSONAL_PHONE',
                'PASSWORD',
                'CONFIRM_PASSWORD',
                'AGREEMENT',
                'SUBSCRIBE'
            ),
        ),
        false
    );?>
    <?showPopupHtml(ob_get_clean(),'reg',false,GetMessage('AUTH_SIGNUP'),true);*/?>

<?}?>

<?/*<div class="popup" data-popup-show="init" data-popup-id="geo">
    <div class="popup__overlay js-refresh" data-popup-bg></div>
    <div class="js_customscroll popup__content">
        <div class="popup__inner">
            <div class="popup__close js-refresh" data-popup-close>
                <svg class="icon icon-cross_pop-up">
                    <use xlink:href="<?=SITE_TEMPLATE_PATH?>/resources/svg/app.svg#cross_pop-up"></use>
                </svg>
            </div>
            <div class="geo__heading"><?=GetMessage('YOUR_COUNTRY')?> — <span class="country-name">Италия</span>?</div>
            <div class="text--center">
                <a class="btn btn--primary btn--inline-lg btn--block-md country__name js-refresh" href="#" data-country-name="" data-popup-close="true"><?=GetMessage('YES_COUNTRY')?></a>
             </div>
            <div class="text--center">
                <a class="link link--primary link--bold" href="#" data-popup-close data-popup-for="geo_conf"><?=GetMessage('OTHER_REGION')?></a>
             </div>
        </div>
    </div>
</div>*/?>

<?echo $GLOBALS['geo_conf'];?>
<?echo $GLOBALS['product_size_grid'] ? $GLOBALS['product_size_grid'] : '';?>

<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?//popup мы открылись?>
<?
//echo '<pre style="skdebug">';
//print_r (LANGUAGE_ID);
//echo '</pre>';
//die();
//LANGUAGE_ID == 'en' ? 'Sign up' : 'Регистрация';
?>
<div class="popup popup--padding" data-popup-show="init" data-popup-id="we_opened">
    <div class="popup__overlay js-mailpopup-close" data-popup-bg></div>
    <div class="popup__content">
        <div class="popup__inner popup__inner--padding">
            <div class="popup__close popup__close--mobile js-mailpopup-close" data-popup-close>
                <svg class="icon icon-cross_pop-up">
                    <use xlink:href="<?=SITE_TEMPLATE_PATH?>/resources/svg/app.svg#cross_pop-up"></use>
                </svg>
            </div>
            <div class="js-we_opened-content">
                <div class="auth__heading">
                    <?=LANGUAGE_ID == 'en' ? 'We’re open again!' : 'Магазин в Риге открыт!';?>
                </div>
                <div class="modal_note_v1 mod_2 sk_padding">
                    <?=LANGUAGE_ID == 'en' ? 'Store hours:</br>Mon - Fr 11:00 - 20:00<br>Sat - Sun 12:00 - 18:00' : 'Время работы:</br>Будни — с 11:00 до 20:00<br>Выходные — с 12:00 до 18:00';?>
                </div>
                <a href="/help/contacts/" target="_blank">
                    <button type="submit" class="btn btn--primary btn--inline-lg btn--block-md">
                        <?=LANGUAGE_ID == 'en' ? 'How to find?' : 'Как добраться?';?>
                    </button>
                </a>
            </div>
        </div>
    </div>
</div>
<?//if (!$USER->IsAuthorized() || ($USER->IsAuthorized() && !$oChimp->isSubscribed($USER->GetEmail()))){?>
    <input type="hidden" id="popuptimer_we_opened" value="">
<!--    <script>
        jQuery(document).ready(function($){
            var popup_id = 'we_opened';

            if ($.cookie('showPopupWe_opened') == null){
                setTimeout(function(){
                    openPopup(popup_id);
                }, 1000);
            }
            
            //$('.js-mailpopup-close').on('click', function(){
                $.cookie('showPopupWe_opened', '', {expires:30, path: '/'});
            //});
            
        });
    </script>-->
<?//}?>
<?//popup мы открылись?>

<?
CModule::IncludeModule("kodix.mailchimp");
global $USER;
$oChimp = new KDXMailChimp();?>
<div class="popup popup--padding" data-popup-show="init" data-popup-id="subscribe">
    <div class="popup__overlay js-mailpopup-close" data-popup-bg></div>
    <div class="popup__content">
        <div class="popup__inner popup__inner--padding">
            <div class="popup__close popup__close--mobile js-mailpopup-close" data-popup-close>
                <svg class="icon icon-cross_pop-up">
                    <use xlink:href="<?=SITE_TEMPLATE_PATH?>/resources/svg/app.svg#cross_pop-up"></use>
                </svg>
            </div>
            <div class="js-subscribe-content"></div>
        </div>
    </div>
</div>
<?if (!$USER->IsAuthorized() || ($USER->IsAuthorized() && !$oChimp->isSubscribed($USER->GetEmail()))){?>
    <input type="hidden" id="popuptimer" value="">
    <script>
        jQuery(document).ready(function($){
            var popup_id = 'subscribe';

            $('.js-mailpopup-close').on('click', function(){
                $.cookie('showPopupSubscribe', '', {expires:1, path: '/'});
            });
            if ($.cookie('showPopupSubscribe') == null){
                $.get('/local/app/subscribeForm.php', function(data){
                    $('.js-subscribe-content').html(data);
                    setTimeout(function(){
                        openPopup(popup_id);
                    }, 30000);
                });
            }
        });
    </script>
<?}?>
<div class="popup gallery__layout" style="display:none"></div>
<div class="popup" data-popup-show="init" data-popup-id="register">
    <div class="popup__overlay" data-popup-bg=""></div>
    <div class="js_customscroll popup__content mCustomScrollbar">
        <div class="popup__inner">
            <div class="popup__close" data-popup-close="">
                <svg class="icon icon-cross_pop-up">
                    <use xlink:href="/local/templates/kit_new/resources/svg/app.svg#cross_pop-up"></use>
                </svg>
            </div>
            <div class="auth__heading"><?=LANGUAGE_ID == 'en' ? 'Sign up' : 'Регистрация';?></div>
        </div>
    </div>
</div>
<script>
    openPopup = function(popup_id){
        if(typeof popup_id == "undefined") {
            return false;
        }

        var popup = $('.popup[data-popup-id="' + popup_id + '"]');
        if(popup.length) {
            popup.attr('data-popup-show', true);
            $('body').css('overflow', 'hidden');
        }
    }
    var showSignUp = function(e){
        e.preventDefault();
        $('.popup[data-popup-id="register"]').find('form').remove();
        <?if (LANGUAGE_ID == 'en'){?>
            $.get('/local/app/getSignUpEn.php', function(data){
                $('.popup[data-popup-id="register"]').find('.popup__inner').append(data);
            });
        <?} else {?>
            $.get('/local/app/getSignUpRu.php', function(data){
                $('.popup[data-popup-id="register"]').find('.popup__inner').append(data);
            });
        <?}?>
    }

    $(document).on('click', '.js-sign-up-btn', showSignUp);
</script>

    <div class="modal_wrap hidden">
        <div class="modal_box modal_box_1">
            <div class="modal_content block_forgot">
            <?/*$APPLICATION->IncludeComponent('bitrix:system.auth.forgotpasswd','popup',
                array(),
                false
            );*/?>
            </div>
        </div>
        <div class="modal_box modal_box_3">
            <div class="modal_content mod_register">
                <div class="block_entry">

                </div>
                <div class="block_register">
                <?
                /*$APPLICATION->IncludeComponent('kodix:personal.registration', 'popup',
                    array('FIELDS'=>array('NAME','LAST_NAME', 'EMAIL','PASSWORD','CONFIRM_PASSWORD','SUBSCRIBE')),
                    false
                );*/
                ?>
                </div>
            </div>
        </div>
        <div class="modal_box modal_box_size_grid">
            <div class="modal_content mod_size_grid">
                <?//$APPLICATION->ShowViewContent('SIZE_GRID')?>
            </div>
        </div>
<?

/*$APPLICATION->IncludeComponent(
    "kodix:mailchimp.subscribe",
    "popup",
    array(
        "PAGE" => "/ajax/subscribe/popup.php",
        "SHOW_HIDDEN" => "N",
        "AJAX_MODE" => "N",
        "AJAX_OPTION_JUMP" => "N",
        "AJAX_OPTION_STYLE" => "N",
        "AJAX_OPTION_HISTORY" => "N",
        "CACHE_TYPE" => "A",
        "CACHE_TIME" => "3600",
        "ALLOW_ANONYMOUS" => "Y",
        "SHOW_AUTH_LINKS" => "N",
        "SET_TITLE" => "N",
        "AJAX_OPTION_ADDITIONAL" => ""
    ),
    false
);*/?>
    </div>
<!--LiveTex-->
<!--
<script type='text/javascript'>
    setTimeout(function(){
        window['l'+'iv'+'eTe'+'x'] = true,
        window['liv'+'eT'+'exID'] = <?if (SITE_ID == "s1"):?>103167<?else:?>103545<?endif;?>,
        window['liveTe'+'x_obje'+'ct'] = true;
        (function() {
            var t = document['create'+'Elemen'+'t']('script');
            t.type ='text/javascript';
            t.async = true;
            t.src = '//cs15.li'+'vetex.'+'ru/'+'js/client'+'.js';
            var c = document['ge'+'tElementsB'+'yTagName']('script')[0];
            if ( c ) c['p'+'ar'+'ent'+'No'+'d'+'e']['inser'+'tB'+'e'+'fore'](t, c);
            else document['do'+'cume'+'ntEle'+'ment']['fir'+'stC'+'hild']['app'+'en'+'d'+'Child'](t);
        })();
    }, 5000);
</script>
-->
<?
if($APPLICATION->GetPageProperty("show_quiz") != 'Y')
{
    $APPLICATION->IncludeComponent
    (
        "kodix:quiz",
        ".default",
        array(
            "IBLOCK_QUIZ_TYPE" => "kodix_media",
            "IBLOCK_QUIZ_ID" => "8",
            'QUIZ_DROP' => 'Y',
        ),
        false);
}
?>
<?
$APPLICATION->IncludeComponent("kodix:seo.meta", "kit", Array(
	"COMPONENT_TEMPLATE" => "kit",
		"CACHE_TYPE" => "A",	// Cache type
		"CACHE_TIME" => "3600",	// Cache time (sec.)
	),
	false
);
?>
<?/*if (isset($_GET['new']) && $_GET['new'] == 'y'){?>
    <link href="<?=SITE_TEMPLATE_PATH?>/resources/css/styles-new.css" rel="stylesheet">
<?} else {?>
    <link href="<?=SITE_TEMPLATE_PATH?>/resources/css/styles.css" rel="stylesheet">
<?}*/?>

<?if (gettype($APPLICATION->GetProperty('WEBPACK_JS')) !== 'NULL'){?>
    <? if (
      $_SERVER['REQUEST_URI'] !== '/help/return-and-exchanges/' &&
      $_SERVER['REQUEST_URI'] !== '/help/privacy-policy/' &&
      $_SERVER['REQUEST_URI'] !== '/help/delivery-and-payment/' &&
      $_SERVER['REQUEST_URI'] !== '/help/about-us/' &&
      $_SERVER['REQUEST_URI'] !== '/help/terms-and-conditions/' &&
      $_SERVER['REQUEST_URI'] !== '/sitemap/'
    ) { ?>
    <link href="<?=SITE_TEMPLATE_PATH?>/resources/css/<?$APPLICATION->ShowProperty('WEBPACK_JS')?>.css" rel="stylesheet">
    <? } ?>
<?}?>
<?$APPLICATION->ShowCss(true);?>
<?$APPLICATION->ShowHeadScripts();?>
<!-- Go to www.addthis.com/dashboard to customize your tools -->
<!--<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-57fcf379700e36cd"></script>soc_serv-->
<?
$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/jquery.cookie.js');
$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/resources/js/front/commons.js');
$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/custom.js?ver=1.0.0');
?>
<?/*<script src="<?=SITE_TEMPLATE_PATH?>/js/jquery.cookie.js"></script>
<script src="<?=SITE_TEMPLATE_PATH?>/resources/js/front/commons.js"></script>
<script src="<?=SITE_TEMPLATE_PATH?>/js/custom.js?ver=1.0.0"></script>*/?>
<?kitDataCollector::includePageJs()?>

<!-- Facebook Pixel Code -->
<script>
!function(f,b,e,v,n,t,s)
{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];
s.parentNode.insertBefore(t,s)}(window,document,'script',
'https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '177598201131202');
fbq('track', 'PageView');
</script>
<noscript>
<img height="1" width="1"
src="https://www.facebook.com/tr?id=177598201131202&ev=PageView
&noscript=1"/>
</noscript>
<!-- End Facebook Pixel Code -->
<?
//.com
$vkPixelCode='VK-RTRG-1058242-3ZYT3';
//.ru
if (SITE_ID == "s1") {
	$vkPixelCode = 'VK-RTRG-1058244-hNPVX';
}
?>
<!-- VK Pixel Code -->
<script type="text/javascript">!function(){var t=document.createElement("script");t.type="text/javascript",t.async=!0,t.src="https://vk.com/js/api/openapi.js?169",t.onload=function(){VK.Retargeting.Init("<?=$vkPixelCode?>"),VK.Retargeting.Hit()},document.head.appendChild(t)}();</script><noscript><img src="https://vk.com/rtrg?p=<?=$vkPixelCode?>" style="position:fixed; left:-999px;" alt=""/></noscript>
<!-- End VK Pixel Code -->
</body>
</html>

<?if($APPLICATION->GetProperty('NOT_SHOW_TITLE','N') !== 'Y'){

    //if($APPLICATION->GetProperty('NOT_SHOW_PAGE_WRAPPER') !== 'Y') {
        //$PAGE_WRAPPER.='<h1 class="std_header">'.$APPLICATION->GetTitle('H1').'</h1>';
    //}else{
        $APPLICATION->AddViewContent('TITLE','<div class="grid-row"><div class="col-sm-12"><h1 class="title--page">'.$APPLICATION->GetTitle('H1').'</h1></div></div>');
    //}
}?>
<?
if($APPLICATION->GetProperty('NOT_SHOW_PAGE_WRAPPER') !== 'Y') {
    $APPLICATION->AddViewContent('PAGE_WRAPPER', $PAGE_WRAPPER);
}
?>

<?/*if($APPLICATION->GetProperty('SECTION_TITLE') !== false){
    $APPLICATION->AddViewContent('SECTION_TITLE','<div class="title_v1">'.$APPLICATION->GetProperty('SECTION_TITLE').'</div>');
}*/?>

<?/*if($APPLICATION->GetProperty('NOT_SHOW_NAV_CHAIN_IN_HEADER','N') !== 'Y') {
    $APPLICATION->AddViewContent('BREADCRUMB',$APPLICATION->GetNavChain());
}*/?>

<?


if(isset($_REQUEST['qs']))
{
    $APPLICATION->IncludeComponent
    (
        "kodix:quiz",
        ".default",
        array(
            "IBLOCK_QUIZ_TYPE" => "kodix_media",
            "IBLOCK_QUIZ_ID" => "8",
            'QUIZ_OG' => 'Y',
        ),
        false);
}
/*else
{
    $APPLICATION->IncludeComponent(
        "kodix:social",
        ".default",
        array(
            "TYPE" => "og",
            "TAGS" => array(

                "OG:TITLE" => '',
                "OG:DESCRIPTION" => '',
                "OG:TYPE" => "website",
                "OG:URL" => '',
                "OG:SITE_NAME" => "",
                "OG:IMAGE" => "",
            )
        ),
        false
    );
}*/
global $APPLICATION;
if((int)$_GET['PAGEN_1'] > 1) {
    //$t =  explode(':', $APPLICATION->GetPageProperty('title'));
    $t = $APPLICATION->GetTitle();
    $desc = explode('Itk Online Store', $APPLICATION->GetPageProperty('description'));
    $APPLICATION->SetPageProperty('title', $t . ' - page ' . $_GET['PAGEN_1']);
    $APPLICATION->SetPageProperty('description', $desc[0].'Itk Online Store - page '.$_GET['PAGEN_1']);
}
?>
