<?Bitrix\Main\Localization\Loc::loadMessages(__FILE__);?>
<?
global $USER;
if ($USER->IsAdmin()) {
    if ($_REQUEST['clear_cache_picture'] === 'Y') {
        setcookie('clear_cache_picture', 'Y', time() + 60 * 60);
    }else{
        setcookie('clear_cache_picture', null, -1);
    }
}
if ($_SERVER['REQUEST_URI'] !== '/ajax/auth/auth.php?register=yes' && strstr($_SERVER['REQUEST_URI'], 'register=yes')){
    LocalRedirect('/', false, "301 Moved Permanently");
}
?>
<!DOCTYPE html>
<!--[if lte IE 9]>      <html <?$APPLICATION->ShowProperty('AMP')?> class="lt-ie9 ie9" lang="<?=strtolower(LANGUAGE_ID)?>"> <![endif]-->
<!--[if gt IE 8]><!--> <html <?$APPLICATION->ShowProperty('AMP')?> lang="<?=strtolower(LANGUAGE_ID)?>"> <!--<![endif]-->
<head>
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title><?$APPLICATION->ShowTitle()?></title>
    <meta name="viewport" content="width=device-width,minimum-scale=1,initial-scale=1">
    <meta name="copyright" content="itk">
    <meta name="apple-mobile-web-app-title" content="itk">
    <meta name="SKYPE_TOOLBAR" content="SKYPE_TOOLBAR_PARSER_COMPATIBLE"/>
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta name="tagline" content="kodix.ru" />
    <meta name="cmsmagazine" content="c5926dc15d50d2f777c581278b0efe9f" />
    <meta name='yandex-verification' content='4732690687e9b94c' />
	<meta name="p:domain_verify" content="5d5df7a02d42e33b3694dd87fdfb1d21"/>
    <?if (CSite::InDir('/index.php')){?>
        <meta property="og:title" content="Itk KIT | Shop the Latest Streetwear Fashion Online"/>
        <meta property="og:type" content="website"/>
        <meta property="og:url" content="https://www.itkkit.com/"/>
        <meta property="og:site_name" content="Itkkit.com" />
        <meta property="og:description" content="Shop the Latest Streetwear Clothing at itk Online Store Now. Free Shipping on All Orders Over €350 ✓ Fast Delivery ✓ 14 Days Return Policy ✓ 100% Authenticity ✓"/>
        <meta property="og:image" content="https://www.itkkit.com/images/itk-og.jpg"/>
        <meta property="vk:image" content="https://www.itkkit.com/images/itk-vk-image.jpg"/>

        <meta property="twitter:card" content="summary_large_image"/>
        <meta property="twitter:url" content="https://www.itkkit.com/"/>
        <meta property="twitter:title" content="Itk KIT | Shop the Latest Streetwear Fashion Online"/>
        <meta property="twitter:description" content="Shop the Latest Streetwear Clothing at itk Online Store Now. Free Shipping on All Orders Over €350 ✓ Fast Delivery ✓ 14 Days Return Policy ✓ 100% Authenticity ✓"/>
        <meta property="twitter:image:src" content="https://www.itkkit.com/images/logo-social.jpg?ver=1"/>
        <meta property="twitter:site" content="Itkkit.com"/>
    <?}?>
    <?switch(SITE_ID){
        case 'en':
            echo '<meta name="google-site-verification" content="P7wSiEsN8QZAxWtSm8ptE6Wffwoh9m4WeiLe-ylaHyo" />';
            break;
        case 's1':
            echo '<meta name="google-site-verification" content="JO7c3V0KWNb12r2nry6cN4FS5_os3dIdVS-wOWTsyNE" />';
            break;
        default:break;
    }?>
    <?//addCanonicalLinks(); //Линки для SEO ?>
    <?/*<link rel="icon" href="<?=SITE_TEMPLATE_PATH?>/favicon.ico" type="image/vnd.microsoft.icon" />*/?>
    <link rel="icon" href="<?=SITE_TEMPLATE_PATH?>/favicon.png" type="image/png" />
    <link rel="apple-touch-icon" href="<?=SITE_TEMPLATE_PATH?>/apple-touch-icon.png" />
    <style type="text/css">img[data-object-fit="contain"] {
            object-fit: contain;
        }

        img[data-object-fit="cover"] {
            object-fit: cover;
        }

        .img--lazyload {
            opacity: 0;
        }

        .lazyload, .lazyloaded, .img--lazyload {
            transition: opacity 0.3s;
            -webkit-transition: opacity .3s;
        }

        .lazyload {
            opacity: 0;
        }

        .lazyloaded {
            opacity: 1;
        }

        .preloader {
            display: -webkit-box;
            display: -webkit-flex;
            display: -ms-flexbox;
            display: flex;
            -webkit-box-align: center;
            -webkit-align-items: center;
            -ms-flex-align: center;
            align-items: center;
            -webkit-box-pack: center;
            -webkit-justify-content: center;
            -ms-flex-pack: center;
            justify-content: center;
            position: fixed;
            z-index: 3000;
            top: 0;
            bottom: 0;
            left: 0;
            right: 0;
            background: #fff;
        }

        .page-loading {
            overflow-x: hidden;
        }
    </style>
    <link href="<?=SITE_TEMPLATE_PATH?>/resources/css/intlTelInput.css" rel="stylesheet">
    <link href="<?=SITE_TEMPLATE_PATH?>/resources/css/commons.css" rel="stylesheet">
    <link href="<?=SITE_TEMPLATE_PATH?>/resources/css/styles.css" rel="stylesheet">
    <?/*<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600&display=block&subset=cyrillic" rel="stylesheet">*/?>
    <?/*
    $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . '/resources/css/commons.css');
    $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . '/resources/css/'.$APPLICATION->ShowProperty('WEBPACK_JS').'.css');
    */?>
    <!-- мы подрубаем jQuery для того, чтобы заработали все скрипты Bitrix и Kodix -->
    <script src="<?=SITE_TEMPLATE_PATH?>/js/jquery.min.js"></script>
    <script src="<?=SITE_TEMPLATE_PATH?>/js/intlTelInput.js"></script>
    <script src="<?=SITE_TEMPLATE_PATH?>/js/intlTelInput.min.js"></script>
    <?$APPLICATION->ShowProperty('SELECTION_STYLE','')?>
    <?$APPLICATION->ShowHeadStrings();?>
    <?CJSCore::Init();?>
    <?//$APPLICATION->ShowHead()?>
    <?
    echo '<meta http-equiv="Content-Type" content="text/html; charset='.LANG_CHARSET.'"'.($bXhtmlStyle? ' /':'').'>'."\n";
    $APPLICATION->ShowMeta("robots");
    if (SITE_ID == 'en') {
      $APPLICATION->ShowMeta("keywords");
    }
    $APPLICATION->ShowMeta("description");
    //$APPLICATION->ShowLink("canonical");
    ?>
    <?if (!checkGooglePagespeed()){?>
        <?
        $APPLICATION->IncludeComponent(
            "bitrix:main.include",
            "",
            Array(
                'AREA_FILE_SHOW' => 'sect',
                'AREA_FILE_RECURSIVE'=>'Y',
                'AREA_FILE_SUFFIX' => 'criteo'
            )
        );
        ?>
        <script type="text/javascript">(window.Image ? (new Image()) : document.createElement('img')).src = location.protocol + '//vk.com/rtrg?r=zpUWCBFOjBc0ltUcH4DLTLLE0mUjm6uWcFZhtv22bPhCJWGSSVd96QMLTlf75ZLLOk4DrFuHY9ZeYqh7D6OjjtePyMOQHddqlyIaAQNwRJ3cm5rMxH0J0GC8QXQE8W1yJhdlcnyvLqT28yhw*eNkiRr9jKFaV*v*bjDureeD6KQ-';</script>
        <?//$analyticsId = ['en' => 'UA-52518243-2', 's1' => 'UA-52518243-2']?>
        <?$analyticsId = 'UA-52518243-2';?>
        <!-- Global site tag (gtag.js) - Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=<?=$analyticsId[SITE_ID]?>"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '<?=$analyticsId[SITE_ID]?>');
        </script>
    <?}?>
	<!-- Google Tag Manager -->
	<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
	new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
	j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
	'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
	})(window,document,'script','dataLayer','GTM-KMM2CV8');</script>
	<!-- End Google Tag Manager -->
</head>
<body class="page-loading page"><!-- Mixins-->
	<!-- Google Tag Manager (noscript) -->
	<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-KMM2CV8"
	height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
	<!-- End Google Tag Manager (noscript) -->
    <script>
        window.fbAsyncInit = function() {
        FB.init({
        appId      : '564057078071426',
        cookie     : true,
        xfbml      : true,
        version    : 'v11.0'
        });

        FB.AppEvents.logPageView();   

        };

        (function(d, s, id){
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) {return;}
        js = d.createElement(s); js.id = id;
        js.src = "https://connect.facebook.net/en_US/sdk.js";
        fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));
    </script>
<?/*if (!$USER->IsAdmin()) {?>
    <div id="__kdx_preloader" class="preloader"><img src="<?=SITE_TEMPLATE_PATH?>/resources/img/preloader.gif"></div>
<?}*/?>
<?$APPLICATION->ShowPanel();?>
<?
if(!empty($_GET['user']) && !empty($_GET['hash']))
{
    /*
     * авторизуем пользователя
    */

    $user = base64_decode($_GET['user']);
    $hash = $_GET['hash'];

    $login = CUser::GetByLogin($user)->Fetch();
    if(!empty($login['PASSWORD']) && md5($login['PASSWORD']) == $hash && !$USER->IsAuthorized())
    {
        /*
         * если это админ, не авторизуем его
         * */
        /*if(in_array(1, CUser::GetUserGroup($login['ID']))){
            LocalRedirect('/');
            exit;
        }*/

        $USER->Authorize($login['ID']);
    }

    $current_url = $APPLICATION->GetCurPageParam("", array("user", "hash"));

    LocalRedirect($current_url);

}

if(!empty($_GET['cur'])){
        $_SESSION['LAST_COUNTRY'] = $_GET['cur'];
        switch($_GET['cur']){
            case 'RU':
                $_SESSION['KDX_CURRENCY'] = 'RUB';
                break;
            case 'UK':
                $_SESSION['KDX_CURRENCY'] = 'GBP';
                break;
            case 'US':
                $_SESSION['KDX_CURRENCY'] = 'USD';
                break;
            default:
                $_SESSION['KDX_CURRENCY'] = 'EUR';
                break;
        }

    $current_url = $APPLICATION->GetCurPageParam("", array( "cur"));

    LocalRedirect($current_url);
}
?>
<?
//$APPLICATION->IncludeComponent('kodix:rr.popup','.default', array());
?>
<div class="nav-panel" data-nav-panel>
    <div class="nav-panel__inner js_customscroll">
        <div class="nav-panel__header">
            <div class="nav-panel__close" data-nav-panel-close>
                <svg class="icon icon-cross_pop-up">
                    <use xlink:href="<?=SITE_TEMPLATE_PATH?>/resources/svg/app.svg#cross_pop-up"></use>
                </svg>
            </div>
            <div class="nav-panel__enter">
                <a class="nav-panel__enter-icon" <?=$USER->IsAuthorized() ? 'href="/personal/"' : 'data-popup-for="enter"'?>>
                    <svg class="icon icon-account_header">
                        <use xlink:href="<?=SITE_TEMPLATE_PATH?>/resources/svg/app.svg#account_header"></use>
                    </svg>
               </a>
               <a <?=$USER->IsAuthorized() ? 'href="/personal/"' : 'data-popup-for="enter"'?> class="nav-panel__enter-text link link--primary"><?=$USER->IsAuthorized() ? $USER->GetFullName() : GetMessage('SIGN_IN')?></a>

            </div>
        </div>
        <?$APPLICATION->IncludeComponent(
            "bitrix:menu",
            "kit_main_new_mobile",
            array(
                "ROOT_MENU_TYPE" => "main",
                "MENU_CACHE_TYPE" => "Y",
                "MENU_CACHE_TIME" => "3600",
                "MENU_CACHE_USE_GROUPS" => "Y",
                "MENU_CACHE_GET_VARS" => array(
                ),
                "MAX_LEVEL" => "2",
                "CHILD_MENU_TYPE" => "left",
                "USE_EXT" => "Y",
                "DELAY" => "N",
                "ALLOW_MULTI_SELECT" => "N",
            ),
            false
        );?>
        <?$APPLICATION->IncludeComponent(
            "bitrix:menu",
            "kit_bottom_new_mobile",
            array(
                "ROOT_MENU_TYPE" => "bottom",
                "MENU_CACHE_TYPE" => "Y",
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
    </div>
</div>
<div class="page-wrapper" style="background: <?$APPLICATION->ShowProperty('SELECTION_COLOR','none')?>">
    <header class="header <?if($APPLICATION->GetCurPage()==SITE_DIR){?>header--fixed<?} elseif(strpos($APPLICATION->GetCurPage(), 'catalog/product') !== false){?>header--reset<?}?>">
        <?$APPLICATION->ShowViewContent('ACTION_DESCR')?>
        <div class="grid-container grid-container--fluid">
            <div class="header__container">
                <div class="header__left">
                    <?if(!isIndex()){?>
                        <a class="header__title" href="/">
                    <?}else{?>
                        <span class="header__title">
                    <?}?>
                        <?/*<svg class="icon icon-logo_header">
                            <use xlink:href="<?=SITE_TEMPLATE_PATH?>/resources/svg/app.svg#logo_header"></use>
                        </svg>*/?>
                        <img src="<?=SITE_TEMPLATE_PATH?>/img/ITK_Logo_RGB.svg" width="65" alt="">
                        <?//if ($USER->IsAdmin()) {?>
                            <img class="logo-preloader js-logo-preloader" src="<?=SITE_TEMPLATE_PATH?>/resources/img/preloader.gif" alt="">
                        <?//}?>
                    <?if(isIndex()){?>
                        </span>
                    <?}else{?>
                        </a>
                    <?}?>
                </div>
                <?$APPLICATION->IncludeComponent(
                    "bitrix:menu",
                    "kit_main_new",
                    array(
                        "ROOT_MENU_TYPE" => "main",
                        "MENU_CACHE_TYPE" => "A",
                        "MENU_CACHE_TIME" => "3600",
                        "MENU_CACHE_USE_GROUPS" => "Y",
                        "MENU_CACHE_GET_VARS" => array(
                        ),
                        "MAX_LEVEL" => "1",
                        "CHILD_MENU_TYPE" => "left",
                        "USE_EXT" => "Y",
                        "DELAY" => "N",
                        "ALLOW_MULTI_SELECT" => "N",
                        'PAGE_ITEMS_SEPARATOR' => 5,
                    ),
                    false
                );?>
                <div class="header__right">
                    <a style="display: none; width: 30px; height: 30px; background: #000" class="js-sign-up-btn" href="#" data-popup-close="" data-popup-for="register"></a>
                    <div class="header-action">

                        <div class="header-action__tab header-action__tab--margin-right ninja--sm">
                            <?if (LANGUAGE_ID == 'ru') echo '<!--noindex-->';?>
                            <?$APPLICATION->IncludeComponent(
                                "kodix:country.selector",
                                ".default",
                                array(
                                    "LANGS" => array(
                                        0 => "ru",
                                        1 => "en",
                                    ),
                                    "CACHE_TYPE" => "A",
                                    "CACHE_TIME" => "86400"
                                ),
                                false
                            );?>
                            <?if (LANGUAGE_ID == 'ru') echo '<!--/noindex-->';?>

                            <?/*$APPLICATION->IncludeComponent(
                                "kodix:language.selector",
                                "new",
                                array(
                                    "LANGS" => array(
                                        0 => "ru",
                                        1 => "en",
                                    ),
                                    "CACHE_TYPE" => "A",
                                    "CACHE_TIME" => "86400"
                                ),
                                false
                            );*/?>

                            <div class="header-action__select">/</div>

                            <?$APPLICATION->IncludeComponent(
                                "kodix:currency.selector",
                                "new",
                                array(
                                    "CURRENCIES" => array(
                                        0 => "RUB",
                                        1 => "USD",
                                        2 => "EUR",
                                        3 => "GBP",
                                    ),
                                    "CACHE_TYPE" => "A",
                                    "CACHE_TIME" => "86400"
                                ),
                                false
                            );?>

                        </div>

<div style="display:flex; margin-top: 3px;">
                        <div class="header-action__tab header__search-wrap" style="display:none;">
                            <a class="header-action__link" href="#" data-toggle-for="search">
                                <svg class="icon icon-search_header">
                                    <use xlink:href="<?=SITE_TEMPLATE_PATH?>/resources/svg/app.svg#search_header"></use>
                                </svg>
                            </a>
                            <?$APPLICATION->IncludeComponent(
                                "bitrix:search.form",
                                "new",
                                array(
                                    "PAGE" => "#SITE_DIR#search/"
                                ),
                                false
                            );?>
                        </div>

                        <div class="header-action__tab">
                            <a class="header-action__link" <?=$USER->IsAuthorized() ? 'href="/personal/"' : 'data-popup-for="enter"'?>>
                                <svg class="icon icon-account_header">
                                    <use xlink:href="<?=SITE_TEMPLATE_PATH?>/resources/svg/app.svg#account_header"></use>
                                </svg>
                            </a>
                        </div>

                        <?=getHtmlBasketMini()?>
                        <div class="header-action__tab nav-panel__button" data-nav-panel-button>
                            <a class="header-action__link" href="#">
                                <svg class="icon icon-menu_header">
                                    <use xlink:href="<?=SITE_TEMPLATE_PATH?>/resources/svg/app.svg#menu_header"></use>
                                </svg>
                            </a>
                        </div>
</div>						
                    </div>
                </div>

                <?/*$APPLICATION->IncludeComponent(
                    "bitrix:main.include",
                    ".default",
                    array(
                        "AREA_FILE_SHOW" => "sect",
                        "AREA_FILE_SUFFIX" => "phone",
                        "AREA_FILE_RECURSIVE" => "Y",
                    ),
                    false
                );*/?>

                <?/*$APPLICATION->IncludeComponent(
                    "bitrix:main.include",
                    ".default",
                    array(
                        "AREA_FILE_SHOW" => "sect",
                        "AREA_FILE_SUFFIX" => "headaddress",
                        "AREA_FILE_RECURSIVE" => "Y",
                    ),
                    false
                );*/?>


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


                <?/*<div class="func_block_v2 <?if($USER->IsAuthorized()){?>mod_auth<?}?>">

                    <div class="login_box clearfix">
                        <div class="lb_item search_hold"><a href="#" class="lb_link"></a></div>
                        <?if(!$USER->IsAuthorized()){?>
                            <div class="lb_item login_hold"><a href="/personal/" title="Авторизация/Регистрация" class="lb_link js_modal_ctrl_3"></a></div>
                        <?}else{?>

                            <div class="lb_item login_hold">
                                <a href="/personal/" class="login_ctrl">
                                    <div class="dt"><?=GetMessage('HEADER_HELLO')?>,</div>
                                    <div class="dd"><?=$USER->GetFirstName()?></div>
                                </a>
                                <a href="<?=$APPLICATION->GetCurPageParam('logout=yes', array('logout'))?>" class="lb_link login_ctrl_logout"></a>
                        </div>
                        <?}?>
                    </div>
                </div>*/?>

            </div>
        </div>
    </header>
    <main class="<?$APPLICATION->ShowProperty('MAIN_CLASS', '')?> <?if($APPLICATION->GetCurPage()==SITE_DIR){?>js-fullpage fullpage<?}?>">
        <?if($APPLICATION->GetCurPage()!=SITE_DIR){?>
            <div class="grid-container <?$APPLICATION->ShowProperty('PAGE_WRAPPER_CLASS', '')?>">
        <?}?>

            <?$APPLICATION->ShowViewContent('PAGE_WRAPPER')?>
            <?//$APPLICATION->ShowViewContent('SECTION_TITLE')?>

            <?
            if ($APPLICATION->GetCurPage() !== '/'/* && $USER->GetId() == 403099*/){?>
                <?$APPLICATION->IncludeComponent(
                    "bitrix:breadcrumb",
                    "new",
                    array(
                      "PATH" => "",
                      "SITE_ID" => SITE_ID,
                      "START_FROM" => "0"
                    )
                );?>
            <?}?>
            <?$APPLICATION->ShowViewContent('TITLE')?>
