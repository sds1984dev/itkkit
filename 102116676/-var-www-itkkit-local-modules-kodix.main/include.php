<?
$module_id = "kodix.main";

\Bitrix\Main\Loader::registerAutoLoadClasses(
    'kodix.main',
    array(
        'KDXMainEventHandler' => 'general/KDXMainEventHandler.php',
        'KDXSettings' => 'general/KDXSettings.php',
        'KDXGallery' => 'general/properties/KDXGallery.php',
        'KDXElementPoint' => 'general/properties/KDXElementPoint.php',
        'KDXSiteID' => 'general/properties/KDXSiteID.php',
        'KDXSortableLinkElement' => 'general/properties/KDXSortableLinkElement.php',
    )
);

CJSCore::RegisterExt('kodix_jquery', array(
    'js' => array(
        '/bitrix/js/kodix.main/kodix_libs/jquery_kdx.js',
        '/bitrix/js/kodix.main/kodix_libs/jquery_noconflict.js',
    ),
));


CJSCore::RegisterExt('kodix_jquery_ui', array(
    'js'  => '/bitrix/js/kodix.main/kodix_libs/jquery_ui_kdx.js',
    'css' => '/bitrix/js/themes/.default/kodix_libs/jquery-ui.css',
    'rel' => array('kodix_jquery'),
));
CJSCore::RegisterExt('kodix_print_page', array(
    'js'  => '/bitrix/js/kodix.main/kodix_libs/jquery.printPage.js',
));


CJSCore::RegisterExt('kodix_iosslider', array(
    'js'  => '/bitrix/js/kodix.main/kodix_libs/jquery.iosslider.min.js',
    'rel' => array('kodix_jquery'),
));

$sAnalytics = COption::GetOptionString($module_id, "analytics", "");
if (strlen($sAnalytics) > 0)
    $arAnalytics = unserialize($sAnalytics);

// if(strlen($arAnalytics[SITE_ID]['google']))
// {
//     $script = '<script>
//         (function(i,s,o,g,r,a,m){i["GoogleAnalyticsObject"]=r;i[r]=i[r]||function(){
//             (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
//             m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
//         })(window,document,"script","//www.google-analytics.com/analytics.js","ga");

//         ga("create", "'.$arAnalytics[SITE_ID]['google'].'", "auto");
//         ga("send", "pageview");

//     </script>';
//     \Bitrix\Main\Page\Asset::getInstance()->addString($script);
// }

if(strlen($arAnalytics[SITE_ID]['yandex']))
{
    if (!checkGooglePagespeed()){
        $script = '<script type="text/javascript">
            (function (d, w, c) {
                (w[c] = w[c] || []).push(function() {
                    try {
                        w.yaCounter = new Ya.Metrika({id:'.$arAnalytics[SITE_ID]['yandex'].',
                        clickmap:true,
                        trackLinks:true,
                        accurateTrackBounce:true});
                    } catch(e) { }
                });

                var n = d.getElementsByTagName("script")[0],
                    s = d.createElement("script"),
                    f = function () { n.parentNode.insertBefore(s, n); };
                s.type = "text/javascript";
                s.async = true;
                s.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//mc.yandex.ru/metrika/watch.js";

                if (w.opera == "[object Opera]") {
                    d.addEventListener("DOMContentLoaded", f, false);
                } else { f(); }
            })(document, window, "yandex_metrika_callbacks");
        </script>';
        \Bitrix\Main\Page\Asset::getInstance()->addString($script);
    }
}
?>