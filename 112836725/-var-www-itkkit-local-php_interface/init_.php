<?php 
/**
 * Date: 23.03.2015
 * Time: 19:25
 */


//******************антибот********************
//определение RU или EN
$host=$_SERVER['HTTP_HOST'];
if (count(explode('.',$host)>1)) {
    $host=explode('.',$host)[2];
} else {
    $host='';
}


//if ($ip=='195.135.215.10') {
//    //[REQUEST_URI] => /catalog/brand/carne-bollente/?test=1
//    //header('Location: https://www.itkkit.com/recapcha.html#'.$url);
////    echo '<pre>';
////    print_r ($url);
////    echo '</pre>';
////    die();
//}

if ($host=='com') {
    $file = '/var/www/itkkit/en/bitrix/admin/recapcha/recapcha_suspect.log';
    
    if (file_exists($file)) {
        $content=file_get_contents($file);
        $suspects = explode("\n", trim($content));
    }
    
    //$suspects[]='195.135.215.10'; //для проверки - мой адрес
    $ip=$_SERVER['REMOTE_ADDR'];
    $url='https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

    if (in_array($ip,$suspects)) {
        //добавление в сприсок "отправлено на проверку"
        $file = '/var/www/itkkit/en/bitrix/admin/recapcha/sended_to_challenge.log';
        //2020-11-06T09:33:19Z
        $content=$ip."|".date("Y-m-d\TH:i:s\Z")."\n";
        $r=file_put_contents($file,$content,FILE_APPEND | LOCK_EX);
        
        //Убираем подозриельный адрес из списка требующих проверки
        $key=array_search($ip,$suspects);
        unset($suspects[$key]);
        $suspects = implode("\n", $suspects);
        $file = '/var/www/itkkit/en/bitrix/admin/recapcha/recapcha_suspect.log';
        file_put_contents($file,$suspects);
        header('Location: https://www.itkkit.com/recapcha.html#'.$url);
    }
}

if ($host=='ru') {
    $file = '/var/www/itkkit/ru/bitrix/admin/recapcha/recapcha_suspect_ru.log';
    if (file_exists($file)) {
        $content=file_get_contents($file);
        $suspects = explode("\n", trim($content));
    }
    
    //$suspects[]='195.135.215.10'; //для проверки - мой адрес
    $ip=$_SERVER['REMOTE_ADDR'];
    $url='https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

    if (in_array($ip,$suspects)) {
        //добавление в сприсок "отправлено на проверку"
        $file = '/var/www/itkkit/en/bitrix/admin/recapcha/sended_to_challenge_ru.log';
        //2020-11-06T09:33:19Z
        $content=$ip."|".date("Y-m-d\TH:i:s\Z")."\n";
        $r=file_put_contents($file,$content,FILE_APPEND | LOCK_EX);
        
        //Убираем подозриельный адрес из списка требующих проверки
        $key=array_search($ip,$suspects);
        unset($suspects[$key]);
        $suspects = implode("\n", $suspects);
        $file = '/var/www/itkkit/en/bitrix/admin/recapcha/recapcha_suspect_ru.log';
        file_put_contents($file,$suspects);
        header('Location: https://www.itkkit.ru/recapcha.html');
    }
}
//******************антибот********************

require_once  __DIR__ . '/../../vendor/autoload.php';

$lib_dir = __DIR__ . '/kodix';

// определяем библиотеку KODIX
$lib = array(
    'kdxEventBinds.php',
    'kdxFunctions.php',
    'classes/kdxCFile.php',
    'classes/MemcacheFactory.php',
    'classes/CatalogResizePictures.php',
    'classes/kitEventHandler.php',
    'classes/kitDataCollector.php',
    'classes/RetailRocket.class.php',
    'classes/RetailRocketXMLCatalog.php',
    'classes/kit_service_classes.php',
    'properties/KDXGalleryITK.php',
    'firstdata.lv/Merchant.php',
    'firstdata.lv/kdxFDMerchant.php'
);

define('GROUP_SHOP_MANAGERS', 7);
// подключаем библиотеку KODIX
foreach($lib as $libFile) {
    include $lib_dir . '/' . $libFile;
}
define('STOP_STATISTICS',true);

define('BX_AGENTS_LOG_FUNCTION','kitAgentLogger');

register_shutdown_function(function(){
    $error = error_get_last();
    if ($error['type'] === E_ERROR) {
        $errno   = $error["type"];
        $errfile = $error["file"];
        $errline = $error["line"];
        $errstr  = $error["message"];

        $path = $_SERVER['DOCUMENT_ROOT']."/__FatalError.log";
        if($fh = fopen($path, "a")) {
            $date = date("Y,n,j H:i:s");
            $stack_trace = debug_backtrace();
            fwrite($fh, $date." \nERRNO: ".$errno. " \nERRFILE: ".$errfile." \nERRLINE: ".$errline.
                "\n ERRSTR: ".$errstr."\n".print_r($stack_trace,true)."\n\n");
            fclose($fh);
        }

    }
});

AddEventHandler('main', 'OnEpilog', '_Check404Error', 1);
function _Check404Error(){
    if (defined('ERROR_404') && ERROR_404 == 'Y') {
        global $APPLICATION;
        $APPLICATION->RestartBuffer();
        include $_SERVER['DOCUMENT_ROOT'] . SITE_TEMPLATE_PATH . '/header.php';
        include $_SERVER['DOCUMENT_ROOT'] . '/404.php';
        include $_SERVER['DOCUMENT_ROOT'] . SITE_TEMPLATE_PATH . '/footer.php';
    }
}

function checkGooglePagespeed()
{
  if (strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome-Lighthouse')){
    return true;
  } else {
    return false;
  }
}

function getCountryByPhone()
{
    $code = $_SESSION['LAST_COUNTRY'];
    if ($code == 'UK'){
        $code = 'GB';
    }

    return strtolower($code);
}

function mobileDetect()
{
	$useragent = $_SERVER['HTTP_USER_AGENT'];
	if (preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4))){
		return true;
	} else {
		return false;
	}
}

function ceilCoefficient($number, $rate = 100)
{
    $number = ceil($number);
    $rest = ceil($number / $rate) * $rate;

    return $rest;
}

function cutBrandText($str, $maxLen)
{
    $result = '';
    $langMore = LANGUAGE_ID == 'en' ? '[read more]' : '[читать дальше]';
    $langMoreHide = LANGUAGE_ID == 'en' ? '[hide]' : '[свернуть]';

    if (mb_strlen($str) > $maxLen){
        preg_match('/^.{0,'.$maxLen.'} .*?/ui', $str, $match);
        if (isset($match[0])){
            $result .= $match[0]."\r\n";
            $endText = mb_substr($str, mb_strlen($match[0]));
            $result .= '<a class="catalog-section__brand-text-more js-more-show" href="#">'.$langMore.'</a><span class="catalog-section__brand-text-hide js-brandtext-hide">'.$endText.'</span><a class="catalog-section__brand-text-more js-more-hide" style="display: none" href="#">'.$langMoreHide.'</a>';
        }
    } else {
        $result .= $str;
    }

    return $result;
}

// RUSH редирект
if (CSite::InDir('/catalog/footwear/shoe care/')) LocalRedirect('https://www.itkkit.com/catalog/footwear/shoe-care/', false, '301 Moved permanently');
if (CSite::InDir('/catalog/footwear/shoe+care/')) LocalRedirect('https://www.itkkit.com/catalog/footwear/shoe-care/', false, '301 Moved permanently');
if (CSite::InDir('/catalog/clothing/ t-shirts/')) LocalRedirect('https://www.itkkit.com/catalog/clothing/t-shirts/', false, '301 Moved permanently');
if (CSite::InDir('/catalog/footwear/+t-shirts/')) LocalRedirect('https://www.itkkit.com/catalog/clothing/t-shirts/', false, '301 Moved permanently');
if (CSite::InDir('/blog/2019/04/132137_revisiting-the-academy-of-art/')) LocalRedirect('https://www.itkkit.com/blog/2019/06/132137_revisiting-the-academy-of-art/', false, '301 Moved permanently');

if (CSite::InDir('/catalog/product/80903_filling-pieces-low-top-jasper-black/')) LocalRedirect('https://www.itkkit.ru/catalog/footwear/', false, '301 Moved permanently');
if (CSite::InDir('/catalog/product/80903_filling-pieces-low-top-jasper-black/'))  LocalRedirect('https://www.itkkit.ru/catalog/footwear/', false, '301 Moved permanently');
if (CSite::InDir('/catalog/product/80903_filling-pieces-low-top-jasper-black/')) LocalRedirect('https://www.itkkit.ru/catalog/footwear/', false, '301 Moved permanently');
if (CSite::InDir('/catalog/product/60233_krossovki-karhu-legend-aria-suede-grey-burgundy/')) LocalRedirect('https://www.itkkit.ru/catalog/footwear/sneakers/', false, '301 Moved permanently');
if (CSite::InDir('/catalog/product/60233_krossovki-karhu-legend-aria-suede-grey-burgundy/')) LocalRedirect('https://www.itkkit.ru/catalog/footwear/sneakers/', false, '301 Moved permanently');
if (CSite::InDir('/blog/2016/12/85695_christmas-gifts-guide/')) LocalRedirect('https://www.itkkit.ru/', false, '301 Moved permanently');
if (CSite::InDir('/blog/2017/12/103106_big-boy/')) LocalRedirect('https://intheblog.itkkit.com/itknow_en/', false, '301 Moved permanently');
if (CSite::InDir('/catalog/brand/comme-des-garcons/')) LocalRedirect('https://www.itkkit.ru/catalog/accessories/fragrances/', false, '301 Moved permanently');
if (CSite::InDir('/catalog/product/39847_dzhinsy-denim-demon-onne-raw/')) LocalRedirect('https://www.itkkit.ru/catalog/clothing/jeans/', false, '301 Moved permanently');
if (CSite::InDir('/catalog/product/40165_woolrich-classic-chino-fit-summer-stone/')) LocalRedirect('https://www.itkkit.ru/catalog/clothing/pants/', false, '301 Moved permanently');
if (CSite::InDir('/catalog/product/81426_dr-martens-1461-mono-white/')) LocalRedirect('https://www.itkkit.ru/catalog/footwear/brand/dr-martens/', false, '301 Moved permanently');
if (CSite::InDir('/catalog/clothing/outerwear/tag/zip-up/')) LocalRedirect('https://www.itkkit.com/catalog/brand/stussy/clothing/outerwear/tag/zip-up/', false, '301 Moved permanently');

// Redirect 24.01.2020
if ($_SERVER['HTTP_HOST'] == 'www.itkkit.com'){
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/civilist/white/')) LocalRedirect('https://www.itkkit.com/catalog/brand/civilist/clothing/t-shirts/white/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/sandals/brand/birkenstock/white/')) LocalRedirect('https://www.itkkit.com/catalog/brand/birkenstock/footwear/sandals/white/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/casablanca/brown/')) LocalRedirect('https://www.itkkit.com/catalog/brand/casablanca/clothing/sweats/brown/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/souvenirs/brand/carhartt-wip/silver/')) LocalRedirect('https://www.itkkit.com/catalog/brand/carhartt-wip/accessories/souvenirs/silver/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/carhartt-wip/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/carhartt-wip/clothing/t-shirts/multi/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/billionaire-boys-club/navy/')) LocalRedirect('https://www.itkkit.com/catalog/brand/billionaire-boys-club/clothing/t-shirts/navy/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/good-morning-tapes/purple/')) LocalRedirect('https://www.itkkit.com/catalog/brand/good-morning-tapes/clothing/longsleeves/purple/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/rassvet/navy/')) LocalRedirect('https://www.itkkit.com/catalog/brand/rassvet/accessories/hats/navy/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/casablanca/green/')) LocalRedirect('https://www.itkkit.com/catalog/brand/casablanca/clothing/pants/green/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/sporadic/navy/')) LocalRedirect('https://www.itkkit.com/catalog/brand/sporadic/clothing/outerwear/navy/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/carrots/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/carrots/clothing/t-shirts/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shirts/brand/engineered-garments/red/')) LocalRedirect('https://www.itkkit.com/catalog/brand/engineered-garments/clothing/shirts/red/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/sss-world-corp/yellow/')) LocalRedirect('https://www.itkkit.com/catalog/brand/sss-world-corp/clothing/sweats/yellow/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/grooming/brand/marvis/orange/')) LocalRedirect('https://www.itkkit.com/catalog/brand/marvis/accessories/grooming/orange/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/daily-paper/grey/')) LocalRedirect('https://www.itkkit.com/catalog/brand/daily-paper/clothing/pants/grey/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/pop-trading-company/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/pop-trading-company/accessories/hats/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/sneakers/brand/fila-woman/red/')) LocalRedirect('https://www.itkkit.com/catalog/brand/fila-woman/footwear/sneakers/red/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/daily-paper/white/')) LocalRedirect('https://www.itkkit.com/catalog/brand/daily-paper/clothing/longsleeves/white/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/bronze-56k/orange/')) LocalRedirect('https://www.itkkit.com/catalog/brand/bronze-56k/clothing/pants/orange/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/resort-corps/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/resort-corps/clothing/pants/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/pleasures/green/')) LocalRedirect('https://www.itkkit.com/catalog/brand/pleasures/clothing/longsleeves/green/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/souvenirs/brand/polythene-optics/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/polythene-optics/accessories/souvenirs/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/rave-skateboards/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/rave-skateboards/accessories/hats/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/kappa/purple/')) LocalRedirect('https://www.itkkit.com/catalog/brand/kappa/clothing/pants/purple/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/fragrances/brand/comme-des-garcons-parfum/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/comme-des-garcons-parfum/accessories/fragrances/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/ripndip/green/')) LocalRedirect('https://www.itkkit.com/catalog/brand/ripndip/clothing/longsleeves/green/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/fucking-awesome/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/fucking-awesome/clothing/longsleeves/multi/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/still-good/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/still-good/clothing/longsleeves/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/rokit/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/rokit/clothing/longsleeves/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/shoes/brand/diemme/grey/')) LocalRedirect('https://www.itkkit.com/catalog/brand/diemme/footwear/shoes/grey/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/the-trilogy-tapes/blue/')) LocalRedirect('https://www.itkkit.com/catalog/brand/the-trilogy-tapes/clothing/longsleeves/blue/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/idea/white/')) LocalRedirect('https://www.itkkit.com/catalog/brand/idea/clothing/t-shirts/white/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shirts/brand/engineered-garments/grey/')) LocalRedirect('https://www.itkkit.com/catalog/brand/engineered-garments/clothing/shirts/grey/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/fucking-awesome/grey/')) LocalRedirect('https://www.itkkit.com/catalog/brand/fucking-awesome/clothing/t-shirts/grey/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/h-las/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/h-las/clothing/sweats/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/aded/white/')) LocalRedirect('https://www.itkkit.com/catalog/brand/aded/clothing/t-shirts/white/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/kappa/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/kappa/accessories/bags/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/grind-london/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/grind-london/clothing/hoodies/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/tommy-jeans-woman/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/tommy-jeans-woman/clothing/sweats/multi/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/socks/brand/carne-bollente/white/')) LocalRedirect('https://www.itkkit.com/catalog/brand/carne-bollente/accessories/socks/white/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/stussy/brown/')) LocalRedirect('https://www.itkkit.com/catalog/brand/stussy/clothing/outerwear/brown/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/stussy/pink/')) LocalRedirect('https://www.itkkit.com/catalog/brand/stussy/clothing/pants/pink/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/socks/brand/civilist/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/civilist/accessories/socks/multi/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/souvenirs/brand/carhartt-wip/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/carhartt-wip/accessories/souvenirs/multi/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/ripndip/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/ripndip/clothing/hoodies/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/rassvet/purple/')) LocalRedirect('https://www.itkkit.com/catalog/brand/rassvet/clothing/sweats/purple/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/nasaseasons/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/nasaseasons/clothing/hoodies/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/arte-antwerp/grey/')) LocalRedirect('https://www.itkkit.com/catalog/brand/arte-antwerp/clothing/sweats/grey/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/bronze-56k/green/')) LocalRedirect('https://www.itkkit.com/catalog/brand/bronze-56k/clothing/longsleeves/green/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shorts/brand/h-las/blue/')) LocalRedirect('https://www.itkkit.com/catalog/brand/h-las/clothing/shorts/blue/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/call-me-917/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/call-me-917/clothing/t-shirts/multi/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/wallets/brand/fjallraven/grey/')) LocalRedirect('https://www.itkkit.com/catalog/brand/fjallraven/accessories/wallets/grey/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/still-good/white/')) LocalRedirect('https://www.itkkit.com/catalog/brand/still-good/clothing/t-shirts/white/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/civilist/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/civilist/accessories/bags/multi/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/stussy/grey/')) LocalRedirect('https://www.itkkit.com/catalog/brand/stussy/accessories/hats/grey/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/chinatown-market/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/chinatown-market/clothing/hoodies/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/aries/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/aries/clothing/sweats/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/still-good/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/still-good/clothing/longsleeves/multi/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/wallets/brand/carne-bollente/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/carne-bollente/accessories/wallets/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/sneakers/brand/premiata/brown/')) LocalRedirect('https://www.itkkit.com/catalog/brand/premiata/footwear/sneakers/brown/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/knitwear/brand/p-a-m/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/p-a-m/clothing/knitwear/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shirts/brand/maharishi/orange/')) LocalRedirect('https://www.itkkit.com/catalog/brand/maharishi/clothing/shirts/orange/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/socks/brand/ripndip/mint/')) LocalRedirect('https://www.itkkit.com/catalog/brand/ripndip/accessories/socks/mint/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/ripndip/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/ripndip/clothing/t-shirts/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/tommy-jeans/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/tommy-jeans/clothing/outerwear/multi/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/carrots/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/carrots/clothing/hoodies/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/rave-skateboards/camo/')) LocalRedirect('https://www.itkkit.com/catalog/brand/rave-skateboards/clothing/pants/camo/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/sss-world-corp/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/sss-world-corp/clothing/longsleeves/multi/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/bronze-56k/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/bronze-56k/clothing/pants/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/norse-projects/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/norse-projects/clothing/pants/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/champion-woman/white/')) LocalRedirect('https://www.itkkit.com/catalog/brand/champion-woman/accessories/bags/white/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/daily-paper/tie%20dye/')) LocalRedirect('https://www.itkkit.com/catalog/brand/daily-paper/clothing/pants/tie%20dye/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/call-me-917/green/')) LocalRedirect('https://www.itkkit.com/catalog/brand/call-me-917/clothing/longsleeves/green/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/babylon-la/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/babylon-la/clothing/longsleeves/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/kappa/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/kappa/clothing/pants/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/fucking-awesome/navy/')) LocalRedirect('https://www.itkkit.com/catalog/brand/fucking-awesome/clothing/hoodies/navy/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/knitwear/brand/aries/grey/')) LocalRedirect('https://www.itkkit.com/catalog/brand/aries/clothing/knitwear/grey/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/rassvet/red/')) LocalRedirect('https://www.itkkit.com/catalog/brand/rassvet/clothing/t-shirts/red/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/sneakers/brand/adidas-originals/white/')) LocalRedirect('https://www.itkkit.com/catalog/brand/adidas-originals/footwear/sneakers/white/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/souvenirs/brand/stussy/white/')) LocalRedirect('https://www.itkkit.com/catalog/brand/stussy/accessories/souvenirs/white/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/polar-skate-co/navy/')) LocalRedirect('https://www.itkkit.com/catalog/brand/polar-skate-co/clothing/t-shirts/navy/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/pleasures/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/pleasures/clothing/pants/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/civilist/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/civilist/clothing/sweats/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/daily-paper/green/')) LocalRedirect('https://www.itkkit.com/catalog/brand/daily-paper/clothing/pants/green/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/vests/brand/maharishi/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/maharishi/clothing/vests/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/knitwear/brand/arte-antwerp/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/arte-antwerp/clothing/knitwear/multi/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/sneakers/brand/fila-woman/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/fila-woman/footwear/sneakers/multi/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/stussy/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/stussy/clothing/pants/multi/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/souvenirs/brand/stussy/yellow/')) LocalRedirect('https://www.itkkit.com/catalog/brand/stussy/accessories/souvenirs/yellow/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/universal-works/sand/')) LocalRedirect('https://www.itkkit.com/catalog/brand/universal-works/clothing/outerwear/sand/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/wallets/brand/comme-des-gar-ons-wallets/brown/')) LocalRedirect('https://www.itkkit.com/catalog/brand/comme-des-gar-ons-wallets/accessories/wallets/brown/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/patagonia/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/patagonia/clothing/t-shirts/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/socks/brand/still-good/white/')) LocalRedirect('https://www.itkkit.com/catalog/brand/still-good/accessories/socks/white/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/barbour/olive/')) LocalRedirect('https://www.itkkit.com/catalog/brand/barbour/accessories/hats/olive/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/engineered-garments/navy/')) LocalRedirect('https://www.itkkit.com/catalog/brand/engineered-garments/accessories/bags/navy/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/p-a-m/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/p-a-m/clothing/pants/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/know-wave/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/know-wave/clothing/t-shirts/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/gloves/brand/the-north-face/camo/')) LocalRedirect('https://www.itkkit.com/catalog/brand/the-north-face/accessories/gloves/camo/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/skateboarding/brand/palace-skateboards/green/')) LocalRedirect('https://www.itkkit.com/catalog/brand/palace-skateboards/accessories/skateboarding/green/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/sneakers/brand/fila/white/')) LocalRedirect('https://www.itkkit.com/catalog/brand/fila/footwear/sneakers/white/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/daily-paper/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/daily-paper/clothing/outerwear/multi/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/sandals/brand/pleasures/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/pleasures/footwear/sandals/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/the-north-face/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/the-north-face/accessories/hats/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/the-north-face/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/the-north-face/accessories/bags/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/fucking-awesome/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/fucking-awesome/clothing/hoodies/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/stussy/violet/')) LocalRedirect('https://www.itkkit.com/catalog/brand/stussy/clothing/pants/violet/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/taikan/green/')) LocalRedirect('https://www.itkkit.com/catalog/brand/taikan/accessories/bags/green/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/alpha-industries/camo/')) LocalRedirect('https://www.itkkit.com/catalog/brand/alpha-industries/clothing/sweats/camo/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/fragrances/brand/sporadic/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/sporadic/accessories/fragrances/multi/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/rassvet/navy/')) LocalRedirect('https://www.itkkit.com/catalog/brand/rassvet/clothing/sweats/navy/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/m-rc-noir/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/m-rc-noir/clothing/t-shirts/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/grooming/brand/baxter-of-california/blue/')) LocalRedirect('https://www.itkkit.com/catalog/brand/baxter-of-california/accessories/grooming/blue/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shirts/brand/daily-paper/blue/')) LocalRedirect('https://www.itkkit.com/catalog/brand/daily-paper/clothing/shirts/blue/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/tommy-jeans/grey/')) LocalRedirect('https://www.itkkit.com/catalog/brand/tommy-jeans/clothing/t-shirts/grey/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/still-good/blue/')) LocalRedirect('https://www.itkkit.com/catalog/brand/still-good/clothing/hoodies/blue/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/pop-trading-company/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/pop-trading-company/clothing/outerwear/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/fjallraven/green/')) LocalRedirect('https://www.itkkit.com/catalog/brand/fjallraven/accessories/bags/green/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/sporadic/burgundy/')) LocalRedirect('https://www.itkkit.com/catalog/brand/sporadic/clothing/longsleeves/burgundy/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/pop-trading-company/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/pop-trading-company/clothing/pants/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/knitwear/brand/stussy/navy/')) LocalRedirect('https://www.itkkit.com/catalog/brand/stussy/clothing/knitwear/navy/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/knitwear/brand/nigel-cabourn/grey/')) LocalRedirect('https://www.itkkit.com/catalog/brand/nigel-cabourn/clothing/knitwear/grey/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/pop-trading-company/pink/')) LocalRedirect('https://www.itkkit.com/catalog/brand/pop-trading-company/clothing/t-shirts/pink/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/tommy-jeans/blue/')) LocalRedirect('https://www.itkkit.com/catalog/brand/tommy-jeans/accessories/bags/blue/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/sneakers/brand/tommy-jeans-woman/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/tommy-jeans-woman/footwear/sneakers/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/stussy/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/stussy/clothing/longsleeves/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/stussy/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/stussy/clothing/sweats/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/maharishi/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/maharishi/clothing/t-shirts/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/fucking-awesome/royal/')) LocalRedirect('https://www.itkkit.com/catalog/brand/fucking-awesome/clothing/t-shirts/royal/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/souvenirs/brand/carne-bollente/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/carne-bollente/accessories/souvenirs/multi/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/assid/white/')) LocalRedirect('https://www.itkkit.com/catalog/brand/assid/clothing/t-shirts/white/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/sneakers/brand/fila-woman/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/fila-woman/footwear/sneakers/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/jeans/brand/polar-skate-co/red/')) LocalRedirect('https://www.itkkit.com/catalog/brand/polar-skate-co/clothing/jeans/red/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/rave-skateboards/blue/')) LocalRedirect('https://www.itkkit.com/catalog/brand/rave-skateboards/accessories/bags/blue/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/sandals/brand/keen/olive/')) LocalRedirect('https://www.itkkit.com/catalog/brand/keen/footwear/sandals/olive/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/carrots/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/carrots/clothing/pants/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/parkas/brand/m-rc-noir/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/m-rc-noir/clothing/parkas/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/needles/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/needles/clothing/sweats/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/socks/brand/civilist/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/civilist/accessories/socks/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/m-rc-noir/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/m-rc-noir/clothing/hoodies/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/call-me-917/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/call-me-917/clothing/t-shirts/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/billionaire-boys-club/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/billionaire-boys-club/clothing/longsleeves/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/belts/brand/comme-des-gar-ons-wallets/red/')) LocalRedirect('https://www.itkkit.com/catalog/brand/comme-des-gar-ons-wallets/accessories/belts/red/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/babylon-la/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/babylon-la/clothing/longsleeves/multi/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/bronze-56k/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/bronze-56k/clothing/hoodies/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/rave-skateboards/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/rave-skateboards/accessories/bags/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/resort-corps/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/resort-corps/clothing/sweats/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/aries/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/aries/clothing/pants/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/sporadic/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/sporadic/clothing/t-shirts/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/rassvet/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/rassvet/clothing/t-shirts/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/socks/brand/champion-reverse-weave/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/champion-reverse-weave/accessories/socks/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/idea/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/idea/accessories/bags/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/rokit/orange/')) LocalRedirect('https://www.itkkit.com/catalog/brand/rokit/clothing/longsleeves/orange/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/chinatown-market/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/chinatown-market/clothing/t-shirts/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/champion-woman/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/champion-woman/clothing/outerwear/multi/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/shoes/brand/mephisto/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/mephisto/footwear/shoes/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/polar-skate-co/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/polar-skate-co/accessories/bags/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/p-a-m/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/p-a-m/clothing/t-shirts/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/champion-reverse-weave/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/champion-reverse-weave/accessories/hats/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/champion-reverse-weave/blue/')) LocalRedirect('https://www.itkkit.com/catalog/brand/champion-reverse-weave/clothing/hoodies/blue/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/bombers/brand/liam-hodges/green/')) LocalRedirect('https://www.itkkit.com/catalog/brand/liam-hodges/clothing/bombers/green/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/lack-of-guidance/grey/')) LocalRedirect('https://www.itkkit.com/catalog/brand/lack-of-guidance/clothing/longsleeves/grey/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/aries/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/aries/clothing/t-shirts/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/sneakers/brand/adidas-originals/green/')) LocalRedirect('https://www.itkkit.com/catalog/brand/adidas-originals/footwear/sneakers/green/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shirts/brand/needles/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/needles/clothing/shirts/multi/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/m-rc-noir/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/m-rc-noir/accessories/bags/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/fucking-awesome/white/')) LocalRedirect('https://www.itkkit.com/catalog/brand/fucking-awesome/clothing/t-shirts/white/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/souvenirs/brand/fucking-awesome/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/fucking-awesome/accessories/souvenirs/multi/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/patta/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/patta/clothing/pants/multi/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/bronze-56k/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/bronze-56k/clothing/t-shirts/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/daily-paper/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/daily-paper/clothing/sweats/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/polar-skate-co/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/polar-skate-co/accessories/hats/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/socks/brand/ripndip/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/ripndip/accessories/socks/multi/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/alpha-industries/grey/')) LocalRedirect('https://www.itkkit.com/catalog/brand/alpha-industries/clothing/hoodies/grey/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/sneakers/brand/fila/red/')) LocalRedirect('https://www.itkkit.com/catalog/brand/fila/footwear/sneakers/red/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/ripndip/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/ripndip/clothing/hoodies/multi/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/h-las/white/')) LocalRedirect('https://www.itkkit.com/catalog/brand/h-las/accessories/hats/white/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/vests/brand/human-with-attitude/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/human-with-attitude/clothing/vests/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/sneakers/brand/hoka-one-one/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/hoka-one-one/footwear/sneakers/multi/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/billionaire-boys-club/grey/')) LocalRedirect('https://www.itkkit.com/catalog/brand/billionaire-boys-club/clothing/hoodies/grey/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/stussy/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/stussy/clothing/t-shirts/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shirts/brand/comme-des-gar-ons-shirt/white/')) LocalRedirect('https://www.itkkit.com/catalog/brand/comme-des-gar-ons-shirt/clothing/shirts/white/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/stussy/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/stussy/clothing/pants/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/souvenirs/brand/sporadic/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/sporadic/accessories/souvenirs/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/universal-works/olive/')) LocalRedirect('https://www.itkkit.com/catalog/brand/universal-works/clothing/pants/olive/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/m-rc-noir/purple/')) LocalRedirect('https://www.itkkit.com/catalog/brand/m-rc-noir/accessories/bags/purple/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/ripndip/blue/')) LocalRedirect('https://www.itkkit.com/catalog/brand/ripndip/clothing/t-shirts/blue/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/socks/brand/rassvet/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/rassvet/accessories/socks/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/daily-paper/purple/')) LocalRedirect('https://www.itkkit.com/catalog/brand/daily-paper/clothing/t-shirts/purple/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/ripndip/gold/')) LocalRedirect('https://www.itkkit.com/catalog/brand/ripndip/clothing/t-shirts/gold/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/wallets/brand/civilist/white/')) LocalRedirect('https://www.itkkit.com/catalog/brand/civilist/accessories/wallets/white/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/dr-le-de-monsieur/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/dr-le-de-monsieur/accessories/hats/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/daily-paper/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/daily-paper/clothing/outerwear/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/pop-trading-company/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/pop-trading-company/clothing/sweats/multi/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/h-las/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/h-las/clothing/t-shirts/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/souvenirs/brand/ripndip/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/ripndip/accessories/souvenirs/multi/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/tommy-jeans/white/')) LocalRedirect('https://www.itkkit.com/catalog/brand/tommy-jeans/accessories/hats/white/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/sneakers/brand/tommy-jeans/white/')) LocalRedirect('https://www.itkkit.com/catalog/brand/tommy-jeans/footwear/sneakers/white/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/wallets/brand/carne-bollente/pink/')) LocalRedirect('https://www.itkkit.com/catalog/brand/carne-bollente/accessories/wallets/pink/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/knitwear/brand/pleasures/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/pleasures/clothing/knitwear/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/sporadic/grey/')) LocalRedirect('https://www.itkkit.com/catalog/brand/sporadic/clothing/t-shirts/grey/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/vests/brand/human-with-attitude/reflective/')) LocalRedirect('https://www.itkkit.com/catalog/brand/human-with-attitude/clothing/vests/reflective/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/daily-paper/grey/')) LocalRedirect('https://www.itkkit.com/catalog/brand/daily-paper/clothing/sweats/grey/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/souvenirs/brand/barbour/green/')) LocalRedirect('https://www.itkkit.com/catalog/brand/barbour/accessories/souvenirs/green/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/casablanca/brown/')) LocalRedirect('https://www.itkkit.com/catalog/brand/casablanca/clothing/pants/brown/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/h-las/blue/')) LocalRedirect('https://www.itkkit.com/catalog/brand/h-las/clothing/pants/blue/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/sergio-tacchini/navy/')) LocalRedirect('https://www.itkkit.com/catalog/brand/sergio-tacchini/clothing/sweats/navy/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/the-north-face/red/')) LocalRedirect('https://www.itkkit.com/catalog/brand/the-north-face/accessories/bags/red/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/souvenirs/brand/fjallraven/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/fjallraven/accessories/souvenirs/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/shoes/brand/barbour/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/barbour/footwear/shoes/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/vests/brand/rave-skateboards/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/rave-skateboards/clothing/vests/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/ripndip/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/ripndip/clothing/t-shirts/multi/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/daily-paper/white/')) LocalRedirect('https://www.itkkit.com/catalog/brand/daily-paper/clothing/pants/white/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/rassvet/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/rassvet/accessories/bags/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/sneakers/brand/new-balance/grey/')) LocalRedirect('https://www.itkkit.com/catalog/brand/new-balance/footwear/sneakers/grey/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/daily-paper/tie%20dye/')) LocalRedirect('https://www.itkkit.com/catalog/brand/daily-paper/clothing/outerwear/tie%20dye/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/tommy-jeans-woman/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/tommy-jeans-woman/clothing/longsleeves/multi/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/pop-trading-company/red/')) LocalRedirect('https://www.itkkit.com/catalog/brand/pop-trading-company/clothing/outerwear/red/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/lack-of-guidance/white/')) LocalRedirect('https://www.itkkit.com/catalog/brand/lack-of-guidance/clothing/t-shirts/white/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/stussy/paisley/')) LocalRedirect('https://www.itkkit.com/catalog/brand/stussy/clothing/pants/paisley/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/barbour/brown/')) LocalRedirect('https://www.itkkit.com/catalog/brand/barbour/accessories/hats/brown/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/kappa/red/')) LocalRedirect('https://www.itkkit.com/catalog/brand/kappa/clothing/pants/red/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/m-rc-noir/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/m-rc-noir/accessories/bags/multi/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shirts/brand/engineered-garments/purple/')) LocalRedirect('https://www.itkkit.com/catalog/brand/engineered-garments/clothing/shirts/purple/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/souvenirs/brand/primus/pink/')) LocalRedirect('https://www.itkkit.com/catalog/brand/primus/accessories/souvenirs/pink/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/still-good/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/still-good/accessories/hats/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/souvenirs/brand/primus/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/primus/accessories/souvenirs/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/polar-skate-co/blue/')) LocalRedirect('https://www.itkkit.com/catalog/brand/polar-skate-co/accessories/hats/blue/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/daily-paper/white/')) LocalRedirect('https://www.itkkit.com/catalog/brand/daily-paper/clothing/t-shirts/white/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/knitwear/brand/polar-skate-co/green/')) LocalRedirect('https://www.itkkit.com/catalog/brand/polar-skate-co/clothing/knitwear/green/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/wallets/brand/fjallraven/camo/')) LocalRedirect('https://www.itkkit.com/catalog/brand/fjallraven/accessories/wallets/camo/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/human-with-attitude/lavender/')) LocalRedirect('https://www.itkkit.com/catalog/brand/human-with-attitude/clothing/pants/lavender/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/dr-le-de-monsieur/grey/')) LocalRedirect('https://www.itkkit.com/catalog/brand/dr-le-de-monsieur/clothing/hoodies/grey/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/universal-works/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/universal-works/clothing/pants/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/stussy/orange/')) LocalRedirect('https://www.itkkit.com/catalog/brand/stussy/clothing/hoodies/orange/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/tommy-jeans/orange/')) LocalRedirect('https://www.itkkit.com/catalog/brand/tommy-jeans/clothing/hoodies/orange/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/polar-skate-co/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/polar-skate-co/clothing/outerwear/multi/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/jeans/brand/needles/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/needles/clothing/jeans/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/arte-antwerp/navy/')) LocalRedirect('https://www.itkkit.com/catalog/brand/arte-antwerp/clothing/pants/navy/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/souvenirs/brand/primus/silver/')) LocalRedirect('https://www.itkkit.com/catalog/brand/primus/accessories/souvenirs/silver/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/rave-skateboards/purple/')) LocalRedirect('https://www.itkkit.com/catalog/brand/rave-skateboards/accessories/bags/purple/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/socks/brand/aries/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/aries/accessories/socks/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/billionaire-boys-club/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/billionaire-boys-club/clothing/outerwear/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/knitwear/brand/norse-projects/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/norse-projects/clothing/knitwear/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/wallets/brand/comme-des-gar-ons-wallets/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/comme-des-gar-ons-wallets/accessories/wallets/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/patagonia/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/patagonia/accessories/bags/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/m-rc-noir/blue/')) LocalRedirect('https://www.itkkit.com/catalog/brand/m-rc-noir/clothing/outerwear/blue/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/gasius/pink/')) LocalRedirect('https://www.itkkit.com/catalog/brand/gasius/clothing/t-shirts/pink/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/rassvet/grey/')) LocalRedirect('https://www.itkkit.com/catalog/brand/rassvet/clothing/t-shirts/grey/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/skateboarding/brand/alltimers/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/alltimers/accessories/skateboarding/multi/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/casablanca/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/casablanca/clothing/hoodies/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/polythene-optics/red/')) LocalRedirect('https://www.itkkit.com/catalog/brand/polythene-optics/clothing/pants/red/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/knitwear/brand/fucking-awesome/orange/')) LocalRedirect('https://www.itkkit.com/catalog/brand/fucking-awesome/clothing/knitwear/orange/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/daily-paper/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/daily-paper/clothing/hoodies/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shorts/brand/tommy-jeans/gold/')) LocalRedirect('https://www.itkkit.com/catalog/brand/tommy-jeans/clothing/shorts/gold/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/rassvet/burgundy/')) LocalRedirect('https://www.itkkit.com/catalog/brand/rassvet/clothing/sweats/burgundy/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/universal-works/navy/')) LocalRedirect('https://www.itkkit.com/catalog/brand/universal-works/clothing/pants/navy/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/souvenirs/brand/barbour/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/barbour/accessories/souvenirs/multi/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/socks/brand/mki-miyuki-zoku/orange/')) LocalRedirect('https://www.itkkit.com/catalog/brand/mki-miyuki-zoku/accessories/socks/orange/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/sandals/brand/fila-woman/sand/')) LocalRedirect('https://www.itkkit.com/catalog/brand/fila-woman/footwear/sandals/sand/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/mki-miyuki-zoku/red/')) LocalRedirect('https://www.itkkit.com/catalog/brand/mki-miyuki-zoku/clothing/pants/red/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/socks/brand/rassvet/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/rassvet/accessories/socks/multi/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/kappa/purple/')) LocalRedirect('https://www.itkkit.com/catalog/brand/kappa/clothing/sweats/purple/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/sandals/brand/the-north-face/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/the-north-face/footwear/sandals/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/sss-world-corp/yellow/')) LocalRedirect('https://www.itkkit.com/catalog/brand/sss-world-corp/clothing/hoodies/yellow/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/lack-of-guidance/navy/')) LocalRedirect('https://www.itkkit.com/catalog/brand/lack-of-guidance/clothing/longsleeves/navy/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/sandals/brand/fila-woman/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/fila-woman/footwear/sandals/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/rassvet/purple/')) LocalRedirect('https://www.itkkit.com/catalog/brand/rassvet/clothing/t-shirts/purple/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/billionaire-boys-club/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/billionaire-boys-club/clothing/pants/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/grooming/brand/marvis/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/marvis/accessories/grooming/multi/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/bombers/brand/alpha-industries/green/')) LocalRedirect('https://www.itkkit.com/catalog/brand/alpha-industries/clothing/bombers/green/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/champion-reverse-weave/grey/')) LocalRedirect('https://www.itkkit.com/catalog/brand/champion-reverse-weave/clothing/hoodies/grey/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/pop-trading-company/navy/')) LocalRedirect('https://www.itkkit.com/catalog/brand/pop-trading-company/clothing/t-shirts/navy/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/kappa/green/')) LocalRedirect('https://www.itkkit.com/catalog/brand/kappa/clothing/sweats/green/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/assid/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/assid/clothing/t-shirts/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/dr-le-de-monsieur/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/dr-le-de-monsieur/clothing/outerwear/multi/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shirts/brand/chinatown-market/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/chinatown-market/clothing/shirts/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/comme-des-gar-ons-play/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/comme-des-gar-ons-play/clothing/longsleeves/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/the-trilogy-tapes/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/the-trilogy-tapes/clothing/t-shirts/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/sneakers/brand/hoka-one-one/gold/')) LocalRedirect('https://www.itkkit.com/catalog/brand/hoka-one-one/footwear/sneakers/gold/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/still-good/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/still-good/accessories/bags/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shirts/brand/sss-world-corp/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/sss-world-corp/clothing/shirts/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/know-wave/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/know-wave/clothing/longsleeves/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/souvenirs/brand/h-las/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/h-las/accessories/souvenirs/multi/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/resort-corps/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/resort-corps/clothing/hoodies/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/sneakers/brand/fila-woman/silver/')) LocalRedirect('https://www.itkkit.com/catalog/brand/fila-woman/footwear/sneakers/silver/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/casablanca/blue/')) LocalRedirect('https://www.itkkit.com/catalog/brand/casablanca/clothing/outerwear/blue/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/human-with-attitude/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/human-with-attitude/clothing/outerwear/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/the-north-face/blue/')) LocalRedirect('https://www.itkkit.com/catalog/brand/the-north-face/clothing/outerwear/blue/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shirts/brand/norse-projects/white/')) LocalRedirect('https://www.itkkit.com/catalog/brand/norse-projects/clothing/shirts/white/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/h-las/green/')) LocalRedirect('https://www.itkkit.com/catalog/brand/h-las/clothing/pants/green/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/billionaire-boys-club/camo/')) LocalRedirect('https://www.itkkit.com/catalog/brand/billionaire-boys-club/clothing/t-shirts/camo/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/sneakers/brand/adidas-originals/grey/')) LocalRedirect('https://www.itkkit.com/catalog/brand/adidas-originals/footwear/sneakers/grey/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/assid/white/')) LocalRedirect('https://www.itkkit.com/catalog/brand/assid/clothing/longsleeves/white/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shirts/brand/billionaire-boys-club/orange/')) LocalRedirect('https://www.itkkit.com/catalog/brand/billionaire-boys-club/clothing/shirts/orange/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/shoes/brand/mephisto/sand/')) LocalRedirect('https://www.itkkit.com/catalog/brand/mephisto/footwear/shoes/sand/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shirts/brand/fucking-awesome/white/')) LocalRedirect('https://www.itkkit.com/catalog/brand/fucking-awesome/clothing/shirts/white/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shirts/brand/stussy/red/')) LocalRedirect('https://www.itkkit.com/catalog/brand/stussy/clothing/shirts/red/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/socks/brand/ripndip/gold/')) LocalRedirect('https://www.itkkit.com/catalog/brand/ripndip/accessories/socks/gold/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/socks/brand/champion-reverse-weave/yellow/')) LocalRedirect('https://www.itkkit.com/catalog/brand/champion-reverse-weave/accessories/socks/yellow/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/stussy/navy/')) LocalRedirect('https://www.itkkit.com/catalog/brand/stussy/clothing/pants/navy/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/aries/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/aries/clothing/hoodies/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/chinatown-market/white/')) LocalRedirect('https://www.itkkit.com/catalog/brand/chinatown-market/clothing/longsleeves/white/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/the-north-face/yellow/')) LocalRedirect('https://www.itkkit.com/catalog/brand/the-north-face/accessories/bags/yellow/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/civilist/orange/')) LocalRedirect('https://www.itkkit.com/catalog/brand/civilist/clothing/t-shirts/orange/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/bronze-56k/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/bronze-56k/accessories/hats/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/p-a-m/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/p-a-m/clothing/outerwear/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/sneakers/brand/comme-des-gar-ons-play/beige/')) LocalRedirect('https://www.itkkit.com/catalog/brand/comme-des-gar-ons-play/footwear/sneakers/beige/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/polar-skate-co/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/polar-skate-co/clothing/t-shirts/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/daily-paper/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/daily-paper/accessories/bags/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/grooming/brand/marvis/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/marvis/accessories/grooming/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shirts/brand/fucking-awesome/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/fucking-awesome/clothing/shirts/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/wallets/brand/comme-des-gar-ons-wallets/red/')) LocalRedirect('https://www.itkkit.com/catalog/brand/comme-des-gar-ons-wallets/accessories/wallets/red/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/knitwear/brand/fucking-awesome/white/')) LocalRedirect('https://www.itkkit.com/catalog/brand/fucking-awesome/clothing/knitwear/white/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/still-good/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/still-good/clothing/t-shirts/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/human-with-attitude/tie%20dye/')) LocalRedirect('https://www.itkkit.com/catalog/brand/human-with-attitude/clothing/pants/tie%20dye/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/comme-des-gar-ons-play/grey/')) LocalRedirect('https://www.itkkit.com/catalog/brand/comme-des-gar-ons-play/clothing/t-shirts/grey/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/nasaseasons/red/')) LocalRedirect('https://www.itkkit.com/catalog/brand/nasaseasons/clothing/t-shirts/red/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/daily-paper/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/daily-paper/clothing/hoodies/multi/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/sneakers/brand/premiata/navy/')) LocalRedirect('https://www.itkkit.com/catalog/brand/premiata/footwear/sneakers/navy/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/skateboarding/brand/rassvet/blue/')) LocalRedirect('https://www.itkkit.com/catalog/brand/rassvet/accessories/skateboarding/blue/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/sss-world-corp/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/sss-world-corp/clothing/pants/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/rassvet/blue/')) LocalRedirect('https://www.itkkit.com/catalog/brand/rassvet/clothing/pants/blue/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/polar-skate-co/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/polar-skate-co/clothing/pants/multi/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/pop-trading-company/sand/')) LocalRedirect('https://www.itkkit.com/catalog/brand/pop-trading-company/accessories/hats/sand/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/pop-trading-company/red/')) LocalRedirect('https://www.itkkit.com/catalog/brand/pop-trading-company/accessories/hats/red/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/rassvet/navy/')) LocalRedirect('https://www.itkkit.com/catalog/brand/rassvet/clothing/t-shirts/navy/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/polar-skate-co/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/polar-skate-co/clothing/outerwear/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/knitwear/brand/p-a-m/camo/')) LocalRedirect('https://www.itkkit.com/catalog/brand/p-a-m/clothing/knitwear/camo/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/souvenirs/brand/fucking-awesome/gold/')) LocalRedirect('https://www.itkkit.com/catalog/brand/fucking-awesome/accessories/souvenirs/gold/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/alpha-industries/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/alpha-industries/clothing/t-shirts/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/carne-bollente/white/')) LocalRedirect('https://www.itkkit.com/catalog/brand/carne-bollente/clothing/t-shirts/white/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/good-morning-tapes/gold/')) LocalRedirect('https://www.itkkit.com/catalog/brand/good-morning-tapes/clothing/longsleeves/gold/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shorts/brand/daily-paper/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/daily-paper/clothing/shorts/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/knitwear/brand/stussy/berry/')) LocalRedirect('https://www.itkkit.com/catalog/brand/stussy/clothing/knitwear/berry/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/m-rc-noir/orange/')) LocalRedirect('https://www.itkkit.com/catalog/brand/m-rc-noir/accessories/bags/orange/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shorts/brand/pleasures/blue/')) LocalRedirect('https://www.itkkit.com/catalog/brand/pleasures/clothing/shorts/blue/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shirts/brand/grind-london/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/grind-london/clothing/shirts/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/polar-skate-co/white/')) LocalRedirect('https://www.itkkit.com/catalog/brand/polar-skate-co/clothing/t-shirts/white/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/human-with-attitude/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/human-with-attitude/clothing/t-shirts/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/socks/brand/polythene-optics/white/')) LocalRedirect('https://www.itkkit.com/catalog/brand/polythene-optics/accessories/socks/white/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/pleasures/sand/')) LocalRedirect('https://www.itkkit.com/catalog/brand/pleasures/clothing/longsleeves/sand/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/taikan/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/taikan/accessories/bags/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/tommy-jeans/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/tommy-jeans/accessories/bags/multi/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/socks/brand/carne-bollente/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/carne-bollente/accessories/socks/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/knitwear/brand/needles/purple/')) LocalRedirect('https://www.itkkit.com/catalog/brand/needles/clothing/knitwear/purple/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/pleasures/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/pleasures/clothing/hoodies/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/being-hunted/red/')) LocalRedirect('https://www.itkkit.com/catalog/brand/being-hunted/clothing/t-shirts/red/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/wallets/brand/fjallraven/green/')) LocalRedirect('https://www.itkkit.com/catalog/brand/fjallraven/accessories/wallets/green/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/rave-skateboards/sand/')) LocalRedirect('https://www.itkkit.com/catalog/brand/rave-skateboards/accessories/bags/sand/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/dr-le-de-monsieur/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/dr-le-de-monsieur/clothing/sweats/multi/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/stussy/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/stussy/clothing/hoodies/multi/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/jeans/brand/tommy-jeans-woman/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/tommy-jeans-woman/clothing/jeans/multi/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/tommy-jeans/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/tommy-jeans/clothing/t-shirts/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/human-with-attitude/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/human-with-attitude/clothing/pants/multi/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/souvenirs/brand/stussy/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/stussy/accessories/souvenirs/multi/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/vests/brand/gasius/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/gasius/clothing/vests/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/platformx/white/')) LocalRedirect('https://www.itkkit.com/catalog/brand/platformx/clothing/sweats/white/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/bronze-56k/brown/')) LocalRedirect('https://www.itkkit.com/catalog/brand/bronze-56k/clothing/pants/brown/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/nasaseasons/white/')) LocalRedirect('https://www.itkkit.com/catalog/brand/nasaseasons/clothing/hoodies/white/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/m-rc-noir/green/')) LocalRedirect('https://www.itkkit.com/catalog/brand/m-rc-noir/clothing/t-shirts/green/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/rokit/yellow/')) LocalRedirect('https://www.itkkit.com/catalog/brand/rokit/clothing/t-shirts/yellow/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/dr-le-de-monsieur/grey/')) LocalRedirect('https://www.itkkit.com/catalog/brand/dr-le-de-monsieur/clothing/pants/grey/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/daily-paper/orange/')) LocalRedirect('https://www.itkkit.com/catalog/brand/daily-paper/clothing/pants/orange/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shirts/brand/polythene-optics/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/polythene-optics/clothing/shirts/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/socks/brand/ripndip/yellow/')) LocalRedirect('https://www.itkkit.com/catalog/brand/ripndip/accessories/socks/yellow/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/olaf-hussein/green/')) LocalRedirect('https://www.itkkit.com/catalog/brand/olaf-hussein/clothing/longsleeves/green/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/pleasures/yellow/')) LocalRedirect('https://www.itkkit.com/catalog/brand/pleasures/clothing/outerwear/yellow/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/patta/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/patta/accessories/bags/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/daily-paper/blue/')) LocalRedirect('https://www.itkkit.com/catalog/brand/daily-paper/clothing/outerwear/blue/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/still-good/white/')) LocalRedirect('https://www.itkkit.com/catalog/brand/still-good/accessories/hats/white/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/knitwear/brand/fucking-awesome/purple/')) LocalRedirect('https://www.itkkit.com/catalog/brand/fucking-awesome/clothing/knitwear/purple/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/gasius/green/')) LocalRedirect('https://www.itkkit.com/catalog/brand/gasius/clothing/longsleeves/green/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/stussy/navy/')) LocalRedirect('https://www.itkkit.com/catalog/brand/stussy/clothing/hoodies/navy/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/patagonia/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/patagonia/clothing/longsleeves/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shirts/brand/h-las/navy/')) LocalRedirect('https://www.itkkit.com/catalog/brand/h-las/clothing/shirts/navy/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/polar-skate-co/tan/')) LocalRedirect('https://www.itkkit.com/catalog/brand/polar-skate-co/clothing/pants/tan/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/carne-bollente/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/carne-bollente/clothing/hoodies/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shirts/brand/assid/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/assid/clothing/shirts/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/daily-paper/green/')) LocalRedirect('https://www.itkkit.com/catalog/brand/daily-paper/clothing/outerwear/green/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/rassvet/blue/')) LocalRedirect('https://www.itkkit.com/catalog/brand/rassvet/clothing/outerwear/blue/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shirts/brand/nigel-cabourn/yellow/')) LocalRedirect('https://www.itkkit.com/catalog/brand/nigel-cabourn/clothing/shirts/yellow/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/fucking-awesome/red/')) LocalRedirect('https://www.itkkit.com/catalog/brand/fucking-awesome/clothing/longsleeves/red/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/sss-world-corp/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/sss-world-corp/clothing/longsleeves/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/pechatnye-izdaniya/brand/idea/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/idea/accessories/pechatnye-izdaniya/multi/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/rassvet/navy/')) LocalRedirect('https://www.itkkit.com/catalog/brand/rassvet/clothing/pants/navy/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/sneakers/brand/adidas-originals/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/adidas-originals/footwear/sneakers/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/idea/grey/')) LocalRedirect('https://www.itkkit.com/catalog/brand/idea/accessories/hats/grey/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/still-good/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/still-good/clothing/hoodies/multi/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/souvenirs/brand/primus/red/')) LocalRedirect('https://www.itkkit.com/catalog/brand/primus/accessories/souvenirs/red/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/skateboarding/brand/rassvet/pink/')) LocalRedirect('https://www.itkkit.com/catalog/brand/rassvet/accessories/skateboarding/pink/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/socks/brand/ripndip/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/ripndip/accessories/socks/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/reception/white/')) LocalRedirect('https://www.itkkit.com/catalog/brand/reception/clothing/longsleeves/white/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/nasaseasons/red/')) LocalRedirect('https://www.itkkit.com/catalog/brand/nasaseasons/accessories/hats/red/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/pleasures/blue/')) LocalRedirect('https://www.itkkit.com/catalog/brand/pleasures/clothing/t-shirts/blue/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/shoes/brand/clarks-originals/mint/')) LocalRedirect('https://www.itkkit.com/catalog/brand/clarks-originals/footwear/shoes/mint/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/good-morning-tapes/green/')) LocalRedirect('https://www.itkkit.com/catalog/brand/good-morning-tapes/clothing/t-shirts/green/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/carne-bollente/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/carne-bollente/clothing/longsleeves/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/patagonia/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/patagonia/accessories/hats/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/universal-works/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/universal-works/clothing/outerwear/multi/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/knitwear/brand/universal-works/navy/')) LocalRedirect('https://www.itkkit.com/catalog/brand/universal-works/clothing/knitwear/navy/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/the-north-face/green/')) LocalRedirect('https://www.itkkit.com/catalog/brand/the-north-face/clothing/outerwear/green/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shirts/brand/carne-bollente/grey/')) LocalRedirect('https://www.itkkit.com/catalog/brand/carne-bollente/clothing/shirts/grey/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/p-a-m/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/p-a-m/clothing/pants/multi/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/lack-of-guidance/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/lack-of-guidance/clothing/t-shirts/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/barbour/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/barbour/accessories/bags/multi/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/carrots/orange/')) LocalRedirect('https://www.itkkit.com/catalog/brand/carrots/clothing/outerwear/orange/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/rave-skateboards/yellow/')) LocalRedirect('https://www.itkkit.com/catalog/brand/rave-skateboards/accessories/hats/yellow/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/civilist/navy/')) LocalRedirect('https://www.itkkit.com/catalog/brand/civilist/clothing/t-shirts/navy/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/sss-world-corp/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/sss-world-corp/clothing/sweats/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/polythene-optics/green/')) LocalRedirect('https://www.itkkit.com/catalog/brand/polythene-optics/clothing/outerwear/green/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/socks/brand/ripndip/blue/')) LocalRedirect('https://www.itkkit.com/catalog/brand/ripndip/accessories/socks/blue/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/vests/brand/daily-paper/grey/')) LocalRedirect('https://www.itkkit.com/catalog/brand/daily-paper/clothing/vests/grey/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/engineered-garments/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/engineered-garments/accessories/bags/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/babylon-la/white/')) LocalRedirect('https://www.itkkit.com/catalog/brand/babylon-la/clothing/t-shirts/white/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/human-with-attitude/lavender/')) LocalRedirect('https://www.itkkit.com/catalog/brand/human-with-attitude/clothing/outerwear/lavender/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/know-wave/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/know-wave/clothing/hoodies/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/daily-paper/green/')) LocalRedirect('https://www.itkkit.com/catalog/brand/daily-paper/clothing/t-shirts/green/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/shoes/brand/clarks-originals/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/clarks-originals/footwear/shoes/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/socks/brand/ripndip/red/')) LocalRedirect('https://www.itkkit.com/catalog/brand/ripndip/accessories/socks/red/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/stussy/maroon/')) LocalRedirect('https://www.itkkit.com/catalog/brand/stussy/clothing/sweats/maroon/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/sandals/brand/birkenstock/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/birkenstock/footwear/sandals/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/human-with-attitude/tie%20dye/')) LocalRedirect('https://www.itkkit.com/catalog/brand/human-with-attitude/clothing/hoodies/tie%20dye/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/fucking-awesome/green/')) LocalRedirect('https://www.itkkit.com/catalog/brand/fucking-awesome/clothing/t-shirts/green/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/souvenirs/brand/polythene-optics/yellow/')) LocalRedirect('https://www.itkkit.com/catalog/brand/polythene-optics/accessories/souvenirs/yellow/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/mki-miyuki-zoku/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/mki-miyuki-zoku/clothing/pants/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/m-rc-noir/grey/')) LocalRedirect('https://www.itkkit.com/catalog/brand/m-rc-noir/accessories/hats/grey/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/rassvet/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/rassvet/accessories/hats/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/nasaseasons/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/nasaseasons/accessories/hats/multi/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/pop-trading-company/charcoal/')) LocalRedirect('https://www.itkkit.com/catalog/brand/pop-trading-company/clothing/sweats/charcoal/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/h-las/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/h-las/clothing/hoodies/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/still-good/blue/')) LocalRedirect('https://www.itkkit.com/catalog/brand/still-good/clothing/longsleeves/blue/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/socks/brand/civilist/white/')) LocalRedirect('https://www.itkkit.com/catalog/brand/civilist/accessories/socks/white/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/platformx/white/')) LocalRedirect('https://www.itkkit.com/catalog/brand/platformx/clothing/t-shirts/white/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/pleasures/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/pleasures/clothing/pants/multi/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/casablanca/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/casablanca/clothing/sweats/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/pop-trading-company/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/pop-trading-company/clothing/outerwear/multi/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/souvenirs/brand/primus/grey/')) LocalRedirect('https://www.itkkit.com/catalog/brand/primus/accessories/souvenirs/grey/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shirts/brand/tommy-jeans/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/tommy-jeans/clothing/shirts/multi/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/souvenirs/brand/carhartt-wip/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/carhartt-wip/accessories/souvenirs/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/sss-world-corp/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/sss-world-corp/clothing/hoodies/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/arte-antwerp/white/')) LocalRedirect('https://www.itkkit.com/catalog/brand/arte-antwerp/clothing/t-shirts/white/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/alpha-industries/blue/')) LocalRedirect('https://www.itkkit.com/catalog/brand/alpha-industries/clothing/outerwear/blue/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/tommy-jeans/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/tommy-jeans/clothing/outerwear/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/taikan/yellow/')) LocalRedirect('https://www.itkkit.com/catalog/brand/taikan/accessories/bags/yellow/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/civilist/red/')) LocalRedirect('https://www.itkkit.com/catalog/brand/civilist/accessories/hats/red/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/polythene-optics/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/polythene-optics/clothing/pants/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/vests/brand/daily-paper/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/daily-paper/clothing/vests/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/shoes/brand/suicoke/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/suicoke/footwear/shoes/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/stussy/brown/')) LocalRedirect('https://www.itkkit.com/catalog/brand/stussy/clothing/pants/brown/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/barbour/olive/')) LocalRedirect('https://www.itkkit.com/catalog/brand/barbour/accessories/bags/olive/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/m-rc-noir/silver/')) LocalRedirect('https://www.itkkit.com/catalog/brand/m-rc-noir/accessories/bags/silver/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/sneakers/brand/hoka-one-one/white/')) LocalRedirect('https://www.itkkit.com/catalog/brand/hoka-one-one/footwear/sneakers/white/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/sss-world-corp/white/')) LocalRedirect('https://www.itkkit.com/catalog/brand/sss-world-corp/clothing/t-shirts/white/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/human-with-attitude/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/human-with-attitude/clothing/hoodies/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/souvenirs/brand/carhartt-wip/red/')) LocalRedirect('https://www.itkkit.com/catalog/brand/carhartt-wip/accessories/souvenirs/red/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/pechatnye-izdaniya/brand/idea/red/')) LocalRedirect('https://www.itkkit.com/catalog/brand/idea/accessories/pechatnye-izdaniya/red/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/good-morning-tapes/white/')) LocalRedirect('https://www.itkkit.com/catalog/brand/good-morning-tapes/clothing/t-shirts/white/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/olaf-hussein/navy/')) LocalRedirect('https://www.itkkit.com/catalog/brand/olaf-hussein/clothing/hoodies/navy/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/stussy/camo/')) LocalRedirect('https://www.itkkit.com/catalog/brand/stussy/clothing/pants/camo/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/the-north-face/purple/')) LocalRedirect('https://www.itkkit.com/catalog/brand/the-north-face/clothing/outerwear/purple/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/pop-trading-company/grey/')) LocalRedirect('https://www.itkkit.com/catalog/brand/pop-trading-company/clothing/sweats/grey/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/polar-skate-co/navy/')) LocalRedirect('https://www.itkkit.com/catalog/brand/polar-skate-co/clothing/pants/navy/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/vests/brand/universal-works/navy/')) LocalRedirect('https://www.itkkit.com/catalog/brand/universal-works/clothing/vests/navy/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/arte-antwerp/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/arte-antwerp/clothing/t-shirts/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/civilist/yellow/')) LocalRedirect('https://www.itkkit.com/catalog/brand/civilist/clothing/longsleeves/yellow/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/fucking-awesome/yellow/')) LocalRedirect('https://www.itkkit.com/catalog/brand/fucking-awesome/clothing/hoodies/yellow/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/universal-works/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/universal-works/clothing/outerwear/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/vests/brand/mki-miyuki-zoku/pink/')) LocalRedirect('https://www.itkkit.com/catalog/brand/mki-miyuki-zoku/clothing/vests/pink/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/stussy/white/')) LocalRedirect('https://www.itkkit.com/catalog/brand/stussy/clothing/t-shirts/white/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/carrots/orange/')) LocalRedirect('https://www.itkkit.com/catalog/brand/carrots/clothing/hoodies/orange/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/souvenirs/brand/fucking-awesome/red/')) LocalRedirect('https://www.itkkit.com/catalog/brand/fucking-awesome/accessories/souvenirs/red/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/sss-world-corp/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/sss-world-corp/clothing/hoodies/multi/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/call-me-917/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/call-me-917/clothing/hoodies/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/daily-paper/green/')) LocalRedirect('https://www.itkkit.com/catalog/brand/daily-paper/clothing/sweats/green/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shirts/brand/pleasures/purple/')) LocalRedirect('https://www.itkkit.com/catalog/brand/pleasures/clothing/shirts/purple/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/liam-hodges/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/liam-hodges/clothing/hoodies/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/bronze-56k/teal/')) LocalRedirect('https://www.itkkit.com/catalog/brand/bronze-56k/clothing/pants/teal/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/fragrances/brand/comme-des-garcons-parfum/green/')) LocalRedirect('https://www.itkkit.com/catalog/brand/comme-des-garcons-parfum/accessories/fragrances/green/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/platformx/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/platformx/clothing/sweats/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/mki-miyuki-zoku/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/mki-miyuki-zoku/clothing/longsleeves/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/carne-bollente/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/carne-bollente/clothing/t-shirts/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/fjallraven/blue/')) LocalRedirect('https://www.itkkit.com/catalog/brand/fjallraven/accessories/bags/blue/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/champion-reverse-weave/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/champion-reverse-weave/clothing/hoodies/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/belts/brand/comme-des-gar-ons-wallets/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/comme-des-gar-ons-wallets/accessories/belts/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/pleasures/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/pleasures/clothing/longsleeves/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shorts/brand/daily-paper/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/daily-paper/clothing/shorts/multi/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/souvenirs/brand/rokit/orange/')) LocalRedirect('https://www.itkkit.com/catalog/brand/rokit/accessories/souvenirs/orange/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/souvenirs/brand/maharishi/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/maharishi/accessories/souvenirs/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/needles/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/needles/clothing/pants/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/daily-paper/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/daily-paper/clothing/pants/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/sneakers/brand/fila-woman/white/')) LocalRedirect('https://www.itkkit.com/catalog/brand/fila-woman/footwear/sneakers/white/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/comme-des-gar-ons-play/white/')) LocalRedirect('https://www.itkkit.com/catalog/brand/comme-des-gar-ons-play/clothing/t-shirts/white/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/vests/brand/patagonia/grey/')) LocalRedirect('https://www.itkkit.com/catalog/brand/patagonia/clothing/vests/grey/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/sneakers/brand/tommy-jeans-woman/white/')) LocalRedirect('https://www.itkkit.com/catalog/brand/tommy-jeans-woman/footwear/sneakers/white/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/shoes/brand/diemme/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/diemme/footwear/shoes/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/sneakers/brand/hoka-one-one/brown/')) LocalRedirect('https://www.itkkit.com/catalog/brand/hoka-one-one/footwear/sneakers/brown/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/daily-paper/orange/')) LocalRedirect('https://www.itkkit.com/catalog/brand/daily-paper/clothing/outerwear/orange/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/sneakers/brand/fila-woman/gold/')) LocalRedirect('https://www.itkkit.com/catalog/brand/fila-woman/footwear/sneakers/gold/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/olaf-hussein/navy/')) LocalRedirect('https://www.itkkit.com/catalog/brand/olaf-hussein/clothing/longsleeves/navy/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/still-good/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/still-good/clothing/hoodies/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/human-with-attitude/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/human-with-attitude/clothing/hoodies/multi/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/fucking-awesome/navy/')) LocalRedirect('https://www.itkkit.com/catalog/brand/fucking-awesome/clothing/longsleeves/navy/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/socks/brand/ripndip/green/')) LocalRedirect('https://www.itkkit.com/catalog/brand/ripndip/accessories/socks/green/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/rokit/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/rokit/clothing/t-shirts/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/grind-london/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/grind-london/clothing/t-shirts/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/alltimers/purple/')) LocalRedirect('https://www.itkkit.com/catalog/brand/alltimers/clothing/pants/purple/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/bronze-56k/white/')) LocalRedirect('https://www.itkkit.com/catalog/brand/bronze-56k/clothing/t-shirts/white/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/stussy/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/stussy/clothing/outerwear/multi/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/wallets/brand/fjallraven/yellow/')) LocalRedirect('https://www.itkkit.com/catalog/brand/fjallraven/accessories/wallets/yellow/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/sneakers/brand/clarks-originals/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/clarks-originals/footwear/sneakers/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/ripndip/white/')) LocalRedirect('https://www.itkkit.com/catalog/brand/ripndip/clothing/outerwear/white/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/sergio-tacchini/navy/')) LocalRedirect('https://www.itkkit.com/catalog/brand/sergio-tacchini/clothing/pants/navy/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shirts/brand/comme-des-gar-ons-shirt/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/comme-des-gar-ons-shirt/clothing/shirts/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/polar-skate-co/blue/')) LocalRedirect('https://www.itkkit.com/catalog/brand/polar-skate-co/clothing/pants/blue/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/aries/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/aries/clothing/longsleeves/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/rassvet/yellow/')) LocalRedirect('https://www.itkkit.com/catalog/brand/rassvet/accessories/hats/yellow/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/lack-of-guidance/grey/')) LocalRedirect('https://www.itkkit.com/catalog/brand/lack-of-guidance/clothing/t-shirts/grey/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/sneakers/brand/comme-des-gar-ons-play/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/comme-des-gar-ons-play/footwear/sneakers/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/universal-works/navy/')) LocalRedirect('https://www.itkkit.com/catalog/brand/universal-works/clothing/sweats/navy/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/sneakers/brand/new-balance/navy/')) LocalRedirect('https://www.itkkit.com/catalog/brand/new-balance/footwear/sneakers/navy/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/bombers/brand/resort-corps/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/resort-corps/clothing/bombers/multi/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/m-rc-noir/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/m-rc-noir/clothing/pants/multi/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/aries/pink/')) LocalRedirect('https://www.itkkit.com/catalog/brand/aries/clothing/t-shirts/pink/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/wallets/brand/fjallraven/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/fjallraven/accessories/wallets/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/socks/brand/polythene-optics/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/polythene-optics/accessories/socks/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shorts/brand/stussy/orange/')) LocalRedirect('https://www.itkkit.com/catalog/brand/stussy/clothing/shorts/orange/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/resort-corps/green/')) LocalRedirect('https://www.itkkit.com/catalog/brand/resort-corps/clothing/pants/green/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/billionaire-boys-club/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/billionaire-boys-club/clothing/t-shirts/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/wallets/brand/civilist/yellow/')) LocalRedirect('https://www.itkkit.com/catalog/brand/civilist/accessories/wallets/yellow/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/liam-hodges/white/')) LocalRedirect('https://www.itkkit.com/catalog/brand/liam-hodges/clothing/t-shirts/white/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/chinatown-market/yellow/')) LocalRedirect('https://www.itkkit.com/catalog/brand/chinatown-market/accessories/bags/yellow/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/comme-des-gar-ons-play/white/')) LocalRedirect('https://www.itkkit.com/catalog/brand/comme-des-gar-ons-play/clothing/longsleeves/white/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shirts/brand/resort-corps/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/resort-corps/clothing/shirts/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/fucking-awesome/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/fucking-awesome/clothing/t-shirts/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shirts/brand/rassvet/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/rassvet/clothing/shirts/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/stussy/navy/')) LocalRedirect('https://www.itkkit.com/catalog/brand/stussy/clothing/outerwear/navy/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/platformx/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/platformx/clothing/hoodies/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/engineered-garments/red/')) LocalRedirect('https://www.itkkit.com/catalog/brand/engineered-garments/accessories/bags/red/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/civilist/tie%20dye/')) LocalRedirect('https://www.itkkit.com/catalog/brand/civilist/clothing/t-shirts/tie%20dye/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/knitwear/brand/sss-world-corp/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/sss-world-corp/clothing/knitwear/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/still-good/grey/')) LocalRedirect('https://www.itkkit.com/catalog/brand/still-good/clothing/hoodies/grey/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/alpha-industries/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/alpha-industries/clothing/hoodies/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/engineered-garments/grey/')) LocalRedirect('https://www.itkkit.com/catalog/brand/engineered-garments/clothing/pants/grey/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shirts/brand/h-las/blue/')) LocalRedirect('https://www.itkkit.com/catalog/brand/h-las/clothing/shirts/blue/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/souvenirs/brand/civilist/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/civilist/accessories/souvenirs/multi/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/grooming/brand/marvis/white/')) LocalRedirect('https://www.itkkit.com/catalog/brand/marvis/accessories/grooming/white/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/billionaire-boys-club/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/billionaire-boys-club/clothing/sweats/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/carne-bollente/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/carne-bollente/clothing/t-shirts/multi/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/daily-paper/navy/')) LocalRedirect('https://www.itkkit.com/catalog/brand/daily-paper/clothing/outerwear/navy/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/babylon-la/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/babylon-la/clothing/hoodies/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shirts/brand/comme-des-gar-ons-shirt/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/comme-des-gar-ons-shirt/clothing/shirts/multi/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/souvenirs/brand/h-las/white/')) LocalRedirect('https://www.itkkit.com/catalog/brand/h-las/accessories/souvenirs/white/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/pleasures/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/pleasures/clothing/outerwear/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/tommy-jeans-woman/silver/')) LocalRedirect('https://www.itkkit.com/catalog/brand/tommy-jeans-woman/accessories/bags/silver/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/barbour/brown/')) LocalRedirect('https://www.itkkit.com/catalog/brand/barbour/clothing/outerwear/brown/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/rave-skateboards/olive/')) LocalRedirect('https://www.itkkit.com/catalog/brand/rave-skateboards/accessories/bags/olive/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/pop-trading-company/navy/')) LocalRedirect('https://www.itkkit.com/catalog/brand/pop-trading-company/accessories/hats/navy/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/good-morning-tapes/white/')) LocalRedirect('https://www.itkkit.com/catalog/brand/good-morning-tapes/clothing/longsleeves/white/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/civilist/plum/')) LocalRedirect('https://www.itkkit.com/catalog/brand/civilist/clothing/hoodies/plum/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/kappa/yellow/')) LocalRedirect('https://www.itkkit.com/catalog/brand/kappa/clothing/pants/yellow/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shirts/brand/casablanca/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/casablanca/clothing/shirts/multi/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/souvenirs/brand/fjallraven/grey/')) LocalRedirect('https://www.itkkit.com/catalog/brand/fjallraven/accessories/souvenirs/grey/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/skateboarding/brand/palace-skateboards/pink/')) LocalRedirect('https://www.itkkit.com/catalog/brand/palace-skateboards/accessories/skateboarding/pink/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/wallets/brand/comme-des-gar-ons-wallets/navy/')) LocalRedirect('https://www.itkkit.com/catalog/brand/comme-des-gar-ons-wallets/accessories/wallets/navy/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/fucking-awesome/royal/')) LocalRedirect('https://www.itkkit.com/catalog/brand/fucking-awesome/clothing/hoodies/royal/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/pop-trading-company/navy/')) LocalRedirect('https://www.itkkit.com/catalog/brand/pop-trading-company/clothing/pants/navy/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/champion-reverse-weave/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/champion-reverse-weave/clothing/outerwear/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/h-las/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/h-las/clothing/longsleeves/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/still-good/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/still-good/clothing/outerwear/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/reception/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/reception/clothing/longsleeves/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/m-rc-noir/green/')) LocalRedirect('https://www.itkkit.com/catalog/brand/m-rc-noir/accessories/bags/green/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/daily-paper/brown/')) LocalRedirect('https://www.itkkit.com/catalog/brand/daily-paper/clothing/outerwear/brown/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/stussy/navy/')) LocalRedirect('https://www.itkkit.com/catalog/brand/stussy/accessories/hats/navy/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/daily-paper/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/daily-paper/clothing/t-shirts/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/resort-corps/camo/')) LocalRedirect('https://www.itkkit.com/catalog/brand/resort-corps/clothing/t-shirts/camo/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/pleasures/green/')) LocalRedirect('https://www.itkkit.com/catalog/brand/pleasures/clothing/pants/green/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/fucking-awesome/white/')) LocalRedirect('https://www.itkkit.com/catalog/brand/fucking-awesome/clothing/longsleeves/white/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/stussy/olive/')) LocalRedirect('https://www.itkkit.com/catalog/brand/stussy/clothing/longsleeves/olive/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/maharishi/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/maharishi/accessories/bags/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/arte-antwerp/red/')) LocalRedirect('https://www.itkkit.com/catalog/brand/arte-antwerp/clothing/longsleeves/red/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/jeans/brand/polar-skate-co/blue/')) LocalRedirect('https://www.itkkit.com/catalog/brand/polar-skate-co/clothing/jeans/blue/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/babylon-la/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/babylon-la/clothing/t-shirts/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/fucking-awesome/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/fucking-awesome/clothing/t-shirts/multi/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/stussy/blue/')) LocalRedirect('https://www.itkkit.com/catalog/brand/stussy/clothing/outerwear/blue/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/ripndip/navy/')) LocalRedirect('https://www.itkkit.com/catalog/brand/ripndip/clothing/hoodies/navy/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/tommy-jeans/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/tommy-jeans/clothing/hoodies/multi/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/daily-paper/blue/')) LocalRedirect('https://www.itkkit.com/catalog/brand/daily-paper/clothing/pants/blue/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/fjallraven/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/fjallraven/accessories/bags/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/polythene-optics/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/polythene-optics/clothing/hoodies/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/alpha-industries/orange/')) LocalRedirect('https://www.itkkit.com/catalog/brand/alpha-industries/clothing/outerwear/orange/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/pleasures/violet/')) LocalRedirect('https://www.itkkit.com/catalog/brand/pleasures/clothing/pants/violet/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/rassvet/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/rassvet/clothing/sweats/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/daily-paper/yellow/')) LocalRedirect('https://www.itkkit.com/catalog/brand/daily-paper/accessories/hats/yellow/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/gasius/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/gasius/clothing/longsleeves/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/shoes/brand/mephisto/brown/')) LocalRedirect('https://www.itkkit.com/catalog/brand/mephisto/footwear/shoes/brown/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/l-i-e-s-records/white/')) LocalRedirect('https://www.itkkit.com/catalog/brand/l-i-e-s-records/clothing/t-shirts/white/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/pleasures/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/pleasures/accessories/bags/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/platformx/orange/')) LocalRedirect('https://www.itkkit.com/catalog/brand/platformx/clothing/sweats/orange/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/sandals/brand/birkenstock/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/birkenstock/footwear/sandals/multi/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/m-rc-noir/grey/')) LocalRedirect('https://www.itkkit.com/catalog/brand/m-rc-noir/clothing/outerwear/grey/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/souvenirs/brand/polythene-optics/red/')) LocalRedirect('https://www.itkkit.com/catalog/brand/polythene-optics/accessories/souvenirs/red/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/socks/brand/still-good/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/still-good/accessories/socks/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/needles/tie%20dye/')) LocalRedirect('https://www.itkkit.com/catalog/brand/needles/clothing/hoodies/tie%20dye/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/sss-world-corp/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/sss-world-corp/clothing/outerwear/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/grooming/brand/marvis/mint/')) LocalRedirect('https://www.itkkit.com/catalog/brand/marvis/accessories/grooming/mint/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/vests/brand/pop-trading-company/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/pop-trading-company/clothing/vests/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/patta/white/')) LocalRedirect('https://www.itkkit.com/catalog/brand/patta/clothing/t-shirts/white/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/junior-executive/pink/')) LocalRedirect('https://www.itkkit.com/catalog/brand/junior-executive/clothing/sweats/pink/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/arte-antwerp/navy/')) LocalRedirect('https://www.itkkit.com/catalog/brand/arte-antwerp/clothing/hoodies/navy/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/sneakers/brand/fila/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/fila/footwear/sneakers/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/bombers/brand/p-a-m/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/p-a-m/clothing/bombers/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/aries/red/')) LocalRedirect('https://www.itkkit.com/catalog/brand/aries/clothing/sweats/red/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/nigel-cabourn/blue/')) LocalRedirect('https://www.itkkit.com/catalog/brand/nigel-cabourn/clothing/outerwear/blue/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/human-with-attitude/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/human-with-attitude/clothing/pants/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/stussy/navy/')) LocalRedirect('https://www.itkkit.com/catalog/brand/stussy/clothing/longsleeves/navy/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/tommy-jeans-woman/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/tommy-jeans-woman/accessories/bags/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/grind-london/white/')) LocalRedirect('https://www.itkkit.com/catalog/brand/grind-london/clothing/hoodies/white/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/assid/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/assid/clothing/hoodies/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/babylon-la/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/babylon-la/accessories/hats/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/stussy/moss/')) LocalRedirect('https://www.itkkit.com/catalog/brand/stussy/clothing/pants/moss/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/patta/violet/')) LocalRedirect('https://www.itkkit.com/catalog/brand/patta/clothing/longsleeves/violet/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/barbour/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/barbour/clothing/outerwear/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/nasaseasons/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/nasaseasons/clothing/t-shirts/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/carhartt-wip/white/')) LocalRedirect('https://www.itkkit.com/catalog/brand/carhartt-wip/clothing/t-shirts/white/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/kappa/yellow/')) LocalRedirect('https://www.itkkit.com/catalog/brand/kappa/clothing/sweats/yellow/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/ripndip/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/ripndip/clothing/longsleeves/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/m-rc-noir/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/m-rc-noir/accessories/hats/multi/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/rassvet/grey/')) LocalRedirect('https://www.itkkit.com/catalog/brand/rassvet/clothing/sweats/grey/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/carrots/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/carrots/clothing/outerwear/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/daily-paper/tie%20dye/')) LocalRedirect('https://www.itkkit.com/catalog/brand/daily-paper/accessories/bags/tie%20dye/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shirts/brand/assid/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/assid/clothing/shirts/multi/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/comme-des-gar-ons-play/grey/')) LocalRedirect('https://www.itkkit.com/catalog/brand/comme-des-gar-ons-play/clothing/hoodies/grey/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/wallets/brand/fjallraven/red/')) LocalRedirect('https://www.itkkit.com/catalog/brand/fjallraven/accessories/wallets/red/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/gasius/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/gasius/clothing/t-shirts/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/taikan/white/')) LocalRedirect('https://www.itkkit.com/catalog/brand/taikan/accessories/bags/white/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/ripndip/white/')) LocalRedirect('https://www.itkkit.com/catalog/brand/ripndip/clothing/t-shirts/white/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/sneakers/brand/premiata/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/premiata/footwear/sneakers/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/rassvet/red/')) LocalRedirect('https://www.itkkit.com/catalog/brand/rassvet/accessories/hats/red/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/souvenirs/brand/chinatown-market/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/chinatown-market/accessories/souvenirs/multi/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/socks/brand/ripndip/pink/')) LocalRedirect('https://www.itkkit.com/catalog/brand/ripndip/accessories/socks/pink/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/kappa/green/')) LocalRedirect('https://www.itkkit.com/catalog/brand/kappa/clothing/pants/green/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shirts/brand/daily-paper/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/daily-paper/clothing/shirts/multi/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/tommy-jeans/grey/')) LocalRedirect('https://www.itkkit.com/catalog/brand/tommy-jeans/clothing/hoodies/grey/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/belts/brand/daily-paper/green/')) LocalRedirect('https://www.itkkit.com/catalog/brand/daily-paper/accessories/belts/green/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/junior-executive/white/')) LocalRedirect('https://www.itkkit.com/catalog/brand/junior-executive/clothing/t-shirts/white/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/wallets/brand/fjallraven/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/fjallraven/accessories/wallets/multi/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shirts/brand/stussy/pink/')) LocalRedirect('https://www.itkkit.com/catalog/brand/stussy/clothing/shirts/pink/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/barbour/olive/')) LocalRedirect('https://www.itkkit.com/catalog/brand/barbour/clothing/outerwear/olive/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/fucking-awesome/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/fucking-awesome/clothing/sweats/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shirts/brand/pleasures/red/')) LocalRedirect('https://www.itkkit.com/catalog/brand/pleasures/clothing/shirts/red/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shirts/brand/comme-des-gar-ons-play/white/')) LocalRedirect('https://www.itkkit.com/catalog/brand/comme-des-gar-ons-play/clothing/shirts/white/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/m-rc-noir/grey/')) LocalRedirect('https://www.itkkit.com/catalog/brand/m-rc-noir/clothing/pants/grey/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/p-a-m/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/p-a-m/clothing/longsleeves/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/grind-london/grey/')) LocalRedirect('https://www.itkkit.com/catalog/brand/grind-london/clothing/hoodies/grey/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/human-with-attitude/reflective/')) LocalRedirect('https://www.itkkit.com/catalog/brand/human-with-attitude/clothing/pants/reflective/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/alpha-industries/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/alpha-industries/clothing/sweats/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/comme-des-gar-ons-play/navy/')) LocalRedirect('https://www.itkkit.com/catalog/brand/comme-des-gar-ons-play/clothing/longsleeves/navy/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/polar-skate-co/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/polar-skate-co/clothing/hoodies/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/gloves/brand/the-north-face/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/the-north-face/accessories/gloves/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/champion-reverse-weave/grey/')) LocalRedirect('https://www.itkkit.com/catalog/brand/champion-reverse-weave/accessories/hats/grey/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/rave-skateboards/orange/')) LocalRedirect('https://www.itkkit.com/catalog/brand/rave-skateboards/accessories/hats/orange/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shirts/brand/pleasures/white/')) LocalRedirect('https://www.itkkit.com/catalog/brand/pleasures/clothing/shirts/white/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/carne-bollente/beige/')) LocalRedirect('https://www.itkkit.com/catalog/brand/carne-bollente/clothing/hoodies/beige/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/reception/grey/')) LocalRedirect('https://www.itkkit.com/catalog/brand/reception/clothing/longsleeves/grey/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/socks/brand/ripndip/white/')) LocalRedirect('https://www.itkkit.com/catalog/brand/ripndip/accessories/socks/white/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/ripndip/tie%20dye/')) LocalRedirect('https://www.itkkit.com/catalog/brand/ripndip/clothing/longsleeves/tie%20dye/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/olaf-hussein/red/')) LocalRedirect('https://www.itkkit.com/catalog/brand/olaf-hussein/clothing/t-shirts/red/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/bronze-56k/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/bronze-56k/clothing/sweats/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/ripndip/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/ripndip/clothing/outerwear/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/call-me-917/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/call-me-917/clothing/pants/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/knitwear/brand/pleasures/brown/')) LocalRedirect('https://www.itkkit.com/catalog/brand/pleasures/clothing/knitwear/brown/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/rassvet/grey/')) LocalRedirect('https://www.itkkit.com/catalog/brand/rassvet/clothing/pants/grey/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/alltimers/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/alltimers/clothing/t-shirts/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/babylon-la/pink/')) LocalRedirect('https://www.itkkit.com/catalog/brand/babylon-la/clothing/longsleeves/pink/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/m-rc-noir/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/m-rc-noir/clothing/outerwear/multi/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/skateboarding/brand/fucking-awesome/blue/')) LocalRedirect('https://www.itkkit.com/catalog/brand/fucking-awesome/accessories/skateboarding/blue/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/daily-paper/green/')) LocalRedirect('https://www.itkkit.com/catalog/brand/daily-paper/clothing/hoodies/green/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/pleasures/orange/')) LocalRedirect('https://www.itkkit.com/catalog/brand/pleasures/clothing/longsleeves/orange/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/maharishi/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/maharishi/clothing/longsleeves/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/comme-des-gar-ons-play/red/')) LocalRedirect('https://www.itkkit.com/catalog/brand/comme-des-gar-ons-play/clothing/longsleeves/red/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/sneakers/brand/rassvet/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/rassvet/footwear/sneakers/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/patagonia/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/patagonia/clothing/hoodies/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/vests/brand/mki-miyuki-zoku/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/mki-miyuki-zoku/clothing/vests/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/knitwear/brand/polar-skate-co/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/polar-skate-co/clothing/knitwear/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/fucking-awesome/red/')) LocalRedirect('https://www.itkkit.com/catalog/brand/fucking-awesome/clothing/outerwear/red/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/sss-world-corp/yellow/')) LocalRedirect('https://www.itkkit.com/catalog/brand/sss-world-corp/clothing/t-shirts/yellow/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/rokit/khaki/')) LocalRedirect('https://www.itkkit.com/catalog/brand/rokit/clothing/pants/khaki/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/souvenirs/brand/stussy/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/stussy/accessories/souvenirs/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/fucking-awesome/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/fucking-awesome/accessories/hats/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/alpha-industries/silver/')) LocalRedirect('https://www.itkkit.com/catalog/brand/alpha-industries/clothing/outerwear/silver/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/fjallraven/red/')) LocalRedirect('https://www.itkkit.com/catalog/brand/fjallraven/accessories/bags/red/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/chinatown-market/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/chinatown-market/clothing/longsleeves/multi/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/human-with-attitude/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/human-with-attitude/clothing/longsleeves/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/knitwear/brand/chinatown-market/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/chinatown-market/clothing/knitwear/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/sneakers/brand/rassvet/green/')) LocalRedirect('https://www.itkkit.com/catalog/brand/rassvet/footwear/sneakers/green/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/pleasures/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/pleasures/accessories/hats/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/sneakers/brand/tommy-jeans/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/tommy-jeans/footwear/sneakers/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/casablanca/green/')) LocalRedirect('https://www.itkkit.com/catalog/brand/casablanca/clothing/hoodies/green/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/pop-trading-company/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/pop-trading-company/clothing/t-shirts/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/dr-le-de-monsieur/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/dr-le-de-monsieur/clothing/outerwear/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/human-with-attitude/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/human-with-attitude/accessories/bags/multi/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/billionaire-boys-club/navy/')) LocalRedirect('https://www.itkkit.com/catalog/brand/billionaire-boys-club/clothing/longsleeves/navy/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/billionaire-boys-club/white/')) LocalRedirect('https://www.itkkit.com/catalog/brand/billionaire-boys-club/clothing/t-shirts/white/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/the-north-face/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/the-north-face/clothing/outerwear/multi/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/carrots/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/carrots/accessories/hats/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/comme-des-gar-ons-play/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/comme-des-gar-ons-play/clothing/t-shirts/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/vests/brand/daily-paper/blue/')) LocalRedirect('https://www.itkkit.com/catalog/brand/daily-paper/clothing/vests/blue/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/know-wave/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/know-wave/clothing/sweats/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/comme-des-gar-ons-wallets/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/comme-des-gar-ons-wallets/accessories/bags/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/h-las/purple/')) LocalRedirect('https://www.itkkit.com/catalog/brand/h-las/accessories/hats/purple/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/champion-reverse-weave/yellow/')) LocalRedirect('https://www.itkkit.com/catalog/brand/champion-reverse-weave/accessories/hats/yellow/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/daily-paper/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/daily-paper/clothing/longsleeves/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/daily-paper/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/daily-paper/clothing/t-shirts/multi/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/fucking-awesome/yellow/')) LocalRedirect('https://www.itkkit.com/catalog/brand/fucking-awesome/clothing/t-shirts/yellow/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/the-north-face/purple/')) LocalRedirect('https://www.itkkit.com/catalog/brand/the-north-face/accessories/bags/purple/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/sandals/brand/suicoke/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/suicoke/footwear/sandals/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shirts/brand/comme-des-gar-ons-shirt/navy/')) LocalRedirect('https://www.itkkit.com/catalog/brand/comme-des-gar-ons-shirt/clothing/shirts/navy/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/p-a-m/white/')) LocalRedirect('https://www.itkkit.com/catalog/brand/p-a-m/accessories/hats/white/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/belts/brand/stussy/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/stussy/accessories/belts/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/liam-hodges/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/liam-hodges/clothing/t-shirts/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/stussy/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/stussy/clothing/hoodies/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/skateboarding/brand/rassvet/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/rassvet/accessories/skateboarding/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/idea/grey/')) LocalRedirect('https://www.itkkit.com/catalog/brand/idea/accessories/bags/grey/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/stussy/white/')) LocalRedirect('https://www.itkkit.com/catalog/brand/stussy/accessories/bags/white/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/socks/brand/champion-reverse-weave/red/')) LocalRedirect('https://www.itkkit.com/catalog/brand/champion-reverse-weave/accessories/socks/red/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/polythene-optics/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/polythene-optics/clothing/sweats/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/good-morning-tapes/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/good-morning-tapes/clothing/t-shirts/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/nasaseasons/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/nasaseasons/accessories/hats/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/patta/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/patta/clothing/t-shirts/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/civilist/plum/')) LocalRedirect('https://www.itkkit.com/catalog/brand/civilist/clothing/t-shirts/plum/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/wallets/brand/carne-bollente/silver/')) LocalRedirect('https://www.itkkit.com/catalog/brand/carne-bollente/accessories/wallets/silver/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/souvenirs/brand/polythene-optics/white/')) LocalRedirect('https://www.itkkit.com/catalog/brand/polythene-optics/accessories/souvenirs/white/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/arte-antwerp/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/arte-antwerp/clothing/pants/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/knitwear/brand/fucking-awesome/teal/')) LocalRedirect('https://www.itkkit.com/catalog/brand/fucking-awesome/clothing/knitwear/teal/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/daily-paper/blue/')) LocalRedirect('https://www.itkkit.com/catalog/brand/daily-paper/clothing/t-shirts/blue/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/aries/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/aries/clothing/pants/multi/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/champion-reverse-weave/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/champion-reverse-weave/clothing/t-shirts/multi/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/ripndip/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/ripndip/clothing/pants/multi/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/tommy-jeans/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/tommy-jeans/clothing/sweats/multi/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/parkas/brand/carhartt-wip/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/carhartt-wip/clothing/parkas/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/rassvet/yellow/')) LocalRedirect('https://www.itkkit.com/catalog/brand/rassvet/clothing/sweats/yellow/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/polythene-optics/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/polythene-optics/clothing/outerwear/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/patagonia/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/patagonia/clothing/outerwear/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/tommy-jeans/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/tommy-jeans/clothing/t-shirts/multi/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/ripndip/blue/')) LocalRedirect('https://www.itkkit.com/catalog/brand/ripndip/clothing/hoodies/blue/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/human-with-attitude/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/human-with-attitude/clothing/t-shirts/multi/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/pop-trading-company/navy/')) LocalRedirect('https://www.itkkit.com/catalog/brand/pop-trading-company/clothing/outerwear/navy/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/polythene-optics/yellow/')) LocalRedirect('https://www.itkkit.com/catalog/brand/polythene-optics/clothing/hoodies/yellow/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/bronze-56k/green/')) LocalRedirect('https://www.itkkit.com/catalog/brand/bronze-56k/clothing/t-shirts/green/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shorts/brand/m-rc-noir/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/m-rc-noir/clothing/shorts/multi/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/fucking-awesome/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/fucking-awesome/clothing/sweats/multi/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/vests/brand/engineered-garments/red/')) LocalRedirect('https://www.itkkit.com/catalog/brand/engineered-garments/clothing/vests/red/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/resort-corps/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/resort-corps/clothing/t-shirts/multi/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/socks/brand/rassvet/white/')) LocalRedirect('https://www.itkkit.com/catalog/brand/rassvet/accessories/socks/white/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/being-hunted/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/being-hunted/clothing/t-shirts/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/maharishi/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/maharishi/clothing/pants/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/human-with-attitude/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/human-with-attitude/clothing/outerwear/multi/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/polar-skate-co/white/')) LocalRedirect('https://www.itkkit.com/catalog/brand/polar-skate-co/clothing/longsleeves/white/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/comme-des-gar-ons-play/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/comme-des-gar-ons-play/clothing/hoodies/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shorts/brand/chinatown-market/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/chinatown-market/clothing/shorts/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shirts/brand/life-s-a-beach/blue/')) LocalRedirect('https://www.itkkit.com/catalog/brand/life-s-a-beach/clothing/shirts/blue/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/pop-trading-company/grey/')) LocalRedirect('https://www.itkkit.com/catalog/brand/pop-trading-company/clothing/t-shirts/grey/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/rave-skateboards/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/rave-skateboards/clothing/hoodies/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/souvenirs/brand/primus/blue/')) LocalRedirect('https://www.itkkit.com/catalog/brand/primus/accessories/souvenirs/blue/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/rassvet/yellow/')) LocalRedirect('https://www.itkkit.com/catalog/brand/rassvet/clothing/t-shirts/yellow/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/sss-world-corp/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/sss-world-corp/clothing/t-shirts/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/pop-trading-company/white/')) LocalRedirect('https://www.itkkit.com/catalog/brand/pop-trading-company/clothing/t-shirts/white/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/knitwear/brand/stussy/burgundy/')) LocalRedirect('https://www.itkkit.com/catalog/brand/stussy/clothing/knitwear/burgundy/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/stussy/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/stussy/accessories/hats/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/civilist/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/civilist/clothing/t-shirts/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/pleasures/orange/')) LocalRedirect('https://www.itkkit.com/catalog/brand/pleasures/clothing/outerwear/orange/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/human-with-attitude/tie%20dye/')) LocalRedirect('https://www.itkkit.com/catalog/brand/human-with-attitude/clothing/t-shirts/tie%20dye/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/pleasures/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/pleasures/clothing/sweats/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/wallets/brand/civilist/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/civilist/accessories/wallets/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/souvenirs/brand/civilist/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/civilist/accessories/souvenirs/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/tommy-jeans/orange/')) LocalRedirect('https://www.itkkit.com/catalog/brand/tommy-jeans/clothing/sweats/orange/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/gloves/brand/norse-projects/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/norse-projects/accessories/gloves/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/needles/purple/')) LocalRedirect('https://www.itkkit.com/catalog/brand/needles/clothing/hoodies/purple/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/dr-le-de-monsieur/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/dr-le-de-monsieur/clothing/pants/multi/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/sandals/brand/suicoke/olive/')) LocalRedirect('https://www.itkkit.com/catalog/brand/suicoke/footwear/sandals/olive/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shorts/brand/stussy/olive/')) LocalRedirect('https://www.itkkit.com/catalog/brand/stussy/clothing/shorts/olive/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/fucking-awesome/blue/')) LocalRedirect('https://www.itkkit.com/catalog/brand/fucking-awesome/clothing/t-shirts/blue/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/stussy/yellow/')) LocalRedirect('https://www.itkkit.com/catalog/brand/stussy/accessories/hats/yellow/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shirts/brand/pleasures/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/pleasures/clothing/shirts/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/grooming/brand/baxter-of-california/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/baxter-of-california/accessories/grooming/multi/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/good-morning-tapes/sand/')) LocalRedirect('https://www.itkkit.com/catalog/brand/good-morning-tapes/clothing/longsleeves/sand/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/sneakers/brand/hoka-one-one/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/hoka-one-one/footwear/sneakers/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/the-north-face/yellow/')) LocalRedirect('https://www.itkkit.com/catalog/brand/the-north-face/clothing/outerwear/yellow/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shirts/brand/babylon-la/red/')) LocalRedirect('https://www.itkkit.com/catalog/brand/babylon-la/clothing/shirts/red/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/civilist/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/civilist/accessories/hats/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/sss-world-corp/red/')) LocalRedirect('https://www.itkkit.com/catalog/brand/sss-world-corp/clothing/hoodies/red/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/alpha-industries/white/')) LocalRedirect('https://www.itkkit.com/catalog/brand/alpha-industries/clothing/outerwear/white/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/sneakers/brand/premiata/white/')) LocalRedirect('https://www.itkkit.com/catalog/brand/premiata/footwear/sneakers/white/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/sandals/brand/keen/grey/')) LocalRedirect('https://www.itkkit.com/catalog/brand/keen/footwear/sandals/grey/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/pechatnye-izdaniya/brand/fucking-awesome/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/fucking-awesome/accessories/pechatnye-izdaniya/multi/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/fucking-awesome/grey/')) LocalRedirect('https://www.itkkit.com/catalog/brand/fucking-awesome/clothing/hoodies/grey/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/chinatown-market/white/')) LocalRedirect('https://www.itkkit.com/catalog/brand/chinatown-market/clothing/t-shirts/white/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shirts/brand/olaf-hussein/green/')) LocalRedirect('https://www.itkkit.com/catalog/brand/olaf-hussein/clothing/shirts/green/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/billionaire-boys-club/orange/')) LocalRedirect('https://www.itkkit.com/catalog/brand/billionaire-boys-club/clothing/longsleeves/orange/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/fucking-awesome/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/fucking-awesome/clothing/longsleeves/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/socks/brand/mki-miyuki-zoku/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/mki-miyuki-zoku/accessories/socks/multi/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/wallets/brand/civilist/blue/')) LocalRedirect('https://www.itkkit.com/catalog/brand/civilist/accessories/wallets/blue/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/chinatown-market/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/chinatown-market/clothing/sweats/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/junior-executive/grey/')) LocalRedirect('https://www.itkkit.com/catalog/brand/junior-executive/clothing/t-shirts/grey/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/ripndip/camo/')) LocalRedirect('https://www.itkkit.com/catalog/brand/ripndip/clothing/outerwear/camo/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/polythene-optics/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/polythene-optics/accessories/hats/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/l-i-e-s-records/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/l-i-e-s-records/clothing/t-shirts/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/socks/brand/mki-miyuki-zoku/pink/')) LocalRedirect('https://www.itkkit.com/catalog/brand/mki-miyuki-zoku/accessories/socks/pink/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/socks/brand/ripndip/orange/')) LocalRedirect('https://www.itkkit.com/catalog/brand/ripndip/accessories/socks/orange/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/universal-works/navy/')) LocalRedirect('https://www.itkkit.com/catalog/brand/universal-works/clothing/outerwear/navy/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/socks/brand/champion-reverse-weave/purple/')) LocalRedirect('https://www.itkkit.com/catalog/brand/champion-reverse-weave/accessories/socks/purple/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/skateboarding/brand/palace-skateboards/white/')) LocalRedirect('https://www.itkkit.com/catalog/brand/palace-skateboards/accessories/skateboarding/white/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/fucking-awesome/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/fucking-awesome/clothing/outerwear/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/dr-le-de-monsieur/yellow/')) LocalRedirect('https://www.itkkit.com/catalog/brand/dr-le-de-monsieur/clothing/outerwear/yellow/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/billionaire-boys-club/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/billionaire-boys-club/clothing/hoodies/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/sneakers/brand/premiata/grey/')) LocalRedirect('https://www.itkkit.com/catalog/brand/premiata/footwear/sneakers/grey/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shirts/brand/polar-skate-co/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/polar-skate-co/clothing/shirts/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/aded/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/aded/clothing/longsleeves/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/the-north-face/yellow/')) LocalRedirect('https://www.itkkit.com/catalog/brand/the-north-face/accessories/hats/yellow/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/knitwear/brand/rassvet/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/rassvet/clothing/knitwear/multi/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/grind-london/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/grind-london/clothing/longsleeves/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/comme-des-gar-ons-wallets/red/')) LocalRedirect('https://www.itkkit.com/catalog/brand/comme-des-gar-ons-wallets/accessories/bags/red/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/fragrances/brand/comme-des-garcons-parfum/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/comme-des-garcons-parfum/accessories/fragrances/multi/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/vests/brand/h-las/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/h-las/clothing/vests/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/daily-paper/orange/')) LocalRedirect('https://www.itkkit.com/catalog/brand/daily-paper/clothing/t-shirts/orange/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/daily-paper/grey/')) LocalRedirect('https://www.itkkit.com/catalog/brand/daily-paper/clothing/outerwear/grey/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/polythene-optics/grey/')) LocalRedirect('https://www.itkkit.com/catalog/brand/polythene-optics/accessories/bags/grey/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/souvenirs/brand/primus/green/')) LocalRedirect('https://www.itkkit.com/catalog/brand/primus/accessories/souvenirs/green/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/champion-woman/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/champion-woman/clothing/outerwear/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/billionaire-boys-club/purple/')) LocalRedirect('https://www.itkkit.com/catalog/brand/billionaire-boys-club/clothing/sweats/purple/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/pleasures/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/pleasures/clothing/t-shirts/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/tommy-jeans/black/')) LocalRedirect('https://www.itkkit.com/catalog/brand/tommy-jeans/clothing/hoodies/black/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/knitwear/brand/carne-bollente/grey/')) LocalRedirect('https://www.itkkit.com/catalog/brand/carne-bollente/clothing/knitwear/grey/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/polythene-optics/cream/')) LocalRedirect('https://www.itkkit.com/catalog/brand/polythene-optics/clothing/sweats/cream/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/pop-trading-company/grape/')) LocalRedirect('https://www.itkkit.com/catalog/brand/pop-trading-company/clothing/sweats/grape/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/fucking-awesome/grey/')) LocalRedirect('https://www.itkkit.com/catalog/brand/fucking-awesome/clothing/longsleeves/grey/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/souvenirs/brand/polar-skate-co/white/')) LocalRedirect('https://www.itkkit.com/catalog/brand/polar-skate-co/accessories/souvenirs/white/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/fragrances/brand/comme-des-garcons-parfum/red/')) LocalRedirect('https://www.itkkit.com/catalog/brand/comme-des-garcons-parfum/accessories/fragrances/red/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/still-good/multi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/still-good/accessories/hats/multi/', false, '301 Moved permanently');
	
	if (CSite::InDir('/catalog/clothing/vests/brand/daily-paper/')) LocalRedirect('https://www.itkkit.com/catalog/brand/daily-paper/clothing/vests/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/dr-le-de-monsieur/')) LocalRedirect('https://www.itkkit.com/catalog/brand/dr-le-de-monsieur/clothing/hoodies/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/polar-skate-co/')) LocalRedirect('https://www.itkkit.com/catalog/brand/polar-skate-co/clothing/longsleeves/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/dr-le-de-monsieur/')) LocalRedirect('https://www.itkkit.com/catalog/brand/dr-le-de-monsieur/clothing/sweats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/stussy/')) LocalRedirect('https://www.itkkit.com/catalog/brand/stussy/clothing/hoodies/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/wallets/brand/comme-des-gar-ons-wallets/')) LocalRedirect('https://www.itkkit.com/catalog/brand/comme-des-gar-ons-wallets/accessories/wallets/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/p-a-m/')) LocalRedirect('https://www.itkkit.com/catalog/brand/p-a-m/accessories/hats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/daily-paper/')) LocalRedirect('https://www.itkkit.com/catalog/brand/daily-paper/accessories/bags/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/universal-works/')) LocalRedirect('https://www.itkkit.com/catalog/brand/universal-works/clothing/pants/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/vests/brand/engineered-garments/')) LocalRedirect('https://www.itkkit.com/catalog/brand/engineered-garments/clothing/vests/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/aded/')) LocalRedirect('https://www.itkkit.com/catalog/brand/aded/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/the-trilogy-tapes/')) LocalRedirect('https://www.itkkit.com/catalog/brand/the-trilogy-tapes/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/knitwear/brand/polar-skate-co/')) LocalRedirect('https://www.itkkit.com/catalog/brand/polar-skate-co/clothing/knitwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/shoes/brand/barbour/')) LocalRedirect('https://www.itkkit.com/catalog/brand/barbour/footwear/shoes/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/patta/')) LocalRedirect('https://www.itkkit.com/catalog/brand/patta/clothing/longsleeves/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/human-with-attitude/')) LocalRedirect('https://www.itkkit.com/catalog/brand/human-with-attitude/accessories/bags/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/the-north-face/')) LocalRedirect('https://www.itkkit.com/catalog/brand/the-north-face/clothing/outerwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/belts/brand/daily-paper/')) LocalRedirect('https://www.itkkit.com/catalog/brand/daily-paper/accessories/belts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/knitwear/brand/universal-works/')) LocalRedirect('https://www.itkkit.com/catalog/brand/universal-works/clothing/knitwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/parkas/brand/carhartt-wip/')) LocalRedirect('https://www.itkkit.com/catalog/brand/carhartt-wip/clothing/parkas/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/m-rc-noir/')) LocalRedirect('https://www.itkkit.com/catalog/brand/m-rc-noir/accessories/bags/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/patta/')) LocalRedirect('https://www.itkkit.com/catalog/brand/patta/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/p-a-m/')) LocalRedirect('https://www.itkkit.com/catalog/brand/p-a-m/clothing/longsleeves/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/polar-skate-co/')) LocalRedirect('https://www.itkkit.com/catalog/brand/polar-skate-co/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/maharishi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/maharishi/clothing/longsleeves/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/patagonia/')) LocalRedirect('https://www.itkkit.com/catalog/brand/patagonia/accessories/hats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/skateboarding/brand/rassvet/')) LocalRedirect('https://www.itkkit.com/catalog/brand/rassvet/accessories/skateboarding/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shirts/brand/maharishi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/maharishi/clothing/shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/stussy/')) LocalRedirect('https://www.itkkit.com/catalog/brand/stussy/clothing/pants/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/m-rc-noir/')) LocalRedirect('https://www.itkkit.com/catalog/brand/m-rc-noir/clothing/hoodies/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/fucking-awesome/')) LocalRedirect('https://www.itkkit.com/catalog/brand/fucking-awesome/clothing/sweats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/arte-antwerp/')) LocalRedirect('https://www.itkkit.com/catalog/brand/arte-antwerp/clothing/hoodies/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/comme-des-gar-ons-play/')) LocalRedirect('https://www.itkkit.com/catalog/brand/comme-des-gar-ons-play/clothing/hoodies/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/fucking-awesome/')) LocalRedirect('https://www.itkkit.com/catalog/brand/fucking-awesome/clothing/longsleeves/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shirts/brand/needles/')) LocalRedirect('https://www.itkkit.com/catalog/brand/needles/clothing/shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shirts/brand/fucking-awesome/')) LocalRedirect('https://www.itkkit.com/catalog/brand/fucking-awesome/clothing/shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/champion-reverse-weave/')) LocalRedirect('https://www.itkkit.com/catalog/brand/champion-reverse-weave/clothing/hoodies/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/engineered-garments/')) LocalRedirect('https://www.itkkit.com/catalog/brand/engineered-garments/clothing/pants/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/vests/brand/maharishi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/maharishi/clothing/vests/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/pleasures/')) LocalRedirect('https://www.itkkit.com/catalog/brand/pleasures/clothing/hoodies/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/knitwear/brand/fucking-awesome/')) LocalRedirect('https://www.itkkit.com/catalog/brand/fucking-awesome/clothing/knitwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/daily-paper/')) LocalRedirect('https://www.itkkit.com/catalog/brand/daily-paper/clothing/pants/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shirts/brand/carne-bollente/')) LocalRedirect('https://www.itkkit.com/catalog/brand/carne-bollente/clothing/shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shirts/brand/rassvet/')) LocalRedirect('https://www.itkkit.com/catalog/brand/rassvet/clothing/shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/sandals/brand/pleasures/')) LocalRedirect('https://www.itkkit.com/catalog/brand/pleasures/footwear/sandals/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/gloves/brand/norse-projects/')) LocalRedirect('https://www.itkkit.com/catalog/brand/norse-projects/accessories/gloves/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/daily-paper/')) LocalRedirect('https://www.itkkit.com/catalog/brand/daily-paper/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/parkas/brand/champion-reverse-weave/')) LocalRedirect('https://www.itkkit.com/catalog/brand/champion-reverse-weave/clothing/parkas/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/rassvet/')) LocalRedirect('https://www.itkkit.com/catalog/brand/rassvet/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/human-with-attitude/')) LocalRedirect('https://www.itkkit.com/catalog/brand/human-with-attitude/clothing/outerwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/comme-des-gar-ons-wallets/')) LocalRedirect('https://www.itkkit.com/catalog/brand/comme-des-gar-ons-wallets/accessories/bags/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/arte-antwerp/')) LocalRedirect('https://www.itkkit.com/catalog/brand/arte-antwerp/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/the-north-face/')) LocalRedirect('https://www.itkkit.com/catalog/brand/the-north-face/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/shoes/brand/suicoke/')) LocalRedirect('https://www.itkkit.com/catalog/brand/suicoke/footwear/shoes/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/knitwear/brand/carne-bollente/')) LocalRedirect('https://www.itkkit.com/catalog/brand/carne-bollente/clothing/knitwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/sneakers/brand/comme-des-gar-ons-play/')) LocalRedirect('https://www.itkkit.com/catalog/brand/comme-des-gar-ons-play/footwear/sneakers/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/knitwear/brand/norse-projects/')) LocalRedirect('https://www.itkkit.com/catalog/brand/norse-projects/clothing/knitwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/grooming/brand/baxter-of-california/')) LocalRedirect('https://www.itkkit.com/catalog/brand/baxter-of-california/accessories/grooming/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/casablanca/')) LocalRedirect('https://www.itkkit.com/catalog/brand/casablanca/clothing/sweats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/pleasures/')) LocalRedirect('https://www.itkkit.com/catalog/brand/pleasures/clothing/outerwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/m-rc-noir/')) LocalRedirect('https://www.itkkit.com/catalog/brand/m-rc-noir/accessories/hats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/dr-le-de-monsieur/')) LocalRedirect('https://www.itkkit.com/catalog/brand/dr-le-de-monsieur/clothing/pants/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/fucking-awesome/')) LocalRedirect('https://www.itkkit.com/catalog/brand/fucking-awesome/clothing/hoodies/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/comme-des-gar-ons-play/')) LocalRedirect('https://www.itkkit.com/catalog/brand/comme-des-gar-ons-play/clothing/longsleeves/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shirts/brand/comme-des-gar-ons-shirt/')) LocalRedirect('https://www.itkkit.com/catalog/brand/comme-des-gar-ons-shirt/clothing/shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/patagonia/')) LocalRedirect('https://www.itkkit.com/catalog/brand/patagonia/clothing/longsleeves/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/idea/')) LocalRedirect('https://www.itkkit.com/catalog/brand/idea/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/sneakers/brand/hoka-one-one/')) LocalRedirect('https://www.itkkit.com/catalog/brand/hoka-one-one/footwear/sneakers/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/sneakers/brand/new-balance/')) LocalRedirect('https://www.itkkit.com/catalog/brand/new-balance/footwear/sneakers/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shorts/brand/stussy/')) LocalRedirect('https://www.itkkit.com/catalog/brand/stussy/clothing/shorts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/p-a-m/')) LocalRedirect('https://www.itkkit.com/catalog/brand/p-a-m/clothing/outerwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shirts/brand/casablanca/')) LocalRedirect('https://www.itkkit.com/catalog/brand/casablanca/clothing/shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/universal-works/')) LocalRedirect('https://www.itkkit.com/catalog/brand/universal-works/clothing/sweats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/patagonia/')) LocalRedirect('https://www.itkkit.com/catalog/brand/patagonia/clothing/outerwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/daily-paper/')) LocalRedirect('https://www.itkkit.com/catalog/brand/daily-paper/clothing/hoodies/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/the-north-face/')) LocalRedirect('https://www.itkkit.com/catalog/brand/the-north-face/accessories/bags/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/aries/')) LocalRedirect('https://www.itkkit.com/catalog/brand/aries/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/barbour/')) LocalRedirect('https://www.itkkit.com/catalog/brand/barbour/accessories/hats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/sandals/brand/the-north-face/')) LocalRedirect('https://www.itkkit.com/catalog/brand/the-north-face/footwear/sandals/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/grooming/brand/marvis/')) LocalRedirect('https://www.itkkit.com/catalog/brand/marvis/accessories/grooming/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shirts/brand/norse-projects/')) LocalRedirect('https://www.itkkit.com/catalog/brand/norse-projects/clothing/shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/stussy/')) LocalRedirect('https://www.itkkit.com/catalog/brand/stussy/clothing/outerwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/stussy/')) LocalRedirect('https://www.itkkit.com/catalog/brand/stussy/accessories/bags/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/casablanca/')) LocalRedirect('https://www.itkkit.com/catalog/brand/casablanca/clothing/pants/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/skateboarding/brand/fucking-awesome/')) LocalRedirect('https://www.itkkit.com/catalog/brand/fucking-awesome/accessories/skateboarding/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/knitwear/brand/arte-antwerp/')) LocalRedirect('https://www.itkkit.com/catalog/brand/arte-antwerp/clothing/knitwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/belts/brand/comme-des-gar-ons-wallets/')) LocalRedirect('https://www.itkkit.com/catalog/brand/comme-des-gar-ons-wallets/accessories/belts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/patagonia/')) LocalRedirect('https://www.itkkit.com/catalog/brand/patagonia/accessories/bags/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/souvenirs/brand/carhartt-wip/')) LocalRedirect('https://www.itkkit.com/catalog/brand/carhartt-wip/accessories/souvenirs/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/carne-bollente/')) LocalRedirect('https://www.itkkit.com/catalog/brand/carne-bollente/clothing/longsleeves/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/comme-des-gar-ons-shirt/')) LocalRedirect('https://www.itkkit.com/catalog/brand/comme-des-gar-ons-shirt/clothing/longsleeves/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/m-rc-noir/')) LocalRedirect('https://www.itkkit.com/catalog/brand/m-rc-noir/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/needles/')) LocalRedirect('https://www.itkkit.com/catalog/brand/needles/clothing/pants/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/vests/brand/universal-works/')) LocalRedirect('https://www.itkkit.com/catalog/brand/universal-works/clothing/vests/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/souvenirs/brand/stussy/')) LocalRedirect('https://www.itkkit.com/catalog/brand/stussy/accessories/souvenirs/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/rassvet/')) LocalRedirect('https://www.itkkit.com/catalog/brand/rassvet/clothing/pants/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/p-a-m/')) LocalRedirect('https://www.itkkit.com/catalog/brand/p-a-m/clothing/pants/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/polar-skate-co/')) LocalRedirect('https://www.itkkit.com/catalog/brand/polar-skate-co/clothing/hoodies/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/needles/')) LocalRedirect('https://www.itkkit.com/catalog/brand/needles/clothing/hoodies/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/bombers/brand/p-a-m/')) LocalRedirect('https://www.itkkit.com/catalog/brand/p-a-m/clothing/bombers/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/universal-works/')) LocalRedirect('https://www.itkkit.com/catalog/brand/universal-works/clothing/outerwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/belts/brand/stussy/')) LocalRedirect('https://www.itkkit.com/catalog/brand/stussy/accessories/belts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/aries/')) LocalRedirect('https://www.itkkit.com/catalog/brand/aries/clothing/sweats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/the-north-face/')) LocalRedirect('https://www.itkkit.com/catalog/brand/the-north-face/accessories/hats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/jeans/brand/needles/')) LocalRedirect('https://www.itkkit.com/catalog/brand/needles/clothing/jeans/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shorts/brand/m-rc-noir/')) LocalRedirect('https://www.itkkit.com/catalog/brand/m-rc-noir/clothing/shorts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/souvenirs/brand/polar-skate-co/')) LocalRedirect('https://www.itkkit.com/catalog/brand/polar-skate-co/accessories/souvenirs/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/aries/')) LocalRedirect('https://www.itkkit.com/catalog/brand/aries/clothing/pants/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shirts/brand/stussy/')) LocalRedirect('https://www.itkkit.com/catalog/brand/stussy/clothing/shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/p-a-m/')) LocalRedirect('https://www.itkkit.com/catalog/brand/p-a-m/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/barbour/')) LocalRedirect('https://www.itkkit.com/catalog/brand/barbour/accessories/bags/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/human-with-attitude/')) LocalRedirect('https://www.itkkit.com/catalog/brand/human-with-attitude/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/sneakers/brand/clarks-originals/')) LocalRedirect('https://www.itkkit.com/catalog/brand/clarks-originals/footwear/sneakers/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/pleasures/')) LocalRedirect('https://www.itkkit.com/catalog/brand/pleasures/clothing/sweats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/m-rc-noir/')) LocalRedirect('https://www.itkkit.com/catalog/brand/m-rc-noir/clothing/pants/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/aded/')) LocalRedirect('https://www.itkkit.com/catalog/brand/aded/clothing/longsleeves/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/maharishi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/maharishi/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/casablanca/')) LocalRedirect('https://www.itkkit.com/catalog/brand/casablanca/clothing/hoodies/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/polar-skate-co/')) LocalRedirect('https://www.itkkit.com/catalog/brand/polar-skate-co/clothing/pants/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/arte-antwerp/')) LocalRedirect('https://www.itkkit.com/catalog/brand/arte-antwerp/clothing/longsleeves/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/skateboarding/brand/palace-skateboards/')) LocalRedirect('https://www.itkkit.com/catalog/brand/palace-skateboards/accessories/skateboarding/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/vests/brand/human-with-attitude/')) LocalRedirect('https://www.itkkit.com/catalog/brand/human-with-attitude/clothing/vests/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/champion-reverse-weave/')) LocalRedirect('https://www.itkkit.com/catalog/brand/champion-reverse-weave/accessories/hats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/souvenirs/brand/carne-bollente/')) LocalRedirect('https://www.itkkit.com/catalog/brand/carne-bollente/accessories/souvenirs/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/polar-skate-co/')) LocalRedirect('https://www.itkkit.com/catalog/brand/polar-skate-co/clothing/outerwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/engineered-garments/')) LocalRedirect('https://www.itkkit.com/catalog/brand/engineered-garments/accessories/bags/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/aries/')) LocalRedirect('https://www.itkkit.com/catalog/brand/aries/clothing/longsleeves/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/knitwear/brand/needles/')) LocalRedirect('https://www.itkkit.com/catalog/brand/needles/clothing/knitwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/stussy/')) LocalRedirect('https://www.itkkit.com/catalog/brand/stussy/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/norse-projects/')) LocalRedirect('https://www.itkkit.com/catalog/brand/norse-projects/clothing/pants/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/socks/brand/polar-skate-co/')) LocalRedirect('https://www.itkkit.com/catalog/brand/polar-skate-co/accessories/socks/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/barbour/')) LocalRedirect('https://www.itkkit.com/catalog/brand/barbour/clothing/outerwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/aries/')) LocalRedirect('https://www.itkkit.com/catalog/brand/aries/clothing/hoodies/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/stussy/')) LocalRedirect('https://www.itkkit.com/catalog/brand/stussy/clothing/sweats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shirts/brand/pleasures/')) LocalRedirect('https://www.itkkit.com/catalog/brand/pleasures/clothing/shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/rassvet/')) LocalRedirect('https://www.itkkit.com/catalog/brand/rassvet/accessories/hats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/souvenirs/brand/maharishi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/maharishi/accessories/souvenirs/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/pechatnye-izdaniya/brand/idea/')) LocalRedirect('https://www.itkkit.com/catalog/brand/idea/accessories/pechatnye-izdaniya/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/daily-paper/')) LocalRedirect('https://www.itkkit.com/catalog/brand/daily-paper/clothing/longsleeves/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/knitwear/brand/stussy/')) LocalRedirect('https://www.itkkit.com/catalog/brand/stussy/clothing/knitwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shorts/brand/daily-paper/')) LocalRedirect('https://www.itkkit.com/catalog/brand/daily-paper/clothing/shorts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/pop-trading-company/')) LocalRedirect('https://www.itkkit.com/catalog/brand/pop-trading-company/clothing/outerwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/patta/')) LocalRedirect('https://www.itkkit.com/catalog/brand/patta/accessories/bags/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/daily-paper/')) LocalRedirect('https://www.itkkit.com/catalog/brand/daily-paper/clothing/outerwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/socks/brand/aries/')) LocalRedirect('https://www.itkkit.com/catalog/brand/aries/accessories/socks/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/wallets/brand/carne-bollente/')) LocalRedirect('https://www.itkkit.com/catalog/brand/carne-bollente/accessories/wallets/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/socks/brand/carne-bollente/')) LocalRedirect('https://www.itkkit.com/catalog/brand/carne-bollente/accessories/socks/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/comme-des-gar-ons-shirt/')) LocalRedirect('https://www.itkkit.com/catalog/brand/comme-des-gar-ons-shirt/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/souvenirs/brand/barbour/')) LocalRedirect('https://www.itkkit.com/catalog/brand/barbour/accessories/souvenirs/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/human-with-attitude/')) LocalRedirect('https://www.itkkit.com/catalog/brand/human-with-attitude/clothing/longsleeves/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/arte-antwerp/')) LocalRedirect('https://www.itkkit.com/catalog/brand/arte-antwerp/clothing/pants/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shirts/brand/polar-skate-co/')) LocalRedirect('https://www.itkkit.com/catalog/brand/polar-skate-co/clothing/shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/maharishi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/maharishi/clothing/pants/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/daily-paper/')) LocalRedirect('https://www.itkkit.com/catalog/brand/daily-paper/clothing/sweats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/dr-le-de-monsieur/')) LocalRedirect('https://www.itkkit.com/catalog/brand/dr-le-de-monsieur/accessories/hats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/pop-trading-company/')) LocalRedirect('https://www.itkkit.com/catalog/brand/pop-trading-company/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/carhartt-wip/')) LocalRedirect('https://www.itkkit.com/catalog/brand/carhartt-wip/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/pechatnye-izdaniya/brand/fucking-awesome/')) LocalRedirect('https://www.itkkit.com/catalog/brand/fucking-awesome/accessories/pechatnye-izdaniya/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/idea/')) LocalRedirect('https://www.itkkit.com/catalog/brand/idea/accessories/bags/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/pop-trading-company/')) LocalRedirect('https://www.itkkit.com/catalog/brand/pop-trading-company/clothing/pants/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/sneakers/brand/rassvet/')) LocalRedirect('https://www.itkkit.com/catalog/brand/rassvet/footwear/sneakers/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/vests/brand/patagonia/')) LocalRedirect('https://www.itkkit.com/catalog/brand/patagonia/clothing/vests/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/human-with-attitude/')) LocalRedirect('https://www.itkkit.com/catalog/brand/human-with-attitude/clothing/pants/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/casablanca/')) LocalRedirect('https://www.itkkit.com/catalog/brand/casablanca/clothing/outerwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/sandals/brand/suicoke/')) LocalRedirect('https://www.itkkit.com/catalog/brand/suicoke/footwear/sandals/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/carne-bollente/')) LocalRedirect('https://www.itkkit.com/catalog/brand/carne-bollente/clothing/hoodies/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/socks/brand/rassvet/')) LocalRedirect('https://www.itkkit.com/catalog/brand/rassvet/accessories/socks/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/stussy/')) LocalRedirect('https://www.itkkit.com/catalog/brand/stussy/accessories/hats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/pleasures/')) LocalRedirect('https://www.itkkit.com/catalog/brand/pleasures/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/polar-skate-co/')) LocalRedirect('https://www.itkkit.com/catalog/brand/polar-skate-co/accessories/hats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/shoes/brand/diemme/')) LocalRedirect('https://www.itkkit.com/catalog/brand/diemme/footwear/shoes/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/souvenirs/brand/fucking-awesome/')) LocalRedirect('https://www.itkkit.com/catalog/brand/fucking-awesome/accessories/souvenirs/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/patagonia/')) LocalRedirect('https://www.itkkit.com/catalog/brand/patagonia/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/knitwear/brand/p-a-m/')) LocalRedirect('https://www.itkkit.com/catalog/brand/p-a-m/clothing/knitwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/knitwear/brand/rassvet/')) LocalRedirect('https://www.itkkit.com/catalog/brand/rassvet/clothing/knitwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/rassvet/')) LocalRedirect('https://www.itkkit.com/catalog/brand/rassvet/accessories/bags/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/dr-le-de-monsieur/')) LocalRedirect('https://www.itkkit.com/catalog/brand/dr-le-de-monsieur/clothing/outerwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/pleasures/')) LocalRedirect('https://www.itkkit.com/catalog/brand/pleasures/accessories/bags/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/pop-trading-company/')) LocalRedirect('https://www.itkkit.com/catalog/brand/pop-trading-company/accessories/hats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/patta/')) LocalRedirect('https://www.itkkit.com/catalog/brand/patta/clothing/pants/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/rassvet/')) LocalRedirect('https://www.itkkit.com/catalog/brand/rassvet/clothing/sweats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shirts/brand/engineered-garments/')) LocalRedirect('https://www.itkkit.com/catalog/brand/engineered-garments/clothing/shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/knitwear/brand/aries/')) LocalRedirect('https://www.itkkit.com/catalog/brand/aries/clothing/knitwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/carne-bollente/')) LocalRedirect('https://www.itkkit.com/catalog/brand/carne-bollente/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/pop-trading-company/')) LocalRedirect('https://www.itkkit.com/catalog/brand/pop-trading-company/clothing/sweats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/pleasures/')) LocalRedirect('https://www.itkkit.com/catalog/brand/pleasures/clothing/pants/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/champion-reverse-weave/')) LocalRedirect('https://www.itkkit.com/catalog/brand/champion-reverse-weave/clothing/outerwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/jeans/brand/polar-skate-co/')) LocalRedirect('https://www.itkkit.com/catalog/brand/polar-skate-co/clothing/jeans/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/pleasures/')) LocalRedirect('https://www.itkkit.com/catalog/brand/pleasures/clothing/longsleeves/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shirts/brand/nigel-cabourn/')) LocalRedirect('https://www.itkkit.com/catalog/brand/nigel-cabourn/clothing/shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/maharishi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/maharishi/accessories/bags/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/arte-antwerp/')) LocalRedirect('https://www.itkkit.com/catalog/brand/arte-antwerp/clothing/sweats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/sandals/brand/birkenstock/')) LocalRedirect('https://www.itkkit.com/catalog/brand/birkenstock/footwear/sandals/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/the-trilogy-tapes/')) LocalRedirect('https://www.itkkit.com/catalog/brand/the-trilogy-tapes/clothing/longsleeves/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/daily-paper/')) LocalRedirect('https://www.itkkit.com/catalog/brand/daily-paper/accessories/hats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/vests/brand/pop-trading-company/')) LocalRedirect('https://www.itkkit.com/catalog/brand/pop-trading-company/clothing/vests/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/needles/')) LocalRedirect('https://www.itkkit.com/catalog/brand/needles/clothing/sweats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/idea/')) LocalRedirect('https://www.itkkit.com/catalog/brand/idea/accessories/hats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shirts/brand/daily-paper/')) LocalRedirect('https://www.itkkit.com/catalog/brand/daily-paper/clothing/shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/human-with-attitude/')) LocalRedirect('https://www.itkkit.com/catalog/brand/human-with-attitude/clothing/hoodies/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/m-rc-noir/')) LocalRedirect('https://www.itkkit.com/catalog/brand/m-rc-noir/clothing/outerwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/gloves/brand/the-north-face/')) LocalRedirect('https://www.itkkit.com/catalog/brand/the-north-face/accessories/gloves/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/rassvet/')) LocalRedirect('https://www.itkkit.com/catalog/brand/rassvet/clothing/outerwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/fucking-awesome/')) LocalRedirect('https://www.itkkit.com/catalog/brand/fucking-awesome/accessories/hats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/fragrances/brand/comme-des-garcons-parfum/')) LocalRedirect('https://www.itkkit.com/catalog/brand/comme-des-garcons-parfum/accessories/fragrances/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/comme-des-gar-ons-play/')) LocalRedirect('https://www.itkkit.com/catalog/brand/comme-des-gar-ons-play/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/socks/brand/champion-reverse-weave/')) LocalRedirect('https://www.itkkit.com/catalog/brand/champion-reverse-weave/accessories/socks/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/fucking-awesome/')) LocalRedirect('https://www.itkkit.com/catalog/brand/fucking-awesome/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shorts/brand/pleasures/')) LocalRedirect('https://www.itkkit.com/catalog/brand/pleasures/clothing/shorts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/pleasures/')) LocalRedirect('https://www.itkkit.com/catalog/brand/pleasures/accessories/hats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/shoes/brand/clarks-originals/')) LocalRedirect('https://www.itkkit.com/catalog/brand/clarks-originals/footwear/shoes/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/aded/')) LocalRedirect('https://www.itkkit.com/catalog/brand/aded/clothing/hoodies/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/nigel-cabourn/')) LocalRedirect('https://www.itkkit.com/catalog/brand/nigel-cabourn/clothing/outerwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/knitwear/brand/pleasures/')) LocalRedirect('https://www.itkkit.com/catalog/brand/pleasures/clothing/knitwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/knitwear/brand/nigel-cabourn/')) LocalRedirect('https://www.itkkit.com/catalog/brand/nigel-cabourn/clothing/knitwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/polar-skate-co/')) LocalRedirect('https://www.itkkit.com/catalog/brand/polar-skate-co/accessories/bags/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/parkas/brand/m-rc-noir/')) LocalRedirect('https://www.itkkit.com/catalog/brand/m-rc-noir/clothing/parkas/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/champion-reverse-weave/')) LocalRedirect('https://www.itkkit.com/catalog/brand/champion-reverse-weave/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/fucking-awesome/')) LocalRedirect('https://www.itkkit.com/catalog/brand/fucking-awesome/clothing/outerwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/champion-reverse-weave/')) LocalRedirect('https://www.itkkit.com/catalog/brand/champion-reverse-weave/clothing/pants/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/patagonia/')) LocalRedirect('https://www.itkkit.com/catalog/brand/patagonia/clothing/hoodies/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shirts/brand/comme-des-gar-ons-play/')) LocalRedirect('https://www.itkkit.com/catalog/brand/comme-des-gar-ons-play/clothing/shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/stussy/')) LocalRedirect('https://www.itkkit.com/catalog/brand/stussy/clothing/longsleeves/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/h-las/')) LocalRedirect('https://www.itkkit.com/catalog/brand/h-las/accessories/hats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/polythene-optics/')) LocalRedirect('https://www.itkkit.com/catalog/brand/polythene-optics/clothing/outerwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/rave-skateboards/')) LocalRedirect('https://www.itkkit.com/catalog/brand/rave-skateboards/clothing/pants/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/still-good/')) LocalRedirect('https://www.itkkit.com/catalog/brand/still-good/clothing/longsleeves/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/sss-world-corp/')) LocalRedirect('https://www.itkkit.com/catalog/brand/sss-world-corp/clothing/hoodies/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/rokit/')) LocalRedirect('https://www.itkkit.com/catalog/brand/rokit/clothing/longsleeves/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shirts/brand/billionaire-boys-club/')) LocalRedirect('https://www.itkkit.com/catalog/brand/billionaire-boys-club/clothing/shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/tommy-jeans-woman/')) LocalRedirect('https://www.itkkit.com/catalog/brand/tommy-jeans-woman/accessories/bags/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/platformx/')) LocalRedirect('https://www.itkkit.com/catalog/brand/platformx/clothing/hoodies/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shirts/brand/h-las/')) LocalRedirect('https://www.itkkit.com/catalog/brand/h-las/clothing/shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shirts/brand/babylon-la/')) LocalRedirect('https://www.itkkit.com/catalog/brand/babylon-la/clothing/shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/bronze-56k/')) LocalRedirect('https://www.itkkit.com/catalog/brand/bronze-56k/clothing/sweats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shirts/brand/assid/')) LocalRedirect('https://www.itkkit.com/catalog/brand/assid/clothing/shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/liam-hodges/')) LocalRedirect('https://www.itkkit.com/catalog/brand/liam-hodges/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/sneakers/brand/fila-woman/')) LocalRedirect('https://www.itkkit.com/catalog/brand/fila-woman/footwear/sneakers/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/nasaseasons/')) LocalRedirect('https://www.itkkit.com/catalog/brand/nasaseasons/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/rokit/')) LocalRedirect('https://www.itkkit.com/catalog/brand/rokit/clothing/pants/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/socks/brand/still-good/')) LocalRedirect('https://www.itkkit.com/catalog/brand/still-good/accessories/socks/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/call-me-917/')) LocalRedirect('https://www.itkkit.com/catalog/brand/call-me-917/clothing/longsleeves/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/sss-world-corp/')) LocalRedirect('https://www.itkkit.com/catalog/brand/sss-world-corp/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/souvenirs/brand/polythene-optics/')) LocalRedirect('https://www.itkkit.com/catalog/brand/polythene-optics/accessories/souvenirs/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/ripndip/')) LocalRedirect('https://www.itkkit.com/catalog/brand/ripndip/clothing/outerwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/sergio-tacchini/')) LocalRedirect('https://www.itkkit.com/catalog/brand/sergio-tacchini/clothing/pants/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/vests/brand/h-las/')) LocalRedirect('https://www.itkkit.com/catalog/brand/h-las/clothing/vests/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/shoes/brand/mephisto/')) LocalRedirect('https://www.itkkit.com/catalog/brand/mephisto/footwear/shoes/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/chinatown-market/')) LocalRedirect('https://www.itkkit.com/catalog/brand/chinatown-market/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/ripndip/')) LocalRedirect('https://www.itkkit.com/catalog/brand/ripndip/clothing/hoodies/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/sergio-tacchini/')) LocalRedirect('https://www.itkkit.com/catalog/brand/sergio-tacchini/clothing/sweats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/sneakers/brand/adidas-originals/')) LocalRedirect('https://www.itkkit.com/catalog/brand/adidas-originals/footwear/sneakers/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/carrots/')) LocalRedirect('https://www.itkkit.com/catalog/brand/carrots/clothing/hoodies/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/life-s-a-beach/')) LocalRedirect('https://www.itkkit.com/catalog/brand/life-s-a-beach/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/call-me-917/')) LocalRedirect('https://www.itkkit.com/catalog/brand/call-me-917/clothing/pants/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/carrots/')) LocalRedirect('https://www.itkkit.com/catalog/brand/carrots/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/nasaseasons/')) LocalRedirect('https://www.itkkit.com/catalog/brand/nasaseasons/accessories/hats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/billionaire-boys-club/')) LocalRedirect('https://www.itkkit.com/catalog/brand/billionaire-boys-club/clothing/sweats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/bronze-56k/')) LocalRedirect('https://www.itkkit.com/catalog/brand/bronze-56k/clothing/pants/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/resort-corps/')) LocalRedirect('https://www.itkkit.com/catalog/brand/resort-corps/clothing/hoodies/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/souvenirs/brand/ripndip/')) LocalRedirect('https://www.itkkit.com/catalog/brand/ripndip/accessories/souvenirs/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/sss-world-corp/')) LocalRedirect('https://www.itkkit.com/catalog/brand/sss-world-corp/clothing/outerwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/h-las/')) LocalRedirect('https://www.itkkit.com/catalog/brand/h-las/clothing/sweats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/polythene-optics/')) LocalRedirect('https://www.itkkit.com/catalog/brand/polythene-optics/clothing/sweats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/kappa/')) LocalRedirect('https://www.itkkit.com/catalog/brand/kappa/clothing/sweats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/souvenirs/brand/sporadic/')) LocalRedirect('https://www.itkkit.com/catalog/brand/sporadic/accessories/souvenirs/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/lack-of-guidance/')) LocalRedirect('https://www.itkkit.com/catalog/brand/lack-of-guidance/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/olaf-hussein/')) LocalRedirect('https://www.itkkit.com/catalog/brand/olaf-hussein/clothing/longsleeves/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/call-me-917/')) LocalRedirect('https://www.itkkit.com/catalog/brand/call-me-917/clothing/hoodies/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/jeans/brand/tommy-jeans-woman/')) LocalRedirect('https://www.itkkit.com/catalog/brand/tommy-jeans-woman/clothing/jeans/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/babylon-la/')) LocalRedirect('https://www.itkkit.com/catalog/brand/babylon-la/clothing/hoodies/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/socks/brand/civilist/')) LocalRedirect('https://www.itkkit.com/catalog/brand/civilist/accessories/socks/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/tommy-jeans-woman/')) LocalRedirect('https://www.itkkit.com/catalog/brand/tommy-jeans-woman/clothing/sweats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/lack-of-guidance/')) LocalRedirect('https://www.itkkit.com/catalog/brand/lack-of-guidance/clothing/longsleeves/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shorts/brand/h-las/')) LocalRedirect('https://www.itkkit.com/catalog/brand/h-las/clothing/shorts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shirts/brand/olaf-hussein/')) LocalRedirect('https://www.itkkit.com/catalog/brand/olaf-hussein/clothing/shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/sneakers/brand/tommy-jeans/')) LocalRedirect('https://www.itkkit.com/catalog/brand/tommy-jeans/footwear/sneakers/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/kappa/')) LocalRedirect('https://www.itkkit.com/catalog/brand/kappa/accessories/bags/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/tommy-jeans-woman/')) LocalRedirect('https://www.itkkit.com/catalog/brand/tommy-jeans-woman/clothing/longsleeves/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/rave-skateboards/')) LocalRedirect('https://www.itkkit.com/catalog/brand/rave-skateboards/accessories/hats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/good-morning-tapes/')) LocalRedirect('https://www.itkkit.com/catalog/brand/good-morning-tapes/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/h-las/')) LocalRedirect('https://www.itkkit.com/catalog/brand/h-las/clothing/longsleeves/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/champion-woman/')) LocalRedirect('https://www.itkkit.com/catalog/brand/champion-woman/clothing/outerwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/carrots/')) LocalRedirect('https://www.itkkit.com/catalog/brand/carrots/accessories/hats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/bronze-56k/')) LocalRedirect('https://www.itkkit.com/catalog/brand/bronze-56k/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/vests/brand/rave-skateboards/')) LocalRedirect('https://www.itkkit.com/catalog/brand/rave-skateboards/clothing/vests/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/know-wave/')) LocalRedirect('https://www.itkkit.com/catalog/brand/know-wave/clothing/sweats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/bronze-56k/')) LocalRedirect('https://www.itkkit.com/catalog/brand/bronze-56k/accessories/hats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/olaf-hussein/')) LocalRedirect('https://www.itkkit.com/catalog/brand/olaf-hussein/clothing/hoodies/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/sneakers/brand/fila/')) LocalRedirect('https://www.itkkit.com/catalog/brand/fila/footwear/sneakers/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/taikan/')) LocalRedirect('https://www.itkkit.com/catalog/brand/taikan/accessories/bags/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/assid/')) LocalRedirect('https://www.itkkit.com/catalog/brand/assid/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shirts/brand/polythene-optics/')) LocalRedirect('https://www.itkkit.com/catalog/brand/polythene-optics/clothing/shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/know-wave/')) LocalRedirect('https://www.itkkit.com/catalog/brand/know-wave/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/souvenirs/brand/chinatown-market/')) LocalRedirect('https://www.itkkit.com/catalog/brand/chinatown-market/accessories/souvenirs/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/skateboarding/brand/alltimers/')) LocalRedirect('https://www.itkkit.com/catalog/brand/alltimers/accessories/skateboarding/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/junior-executive/')) LocalRedirect('https://www.itkkit.com/catalog/brand/junior-executive/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/h-las/')) LocalRedirect('https://www.itkkit.com/catalog/brand/h-las/clothing/hoodies/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/alpha-industries/')) LocalRedirect('https://www.itkkit.com/catalog/brand/alpha-industries/clothing/outerwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/assid/')) LocalRedirect('https://www.itkkit.com/catalog/brand/assid/clothing/longsleeves/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/knitwear/brand/sss-world-corp/')) LocalRedirect('https://www.itkkit.com/catalog/brand/sss-world-corp/clothing/knitwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/bombers/brand/alpha-industries/')) LocalRedirect('https://www.itkkit.com/catalog/brand/alpha-industries/clothing/bombers/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/grind-london/')) LocalRedirect('https://www.itkkit.com/catalog/brand/grind-london/clothing/longsleeves/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/tommy-jeans/')) LocalRedirect('https://www.itkkit.com/catalog/brand/tommy-jeans/accessories/hats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/babylon-la/')) LocalRedirect('https://www.itkkit.com/catalog/brand/babylon-la/accessories/hats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/civilist/')) LocalRedirect('https://www.itkkit.com/catalog/brand/civilist/clothing/hoodies/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/sporadic/')) LocalRedirect('https://www.itkkit.com/catalog/brand/sporadic/clothing/outerwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/wallets/brand/civilist/')) LocalRedirect('https://www.itkkit.com/catalog/brand/civilist/accessories/wallets/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/fragrances/brand/sporadic/')) LocalRedirect('https://www.itkkit.com/catalog/brand/sporadic/accessories/fragrances/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/souvenirs/brand/primus/')) LocalRedirect('https://www.itkkit.com/catalog/brand/primus/accessories/souvenirs/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/vests/brand/gasius/')) LocalRedirect('https://www.itkkit.com/catalog/brand/gasius/clothing/vests/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/ripndip/')) LocalRedirect('https://www.itkkit.com/catalog/brand/ripndip/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/sandals/brand/fila-woman/')) LocalRedirect('https://www.itkkit.com/catalog/brand/fila-woman/footwear/sandals/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shirts/brand/tommy-jeans/')) LocalRedirect('https://www.itkkit.com/catalog/brand/tommy-jeans/clothing/shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/nasaseasons/')) LocalRedirect('https://www.itkkit.com/catalog/brand/nasaseasons/clothing/hoodies/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/assid/')) LocalRedirect('https://www.itkkit.com/catalog/brand/assid/clothing/hoodies/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/alltimers/')) LocalRedirect('https://www.itkkit.com/catalog/brand/alltimers/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/grind-london/')) LocalRedirect('https://www.itkkit.com/catalog/brand/grind-london/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/civilist/')) LocalRedirect('https://www.itkkit.com/catalog/brand/civilist/clothing/sweats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/tommy-jeans/')) LocalRedirect('https://www.itkkit.com/catalog/brand/tommy-jeans/clothing/sweats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/sss-world-corp/')) LocalRedirect('https://www.itkkit.com/catalog/brand/sss-world-corp/clothing/longsleeves/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/rave-skateboards/')) LocalRedirect('https://www.itkkit.com/catalog/brand/rave-skateboards/accessories/bags/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/babylon-la/')) LocalRedirect('https://www.itkkit.com/catalog/brand/babylon-la/clothing/longsleeves/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/socks/brand/ripndip/')) LocalRedirect('https://www.itkkit.com/catalog/brand/ripndip/accessories/socks/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/tommy-jeans/')) LocalRedirect('https://www.itkkit.com/catalog/brand/tommy-jeans/clothing/outerwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/socks/brand/polythene-optics/')) LocalRedirect('https://www.itkkit.com/catalog/brand/polythene-optics/accessories/socks/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/resort-corps/')) LocalRedirect('https://www.itkkit.com/catalog/brand/resort-corps/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/sss-world-corp/')) LocalRedirect('https://www.itkkit.com/catalog/brand/sss-world-corp/clothing/sweats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/chinatown-market/')) LocalRedirect('https://www.itkkit.com/catalog/brand/chinatown-market/clothing/sweats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/bronze-56k/')) LocalRedirect('https://www.itkkit.com/catalog/brand/bronze-56k/clothing/longsleeves/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/billionaire-boys-club/')) LocalRedirect('https://www.itkkit.com/catalog/brand/billionaire-boys-club/clothing/hoodies/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shirts/brand/grind-london/')) LocalRedirect('https://www.itkkit.com/catalog/brand/grind-london/clothing/shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/wallets/brand/fjallraven/')) LocalRedirect('https://www.itkkit.com/catalog/brand/fjallraven/accessories/wallets/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/tommy-jeans/')) LocalRedirect('https://www.itkkit.com/catalog/brand/tommy-jeans/accessories/bags/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/chinatown-market/')) LocalRedirect('https://www.itkkit.com/catalog/brand/chinatown-market/accessories/bags/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/billionaire-boys-club/')) LocalRedirect('https://www.itkkit.com/catalog/brand/billionaire-boys-club/clothing/longsleeves/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/chinatown-market/')) LocalRedirect('https://www.itkkit.com/catalog/brand/chinatown-market/clothing/longsleeves/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shirts/brand/chinatown-market/')) LocalRedirect('https://www.itkkit.com/catalog/brand/chinatown-market/clothing/shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/still-good/')) LocalRedirect('https://www.itkkit.com/catalog/brand/still-good/clothing/hoodies/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/billionaire-boys-club/')) LocalRedirect('https://www.itkkit.com/catalog/brand/billionaire-boys-club/clothing/pants/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/call-me-917/')) LocalRedirect('https://www.itkkit.com/catalog/brand/call-me-917/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/ripndip/')) LocalRedirect('https://www.itkkit.com/catalog/brand/ripndip/clothing/longsleeves/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/junior-executive/')) LocalRedirect('https://www.itkkit.com/catalog/brand/junior-executive/clothing/sweats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/sneakers/brand/tommy-jeans-woman/')) LocalRedirect('https://www.itkkit.com/catalog/brand/tommy-jeans-woman/footwear/sneakers/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/gasius/')) LocalRedirect('https://www.itkkit.com/catalog/brand/gasius/clothing/longsleeves/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/tommy-jeans/')) LocalRedirect('https://www.itkkit.com/catalog/brand/tommy-jeans/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/rave-skateboards/')) LocalRedirect('https://www.itkkit.com/catalog/brand/rave-skateboards/clothing/outerwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/souvenirs/brand/h-las/')) LocalRedirect('https://www.itkkit.com/catalog/brand/h-las/accessories/souvenirs/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/polythene-optics/')) LocalRedirect('https://www.itkkit.com/catalog/brand/polythene-optics/accessories/bags/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/polythene-optics/')) LocalRedirect('https://www.itkkit.com/catalog/brand/polythene-optics/clothing/pants/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/souvenirs/brand/fjallraven/')) LocalRedirect('https://www.itkkit.com/catalog/brand/fjallraven/accessories/souvenirs/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/alpha-industries/')) LocalRedirect('https://www.itkkit.com/catalog/brand/alpha-industries/clothing/hoodies/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shirts/brand/sss-world-corp/')) LocalRedirect('https://www.itkkit.com/catalog/brand/sss-world-corp/clothing/shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/still-good/')) LocalRedirect('https://www.itkkit.com/catalog/brand/still-good/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/vests/brand/mki-miyuki-zoku/')) LocalRedirect('https://www.itkkit.com/catalog/brand/mki-miyuki-zoku/clothing/vests/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/fjallraven/')) LocalRedirect('https://www.itkkit.com/catalog/brand/fjallraven/accessories/bags/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/resort-corps/')) LocalRedirect('https://www.itkkit.com/catalog/brand/resort-corps/clothing/sweats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shirts/brand/resort-corps/')) LocalRedirect('https://www.itkkit.com/catalog/brand/resort-corps/clothing/shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/mki-miyuki-zoku/')) LocalRedirect('https://www.itkkit.com/catalog/brand/mki-miyuki-zoku/clothing/longsleeves/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/alpha-industries/')) LocalRedirect('https://www.itkkit.com/catalog/brand/alpha-industries/clothing/sweats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/polythene-optics/')) LocalRedirect('https://www.itkkit.com/catalog/brand/polythene-optics/clothing/hoodies/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/tommy-jeans/')) LocalRedirect('https://www.itkkit.com/catalog/brand/tommy-jeans/clothing/hoodies/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/bombers/brand/resort-corps/')) LocalRedirect('https://www.itkkit.com/catalog/brand/resort-corps/clothing/bombers/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/civilist/')) LocalRedirect('https://www.itkkit.com/catalog/brand/civilist/accessories/bags/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/platformx/')) LocalRedirect('https://www.itkkit.com/catalog/brand/platformx/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/chinatown-market/')) LocalRedirect('https://www.itkkit.com/catalog/brand/chinatown-market/clothing/hoodies/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/still-good/')) LocalRedirect('https://www.itkkit.com/catalog/brand/still-good/clothing/outerwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/sss-world-corp/')) LocalRedirect('https://www.itkkit.com/catalog/brand/sss-world-corp/clothing/pants/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/resort-corps/')) LocalRedirect('https://www.itkkit.com/catalog/brand/resort-corps/clothing/pants/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/sporadic/')) LocalRedirect('https://www.itkkit.com/catalog/brand/sporadic/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/bronze-56k/')) LocalRedirect('https://www.itkkit.com/catalog/brand/bronze-56k/clothing/hoodies/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/billionaire-boys-club/')) LocalRedirect('https://www.itkkit.com/catalog/brand/billionaire-boys-club/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shorts/brand/chinatown-market/')) LocalRedirect('https://www.itkkit.com/catalog/brand/chinatown-market/clothing/shorts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/mki-miyuki-zoku/')) LocalRedirect('https://www.itkkit.com/catalog/brand/mki-miyuki-zoku/clothing/pants/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/h-las/')) LocalRedirect('https://www.itkkit.com/catalog/brand/h-las/clothing/pants/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/gasius/')) LocalRedirect('https://www.itkkit.com/catalog/brand/gasius/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/civilist/')) LocalRedirect('https://www.itkkit.com/catalog/brand/civilist/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/reception/')) LocalRedirect('https://www.itkkit.com/catalog/brand/reception/clothing/longsleeves/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shorts/brand/tommy-jeans/')) LocalRedirect('https://www.itkkit.com/catalog/brand/tommy-jeans/clothing/shorts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/civilist/')) LocalRedirect('https://www.itkkit.com/catalog/brand/civilist/accessories/hats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/kappa/')) LocalRedirect('https://www.itkkit.com/catalog/brand/kappa/clothing/pants/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/babylon-la/')) LocalRedirect('https://www.itkkit.com/catalog/brand/babylon-la/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/platformx/')) LocalRedirect('https://www.itkkit.com/catalog/brand/platformx/clothing/sweats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/civilist/')) LocalRedirect('https://www.itkkit.com/catalog/brand/civilist/clothing/longsleeves/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/still-good/')) LocalRedirect('https://www.itkkit.com/catalog/brand/still-good/accessories/hats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/being-hunted/')) LocalRedirect('https://www.itkkit.com/catalog/brand/being-hunted/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/alltimers/')) LocalRedirect('https://www.itkkit.com/catalog/brand/alltimers/clothing/pants/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/alpha-industries/')) LocalRedirect('https://www.itkkit.com/catalog/brand/alpha-industries/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/sandals/brand/keen/')) LocalRedirect('https://www.itkkit.com/catalog/brand/keen/footwear/sandals/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/billionaire-boys-club/')) LocalRedirect('https://www.itkkit.com/catalog/brand/billionaire-boys-club/clothing/outerwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/rokit/')) LocalRedirect('https://www.itkkit.com/catalog/brand/rokit/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/liam-hodges/')) LocalRedirect('https://www.itkkit.com/catalog/brand/liam-hodges/clothing/hoodies/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/carrots/')) LocalRedirect('https://www.itkkit.com/catalog/brand/carrots/clothing/outerwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/l-i-e-s-records/')) LocalRedirect('https://www.itkkit.com/catalog/brand/l-i-e-s-records/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/socks/brand/mki-miyuki-zoku/')) LocalRedirect('https://www.itkkit.com/catalog/brand/mki-miyuki-zoku/accessories/socks/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/carrots/')) LocalRedirect('https://www.itkkit.com/catalog/brand/carrots/clothing/pants/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/ripndip/')) LocalRedirect('https://www.itkkit.com/catalog/brand/ripndip/clothing/pants/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/souvenirs/brand/rokit/')) LocalRedirect('https://www.itkkit.com/catalog/brand/rokit/accessories/souvenirs/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/carrots/')) LocalRedirect('https://www.itkkit.com/catalog/brand/carrots/clothing/longsleeves/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/souvenirs/brand/civilist/')) LocalRedirect('https://www.itkkit.com/catalog/brand/civilist/accessories/souvenirs/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/rave-skateboards/')) LocalRedirect('https://www.itkkit.com/catalog/brand/rave-skateboards/clothing/hoodies/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/know-wave/')) LocalRedirect('https://www.itkkit.com/catalog/brand/know-wave/clothing/hoodies/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/still-good/')) LocalRedirect('https://www.itkkit.com/catalog/brand/still-good/accessories/bags/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/sporadic/')) LocalRedirect('https://www.itkkit.com/catalog/brand/sporadic/clothing/longsleeves/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/knitwear/brand/chinatown-market/')) LocalRedirect('https://www.itkkit.com/catalog/brand/chinatown-market/clothing/knitwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/champion-woman/')) LocalRedirect('https://www.itkkit.com/catalog/brand/champion-woman/accessories/bags/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/h-las/')) LocalRedirect('https://www.itkkit.com/catalog/brand/h-las/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/good-morning-tapes/')) LocalRedirect('https://www.itkkit.com/catalog/brand/good-morning-tapes/clothing/longsleeves/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shirts/brand/life-s-a-beach/')) LocalRedirect('https://www.itkkit.com/catalog/brand/life-s-a-beach/clothing/shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/grind-london/')) LocalRedirect('https://www.itkkit.com/catalog/brand/grind-london/clothing/hoodies/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/sneakers/brand/premiata/')) LocalRedirect('https://www.itkkit.com/catalog/brand/premiata/footwear/sneakers/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/olaf-hussein/')) LocalRedirect('https://www.itkkit.com/catalog/brand/olaf-hussein/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/know-wave/')) LocalRedirect('https://www.itkkit.com/catalog/brand/know-wave/clothing/longsleeves/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/bombers/brand/liam-hodges/')) LocalRedirect('https://www.itkkit.com/catalog/brand/liam-hodges/clothing/bombers/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/polythene-optics/')) LocalRedirect('https://www.itkkit.com/catalog/brand/polythene-optics/accessories/hats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/arte-antwerp/')) LocalRedirect('https://www.itkkit.com/catalog/brand/arte-antwerp/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/nigel-cabourn/')) LocalRedirect('https://www.itkkit.com/catalog/brand/nigel-cabourn/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/champion-reverse-weave/')) LocalRedirect('https://www.itkkit.com/catalog/brand/champion-reverse-weave/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/comme-des-gar-ons-play/')) LocalRedirect('https://www.itkkit.com/catalog/brand/comme-des-gar-ons-play/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/comme-des-garcons-parfum/')) LocalRedirect('https://www.itkkit.com/catalog/brand/comme-des-garcons-parfum/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/needles/')) LocalRedirect('https://www.itkkit.com/catalog/brand/needles/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/palace-skateboards/')) LocalRedirect('https://www.itkkit.com/catalog/brand/palace-skateboards/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/universal-works/')) LocalRedirect('https://www.itkkit.com/catalog/brand/universal-works/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/pleasures/')) LocalRedirect('https://www.itkkit.com/catalog/brand/pleasures/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/dr-le-de-monsieur/')) LocalRedirect('https://www.itkkit.com/catalog/brand/dr-le-de-monsieur/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/brand/comme-des-gar-ons-play/')) LocalRedirect('https://www.itkkit.com/catalog/brand/comme-des-gar-ons-play/footwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/barbour/')) LocalRedirect('https://www.itkkit.com/catalog/brand/barbour/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/the-trilogy-tapes/')) LocalRedirect('https://www.itkkit.com/catalog/brand/the-trilogy-tapes/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/maharishi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/maharishi/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/rassvet/')) LocalRedirect('https://www.itkkit.com/catalog/brand/rassvet/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/idea/')) LocalRedirect('https://www.itkkit.com/catalog/brand/idea/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/comme-des-gar-ons-shirt/')) LocalRedirect('https://www.itkkit.com/catalog/brand/comme-des-gar-ons-shirt/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/aries/')) LocalRedirect('https://www.itkkit.com/catalog/brand/aries/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/fucking-awesome/')) LocalRedirect('https://www.itkkit.com/catalog/brand/fucking-awesome/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/carhartt-wip/')) LocalRedirect('https://www.itkkit.com/catalog/brand/carhartt-wip/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/brand/the-north-face/')) LocalRedirect('https://www.itkkit.com/catalog/brand/the-north-face/footwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/polar-skate-co/')) LocalRedirect('https://www.itkkit.com/catalog/brand/polar-skate-co/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/brand/pleasures/')) LocalRedirect('https://www.itkkit.com/catalog/brand/pleasures/footwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/norse-projects/')) LocalRedirect('https://www.itkkit.com/catalog/brand/norse-projects/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/human-with-attitude/')) LocalRedirect('https://www.itkkit.com/catalog/brand/human-with-attitude/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/baxter-of-california/')) LocalRedirect('https://www.itkkit.com/catalog/brand/baxter-of-california/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/brand/jason-markk/')) LocalRedirect('https://www.itkkit.com/catalog/brand/jason-markk/footwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/comme-des-gar-ons-wallets/')) LocalRedirect('https://www.itkkit.com/catalog/brand/comme-des-gar-ons-wallets/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/carne-bollente/')) LocalRedirect('https://www.itkkit.com/catalog/brand/carne-bollente/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/engineered-garments/')) LocalRedirect('https://www.itkkit.com/catalog/brand/engineered-garments/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/brand/hoka-one-one/')) LocalRedirect('https://www.itkkit.com/catalog/brand/hoka-one-one/footwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/aries/')) LocalRedirect('https://www.itkkit.com/catalog/brand/aries/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/aded/')) LocalRedirect('https://www.itkkit.com/catalog/brand/aded/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/patta/')) LocalRedirect('https://www.itkkit.com/catalog/brand/patta/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/brand/clarks-originals/')) LocalRedirect('https://www.itkkit.com/catalog/brand/clarks-originals/footwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/carhartt-wip/')) LocalRedirect('https://www.itkkit.com/catalog/brand/carhartt-wip/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/brand/barbour/')) LocalRedirect('https://www.itkkit.com/catalog/brand/barbour/footwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/daily-paper/')) LocalRedirect('https://www.itkkit.com/catalog/brand/daily-paper/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/brand/suicoke/')) LocalRedirect('https://www.itkkit.com/catalog/brand/suicoke/footwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/idea/')) LocalRedirect('https://www.itkkit.com/catalog/brand/idea/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/stussy/')) LocalRedirect('https://www.itkkit.com/catalog/brand/stussy/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/stussy/')) LocalRedirect('https://www.itkkit.com/catalog/brand/stussy/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/brand/birkenstock/')) LocalRedirect('https://www.itkkit.com/catalog/brand/birkenstock/footwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/pop-trading-company/')) LocalRedirect('https://www.itkkit.com/catalog/brand/pop-trading-company/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/marvis/')) LocalRedirect('https://www.itkkit.com/catalog/brand/marvis/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/engineered-garments/')) LocalRedirect('https://www.itkkit.com/catalog/brand/engineered-garments/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/pleasures/')) LocalRedirect('https://www.itkkit.com/catalog/brand/pleasures/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/p-a-m/')) LocalRedirect('https://www.itkkit.com/catalog/brand/p-a-m/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/brand/new-balance/')) LocalRedirect('https://www.itkkit.com/catalog/brand/new-balance/footwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/maharishi/')) LocalRedirect('https://www.itkkit.com/catalog/brand/maharishi/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/dr-le-de-monsieur/')) LocalRedirect('https://www.itkkit.com/catalog/brand/dr-le-de-monsieur/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/p-a-m/')) LocalRedirect('https://www.itkkit.com/catalog/brand/p-a-m/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/patta/')) LocalRedirect('https://www.itkkit.com/catalog/brand/patta/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/fucking-awesome/')) LocalRedirect('https://www.itkkit.com/catalog/brand/fucking-awesome/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/south-devon-chilli-farm/')) LocalRedirect('https://www.itkkit.com/catalog/brand/south-devon-chilli-farm/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/champion-reverse-weave/')) LocalRedirect('https://www.itkkit.com/catalog/brand/champion-reverse-weave/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/m-rc-noir/')) LocalRedirect('https://www.itkkit.com/catalog/brand/m-rc-noir/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/brand/rassvet/')) LocalRedirect('https://www.itkkit.com/catalog/brand/rassvet/footwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/polar-skate-co/')) LocalRedirect('https://www.itkkit.com/catalog/brand/polar-skate-co/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/patagonia/')) LocalRedirect('https://www.itkkit.com/catalog/brand/patagonia/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/rassvet/')) LocalRedirect('https://www.itkkit.com/catalog/brand/rassvet/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/m-rc-noir/')) LocalRedirect('https://www.itkkit.com/catalog/brand/m-rc-noir/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/casablanca/')) LocalRedirect('https://www.itkkit.com/catalog/brand/casablanca/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/brand/diemme/')) LocalRedirect('https://www.itkkit.com/catalog/brand/diemme/footwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/pop-trading-company/')) LocalRedirect('https://www.itkkit.com/catalog/brand/pop-trading-company/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/norse-projects/')) LocalRedirect('https://www.itkkit.com/catalog/brand/norse-projects/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/the-north-face/')) LocalRedirect('https://www.itkkit.com/catalog/brand/the-north-face/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/carne-bollente/')) LocalRedirect('https://www.itkkit.com/catalog/brand/carne-bollente/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/patagonia/')) LocalRedirect('https://www.itkkit.com/catalog/brand/patagonia/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/human-with-attitude/')) LocalRedirect('https://www.itkkit.com/catalog/brand/human-with-attitude/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/the-north-face/')) LocalRedirect('https://www.itkkit.com/catalog/brand/the-north-face/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/barbour/')) LocalRedirect('https://www.itkkit.com/catalog/brand/barbour/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/daily-paper/')) LocalRedirect('https://www.itkkit.com/catalog/brand/daily-paper/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/brand/mephisto/')) LocalRedirect('https://www.itkkit.com/catalog/brand/mephisto/footwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/being-hunted/')) LocalRedirect('https://www.itkkit.com/catalog/brand/being-hunted/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/mki-miyuki-zoku/')) LocalRedirect('https://www.itkkit.com/catalog/brand/mki-miyuki-zoku/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/brand/tommy-jeans/')) LocalRedirect('https://www.itkkit.com/catalog/brand/tommy-jeans/footwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/tommy-jeans/')) LocalRedirect('https://www.itkkit.com/catalog/brand/tommy-jeans/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/call-me-917/')) LocalRedirect('https://www.itkkit.com/catalog/brand/call-me-917/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/liam-hodges/')) LocalRedirect('https://www.itkkit.com/catalog/brand/liam-hodges/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/sss-world-corp/')) LocalRedirect('https://www.itkkit.com/catalog/brand/sss-world-corp/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/civilist/')) LocalRedirect('https://www.itkkit.com/catalog/brand/civilist/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/resort-corps/')) LocalRedirect('https://www.itkkit.com/catalog/brand/resort-corps/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/rokit/')) LocalRedirect('https://www.itkkit.com/catalog/brand/rokit/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/brand/fila-woman/')) LocalRedirect('https://www.itkkit.com/catalog/brand/fila-woman/footwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/brand/premiata/')) LocalRedirect('https://www.itkkit.com/catalog/brand/premiata/footwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/champion-woman/')) LocalRedirect('https://www.itkkit.com/catalog/brand/champion-woman/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/life-s-a-beach/')) LocalRedirect('https://www.itkkit.com/catalog/brand/life-s-a-beach/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/junior-executive/')) LocalRedirect('https://www.itkkit.com/catalog/brand/junior-executive/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/brand/keen/')) LocalRedirect('https://www.itkkit.com/catalog/brand/keen/footwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/tommy-jeans-woman/')) LocalRedirect('https://www.itkkit.com/catalog/brand/tommy-jeans-woman/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/olaf-hussein/')) LocalRedirect('https://www.itkkit.com/catalog/brand/olaf-hussein/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/carrots/')) LocalRedirect('https://www.itkkit.com/catalog/brand/carrots/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/taikan/')) LocalRedirect('https://www.itkkit.com/catalog/brand/taikan/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/babylon-la/')) LocalRedirect('https://www.itkkit.com/catalog/brand/babylon-la/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/kappa/')) LocalRedirect('https://www.itkkit.com/catalog/brand/kappa/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/grind-london/')) LocalRedirect('https://www.itkkit.com/catalog/brand/grind-london/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/kappa/')) LocalRedirect('https://www.itkkit.com/catalog/brand/kappa/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/sun-buddies/')) LocalRedirect('https://www.itkkit.com/catalog/brand/sun-buddies/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/h-las/')) LocalRedirect('https://www.itkkit.com/catalog/brand/h-las/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/bronze-56k/')) LocalRedirect('https://www.itkkit.com/catalog/brand/bronze-56k/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/know-wave/')) LocalRedirect('https://www.itkkit.com/catalog/brand/know-wave/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/sergio-tacchini/')) LocalRedirect('https://www.itkkit.com/catalog/brand/sergio-tacchini/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/nasaseasons/')) LocalRedirect('https://www.itkkit.com/catalog/brand/nasaseasons/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/babylon-la/')) LocalRedirect('https://www.itkkit.com/catalog/brand/babylon-la/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/assid/')) LocalRedirect('https://www.itkkit.com/catalog/brand/assid/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/brand/fila/')) LocalRedirect('https://www.itkkit.com/catalog/brand/fila/footwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/h-las/')) LocalRedirect('https://www.itkkit.com/catalog/brand/h-las/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/reception/')) LocalRedirect('https://www.itkkit.com/catalog/brand/reception/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/alltimers/')) LocalRedirect('https://www.itkkit.com/catalog/brand/alltimers/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/brand/tommy-jeans-woman/')) LocalRedirect('https://www.itkkit.com/catalog/brand/tommy-jeans-woman/footwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/rave-skateboards/')) LocalRedirect('https://www.itkkit.com/catalog/brand/rave-skateboards/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/civilist/')) LocalRedirect('https://www.itkkit.com/catalog/brand/civilist/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/tommy-jeans-woman/')) LocalRedirect('https://www.itkkit.com/catalog/brand/tommy-jeans-woman/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/ripndip/')) LocalRedirect('https://www.itkkit.com/catalog/brand/ripndip/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/primus/')) LocalRedirect('https://www.itkkit.com/catalog/brand/primus/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/fjallraven/')) LocalRedirect('https://www.itkkit.com/catalog/brand/fjallraven/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/lack-of-guidance/')) LocalRedirect('https://www.itkkit.com/catalog/brand/lack-of-guidance/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/ripndip/')) LocalRedirect('https://www.itkkit.com/catalog/brand/ripndip/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/l-i-e-s-records/')) LocalRedirect('https://www.itkkit.com/catalog/brand/l-i-e-s-records/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/still-good/')) LocalRedirect('https://www.itkkit.com/catalog/brand/still-good/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/alltimers/')) LocalRedirect('https://www.itkkit.com/catalog/brand/alltimers/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/sporadic/')) LocalRedirect('https://www.itkkit.com/catalog/brand/sporadic/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/rave-skateboards/')) LocalRedirect('https://www.itkkit.com/catalog/brand/rave-skateboards/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/platformx/')) LocalRedirect('https://www.itkkit.com/catalog/brand/platformx/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/still-good/')) LocalRedirect('https://www.itkkit.com/catalog/brand/still-good/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/chinatown-market/')) LocalRedirect('https://www.itkkit.com/catalog/brand/chinatown-market/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/bronze-56k/')) LocalRedirect('https://www.itkkit.com/catalog/brand/bronze-56k/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/alpha-industries/')) LocalRedirect('https://www.itkkit.com/catalog/brand/alpha-industries/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/champion-woman/')) LocalRedirect('https://www.itkkit.com/catalog/brand/champion-woman/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/good-morning-tapes/')) LocalRedirect('https://www.itkkit.com/catalog/brand/good-morning-tapes/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/nasaseasons/')) LocalRedirect('https://www.itkkit.com/catalog/brand/nasaseasons/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/carrots/')) LocalRedirect('https://www.itkkit.com/catalog/brand/carrots/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/mki-miyuki-zoku/')) LocalRedirect('https://www.itkkit.com/catalog/brand/mki-miyuki-zoku/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/sporadic/')) LocalRedirect('https://www.itkkit.com/catalog/brand/sporadic/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/gasius/')) LocalRedirect('https://www.itkkit.com/catalog/brand/gasius/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/rokit/')) LocalRedirect('https://www.itkkit.com/catalog/brand/rokit/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/chinatown-market/')) LocalRedirect('https://www.itkkit.com/catalog/brand/chinatown-market/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/tommy-jeans/')) LocalRedirect('https://www.itkkit.com/catalog/brand/tommy-jeans/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/polythene-optics/')) LocalRedirect('https://www.itkkit.com/catalog/brand/polythene-optics/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/brand/adidas-originals/')) LocalRedirect('https://www.itkkit.com/catalog/brand/adidas-originals/footwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/polythene-optics/')) LocalRedirect('https://www.itkkit.com/catalog/brand/polythene-optics/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/billionaire-boys-club/')) LocalRedirect('https://www.itkkit.com/catalog/brand/billionaire-boys-club/clothing/', false, '301 Moved permanently');
} else {
	if (CSite::InDir('/catalog/clothing/pants/brand/universal-works/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/universal-works/clothing/pants/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/universal-works/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/universal-works/clothing/outerwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/universal-works/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/universal-works/clothing/sweats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/knitwear/brand/universal-works/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/universal-works/clothing/knitwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/tommy-jeans-woman/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/tommy-jeans-woman/accessories/bags/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/jeans/brand/tommy-jeans-woman/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/tommy-jeans-woman/clothing/jeans/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/tommy-jeans-woman/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/tommy-jeans-woman/clothing/longsleeves/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/sneakers/brand/tommy-jeans-woman/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/tommy-jeans-woman/footwear/sneakers/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/tommy-jeans/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/tommy-jeans/accessories/bags/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/tommy-jeans/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/tommy-jeans/accessories/hats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/tommy-jeans/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/tommy-jeans/clothing/sweats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/tommy-jeans/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/tommy-jeans/clothing/hoodies/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/tommy-jeans/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/tommy-jeans/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/tommy-jeans/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/tommy-jeans/clothing/outerwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shorts/brand/tommy-jeans/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/tommy-jeans/clothing/shorts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shirts/brand/tommy-jeans/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/tommy-jeans/clothing/shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/sneakers/brand/tommy-jeans/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/tommy-jeans/footwear/sneakers/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/the-trilogy-tapes/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/the-trilogy-tapes/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/the-trilogy-tapes/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/the-trilogy-tapes/clothing/longsleeves/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/the-north-face/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/the-north-face/accessories/hats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/gloves/brand/the-north-face/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/the-north-face/accessories/gloves/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/the-north-face/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/the-north-face/accessories/bags/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/the-north-face/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/the-north-face/clothing/outerwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/sandals/brand/the-north-face/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/the-north-face/footwear/sandals/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/taikan/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/taikan/accessories/bags/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/sunglasses/brand/sun-buddies/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/sun-buddies/accessories/sunglasses/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/shoes/brand/suicoke/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/suicoke/footwear/shoes/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/sandals/brand/suicoke/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/suicoke/footwear/sandals/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/stussy/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/stussy/accessories/hats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/stussy/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/stussy/accessories/bags/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/souvenirs/brand/stussy/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/stussy/accessories/souvenirs/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/belts/brand/stussy/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/stussy/accessories/belts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/stussy/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/stussy/clothing/pants/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shorts/brand/stussy/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/stussy/clothing/shorts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/stussy/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/stussy/clothing/hoodies/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/stussy/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/stussy/clothing/sweats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shirts/brand/stussy/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/stussy/clothing/shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/stussy/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/stussy/clothing/longsleeves/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/stussy/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/stussy/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/stussy/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/stussy/clothing/outerwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/knitwear/brand/stussy/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/stussy/clothing/knitwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/socks/brand/still-good/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/still-good/accessories/socks/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/still-good/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/still-good/accessories/bags/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/still-good/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/still-good/accessories/hats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/still-good/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/still-good/clothing/hoodies/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/still-good/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/still-good/clothing/longsleeves/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/still-good/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/still-good/clothing/outerwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/still-good/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/still-good/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/sss-world-corp/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/sss-world-corp/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/sss-world-corp/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/sss-world-corp/clothing/hoodies/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/knitwear/brand/sss-world-corp/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/sss-world-corp/clothing/knitwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/sss-world-corp/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/sss-world-corp/clothing/pants/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/sss-world-corp/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/sss-world-corp/clothing/longsleeves/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/sss-world-corp/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/sss-world-corp/clothing/sweats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shirts/brand/sss-world-corp/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/sss-world-corp/clothing/shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/sss-world-corp/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/sss-world-corp/clothing/outerwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/souvenirs/brand/sporadic/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/sporadic/accessories/souvenirs/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/fragrances/brand/sporadic/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/sporadic/accessories/fragrances/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/sporadic/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/sporadic/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/sporadic/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/sporadic/clothing/outerwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/sporadic/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/sporadic/clothing/longsleeves/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hot-stuff/brand/south-devon-chilli-farm/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/south-devon-chilli-farm/accessories/hot-stuff/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/sergio-tacchini/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/sergio-tacchini/clothing/pants/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/sergio-tacchini/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/sergio-tacchini/clothing/sweats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/souvenirs/brand/rokit/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/rokit/accessories/souvenirs/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/rokit/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/rokit/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/rokit/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/rokit/clothing/longsleeves/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/rokit/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/rokit/clothing/pants/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/socks/brand/ripndip/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/ripndip/accessories/socks/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/ripndip/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/ripndip/clothing/hoodies/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/ripndip/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/ripndip/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/ripndip/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/ripndip/clothing/outerwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/ripndip/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/ripndip/clothing/longsleeves/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/bombers/brand/resort-corps/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/resort-corps/clothing/bombers/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/resort-corps/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/resort-corps/clothing/sweats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shirts/brand/resort-corps/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/resort-corps/clothing/shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/resort-corps/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/resort-corps/clothing/hoodies/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/resort-corps/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/resort-corps/clothing/pants/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/resort-corps/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/resort-corps/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/reception/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/reception/clothing/longsleeves/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/rave-skateboards/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/rave-skateboards/accessories/bags/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/rave-skateboards/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/rave-skateboards/accessories/hats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/rave-skateboards/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/rave-skateboards/clothing/hoodies/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/rave-skateboards/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/rave-skateboards/clothing/pants/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/rave-skateboards/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/rave-skateboards/clothing/outerwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/vests/brand/rave-skateboards/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/rave-skateboards/clothing/vests/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/rassvet/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/rassvet/accessories/bags/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/socks/brand/rassvet/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/rassvet/accessories/socks/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/skateboarding/brand/rassvet/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/rassvet/accessories/skateboarding/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/rassvet/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/rassvet/accessories/hats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shirts/brand/rassvet/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/rassvet/clothing/shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/rassvet/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/rassvet/clothing/sweats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/rassvet/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/rassvet/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/rassvet/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/rassvet/clothing/outerwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/rassvet/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/rassvet/clothing/pants/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/polo/brand/rassvet/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/rassvet/clothing/polo/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/knitwear/brand/rassvet/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/rassvet/clothing/knitwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/sneakers/brand/rassvet/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/rassvet/footwear/sneakers/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/souvenirs/brand/primus/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/primus/accessories/souvenirs/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/sneakers/brand/premiata/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/premiata/footwear/sneakers/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/pop-trading-company/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/pop-trading-company/accessories/bags/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/pop-trading-company/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/pop-trading-company/accessories/hats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/pop-trading-company/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/pop-trading-company/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/pop-trading-company/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/pop-trading-company/clothing/outerwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/pop-trading-company/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/pop-trading-company/clothing/pants/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/pop-trading-company/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/pop-trading-company/clothing/longsleeves/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/vests/brand/pop-trading-company/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/pop-trading-company/clothing/vests/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/pop-trading-company/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/pop-trading-company/clothing/sweats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/jeans/brand/pop-trading-company/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/pop-trading-company/clothing/jeans/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/souvenirs/brand/polythene-optics/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/polythene-optics/accessories/souvenirs/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/socks/brand/polythene-optics/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/polythene-optics/accessories/socks/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/polythene-optics/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/polythene-optics/accessories/hats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/polythene-optics/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/polythene-optics/clothing/hoodies/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/polythene-optics/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/polythene-optics/clothing/outerwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/polythene-optics/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/polythene-optics/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/polythene-optics/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/polythene-optics/clothing/pants/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shirts/brand/polythene-optics/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/polythene-optics/clothing/shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/polythene-optics/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/polythene-optics/clothing/sweats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/souvenirs/brand/polar-skate-co/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/polar-skate-co/accessories/souvenirs/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/polar-skate-co/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/polar-skate-co/accessories/hats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/polar-skate-co/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/polar-skate-co/accessories/bags/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/jeans/brand/polar-skate-co/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/polar-skate-co/clothing/jeans/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/polar-skate-co/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/polar-skate-co/clothing/longsleeves/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shirts/brand/polar-skate-co/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/polar-skate-co/clothing/shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/polar-skate-co/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/polar-skate-co/clothing/outerwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/knitwear/brand/polar-skate-co/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/polar-skate-co/clothing/knitwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/polar-skate-co/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/polar-skate-co/clothing/pants/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/polar-skate-co/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/polar-skate-co/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/polar-skate-co/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/polar-skate-co/clothing/hoodies/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/pleasures/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/pleasures/accessories/bags/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/pleasures/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/pleasures/accessories/hats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/pleasures/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/pleasures/clothing/longsleeves/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/pleasures/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/pleasures/clothing/pants/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/pleasures/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/pleasures/clothing/outerwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/pleasures/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/pleasures/clothing/hoodies/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/polo/brand/pleasures/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/pleasures/clothing/polo/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shirts/brand/pleasures/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/pleasures/clothing/shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/knitwear/brand/pleasures/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/pleasures/clothing/knitwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/pleasures/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/pleasures/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/sandals/brand/pleasures/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/pleasures/footwear/sandals/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/platformx/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/platformx/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/platformx/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/platformx/clothing/hoodies/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/platformx/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/platformx/clothing/sweats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/patta/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/patta/accessories/bags/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/patta/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/patta/clothing/longsleeves/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/patta/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/patta/clothing/pants/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/patta/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/patta/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/patta/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/patta/clothing/hoodies/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/patagonia/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/patagonia/accessories/hats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/patagonia/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/patagonia/accessories/bags/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/patagonia/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/patagonia/clothing/longsleeves/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/vests/brand/patagonia/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/patagonia/clothing/vests/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/patagonia/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/patagonia/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/patagonia/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/patagonia/clothing/hoodies/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/p-a-m/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/p-a-m/accessories/hats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/p-a-m/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/p-a-m/clothing/longsleeves/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/bombers/brand/p-a-m/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/p-a-m/clothing/bombers/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/p-a-m/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/p-a-m/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/p-a-m/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/p-a-m/clothing/outerwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/p-a-m/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/p-a-m/clothing/pants/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/knitwear/brand/p-a-m/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/p-a-m/clothing/knitwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/skateboarding/brand/palace-skateboards/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/palace-skateboards/accessories/skateboarding/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/olaf-hussein/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/olaf-hussein/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/olaf-hussein/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/olaf-hussein/clothing/hoodies/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shirts/brand/olaf-hussein/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/olaf-hussein/clothing/shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/olaf-hussein/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/olaf-hussein/clothing/longsleeves/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shirts/brand/norse-projects/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/norse-projects/clothing/shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/norse-projects/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/norse-projects/clothing/pants/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/knitwear/brand/norse-projects/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/norse-projects/clothing/knitwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/nigel-cabourn/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/nigel-cabourn/accessories/hats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/jeans/brand/nigel-cabourn/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/nigel-cabourn/clothing/jeans/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/nigel-cabourn/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/nigel-cabourn/clothing/outerwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/nigel-cabourn/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/nigel-cabourn/clothing/pants/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/knitwear/brand/nigel-cabourn/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/nigel-cabourn/clothing/knitwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shirts/brand/nigel-cabourn/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/nigel-cabourn/clothing/shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/sneakers/brand/new-balance/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/new-balance/footwear/sneakers/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/needles/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/needles/clothing/pants/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/knitwear/brand/needles/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/needles/clothing/knitwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/needles/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/needles/clothing/sweats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shirts/brand/needles/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/needles/clothing/shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/jeans/brand/needles/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/needles/clothing/jeans/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/needles/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/needles/clothing/hoodies/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/nasaseasons/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/nasaseasons/accessories/hats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/nasaseasons/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/nasaseasons/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/nasaseasons/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/nasaseasons/clothing/hoodies/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/m-rc-noir/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/m-rc-noir/accessories/bags/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/m-rc-noir/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/m-rc-noir/accessories/hats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/m-rc-noir/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/m-rc-noir/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/m-rc-noir/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/m-rc-noir/clothing/pants/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/m-rc-noir/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/m-rc-noir/clothing/outerwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/m-rc-noir/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/m-rc-noir/clothing/hoodies/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/parkas/brand/m-rc-noir/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/m-rc-noir/clothing/parkas/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shorts/brand/m-rc-noir/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/m-rc-noir/clothing/shorts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/socks/brand/mki-miyuki-zoku/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/mki-miyuki-zoku/accessories/socks/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/vests/brand/mki-miyuki-zoku/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/mki-miyuki-zoku/clothing/vests/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/mki-miyuki-zoku/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/mki-miyuki-zoku/clothing/pants/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/mki-miyuki-zoku/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/mki-miyuki-zoku/clothing/longsleeves/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/shoes/brand/mephisto/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/mephisto/footwear/shoes/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/grooming/brand/marvis/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/marvis/accessories/grooming/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/maharishi/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/maharishi/accessories/bags/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/souvenirs/brand/maharishi/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/maharishi/accessories/souvenirs/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shirts/brand/maharishi/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/maharishi/clothing/shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/maharishi/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/maharishi/clothing/pants/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/vests/brand/maharishi/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/maharishi/clothing/vests/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/maharishi/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/maharishi/clothing/longsleeves/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shirts/brand/life-s-a-beach/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/life-s-a-beach/clothing/shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/l-i-e-s-records/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/l-i-e-s-records/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/liam-hodges/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/liam-hodges/clothing/hoodies/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/liam-hodges/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/liam-hodges/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/bombers/brand/liam-hodges/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/liam-hodges/clothing/bombers/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/lack-of-guidance/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/lack-of-guidance/clothing/longsleeves/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/lack-of-guidance/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/lack-of-guidance/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/know-wave/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/know-wave/clothing/hoodies/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/know-wave/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/know-wave/clothing/longsleeves/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/know-wave/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/know-wave/clothing/sweats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/know-wave/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/know-wave/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/sandals/brand/keen/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/keen/footwear/sandals/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/kappa/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/kappa/accessories/bags/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/kappa/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/kappa/clothing/sweats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/kappa/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/kappa/clothing/pants/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/junior-executive/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/junior-executive/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/junior-executive/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/junior-executive/clothing/sweats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/shoe-care/brand/jason-markk/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/jason-markk/footwear/shoe-care/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/idea/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/idea/accessories/bags/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/pechatnye-izdaniya/brand/idea/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/idea/accessories/pechatnye-izdaniya/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/idea/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/idea/accessories/hats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/idea/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/idea/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/human-with-attitude/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/human-with-attitude/accessories/bags/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/vests/brand/human-with-attitude/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/human-with-attitude/clothing/vests/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/human-with-attitude/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/human-with-attitude/clothing/hoodies/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/human-with-attitude/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/human-with-attitude/clothing/pants/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/human-with-attitude/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/human-with-attitude/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/human-with-attitude/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/human-with-attitude/clothing/longsleeves/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/human-with-attitude/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/human-with-attitude/clothing/outerwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/sneakers/brand/hoka-one-one/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/hoka-one-one/footwear/sneakers/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/h-las/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/h-las/accessories/hats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/souvenirs/brand/h-las/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/h-las/accessories/souvenirs/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/vests/brand/h-las/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/h-las/clothing/vests/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/h-las/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/h-las/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/h-las/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/h-las/clothing/sweats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/polo/brand/h-las/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/h-las/clothing/polo/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/h-las/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/h-las/clothing/hoodies/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/h-las/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/h-las/clothing/longsleeves/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shorts/brand/h-las/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/h-las/clothing/shorts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/h-las/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/h-las/clothing/pants/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/grind-london/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/grind-london/accessories/hats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/grind-london/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/grind-london/clothing/hoodies/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/grind-london/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/grind-london/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shirts/brand/grind-london/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/grind-london/clothing/shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/good-morning-tapes/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/good-morning-tapes/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/good-morning-tapes/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/good-morning-tapes/clothing/longsleeves/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/gasius/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/gasius/clothing/longsleeves/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/gasius/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/gasius/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/vests/brand/gasius/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/gasius/clothing/vests/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/fucking-awesome/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/fucking-awesome/accessories/hats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/souvenirs/brand/fucking-awesome/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/fucking-awesome/accessories/souvenirs/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/pechatnye-izdaniya/brand/fucking-awesome/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/fucking-awesome/accessories/pechatnye-izdaniya/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/knitwear/brand/fucking-awesome/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/fucking-awesome/clothing/knitwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/fucking-awesome/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/fucking-awesome/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/fucking-awesome/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/fucking-awesome/clothing/sweats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/fucking-awesome/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/fucking-awesome/clothing/longsleeves/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/fucking-awesome/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/fucking-awesome/clothing/outerwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shirts/brand/fucking-awesome/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/fucking-awesome/clothing/shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/fucking-awesome/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/fucking-awesome/clothing/hoodies/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/wallets/brand/fjallraven/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/fjallraven/accessories/wallets/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/fjallraven/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/fjallraven/accessories/hats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/fjallraven/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/fjallraven/accessories/bags/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/souvenirs/brand/fjallraven/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/fjallraven/accessories/souvenirs/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/sandals/brand/fila-woman/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/fila-woman/footwear/sandals/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/sneakers/brand/fila-woman/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/fila-woman/footwear/sneakers/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/sneakers/brand/fila/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/fila/footwear/sneakers/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/engineered-garments/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/engineered-garments/accessories/bags/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/engineered-garments/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/engineered-garments/clothing/pants/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/vests/brand/engineered-garments/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/engineered-garments/clothing/vests/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shirts/brand/engineered-garments/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/engineered-garments/clothing/shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/dr-le-de-monsieur/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/dr-le-de-monsieur/accessories/hats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/dr-le-de-monsieur/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/dr-le-de-monsieur/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/dr-le-de-monsieur/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/dr-le-de-monsieur/clothing/outerwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/dr-le-de-monsieur/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/dr-le-de-monsieur/clothing/hoodies/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/dr-le-de-monsieur/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/dr-le-de-monsieur/clothing/sweats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/dr-le-de-monsieur/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/dr-le-de-monsieur/clothing/pants/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/shoes/brand/diemme/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/diemme/footwear/shoes/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/daily-paper/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/daily-paper/accessories/hats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/daily-paper/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/daily-paper/accessories/bags/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/daily-paper/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/daily-paper/clothing/hoodies/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/vests/brand/daily-paper/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/daily-paper/clothing/vests/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shirts/brand/daily-paper/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/daily-paper/clothing/shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/daily-paper/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/daily-paper/clothing/outerwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/daily-paper/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/daily-paper/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/daily-paper/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/daily-paper/clothing/sweats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/daily-paper/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/daily-paper/clothing/pants/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shorts/brand/daily-paper/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/daily-paper/clothing/shorts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/daily-paper/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/daily-paper/clothing/longsleeves/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/sneakers/brand/converse/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/converse/footwear/sneakers/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/comme-des-gar-ons-wallets/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/comme-des-gar-ons-wallets/accessories/bags/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/wallets/brand/comme-des-gar-ons-wallets/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/comme-des-gar-ons-wallets/accessories/wallets/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/belts/brand/comme-des-gar-ons-wallets/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/comme-des-gar-ons-wallets/accessories/belts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shirts/brand/comme-des-gar-ons-shirt/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/comme-des-gar-ons-shirt/clothing/shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/comme-des-gar-ons-shirt/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/comme-des-gar-ons-shirt/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/comme-des-gar-ons-shirt/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/comme-des-gar-ons-shirt/clothing/longsleeves/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/comme-des-gar-ons-play/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/comme-des-gar-ons-play/clothing/longsleeves/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shirts/brand/comme-des-gar-ons-play/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/comme-des-gar-ons-play/clothing/shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/comme-des-gar-ons-play/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/comme-des-gar-ons-play/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/comme-des-gar-ons-play/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/comme-des-gar-ons-play/clothing/hoodies/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/sneakers/brand/comme-des-gar-ons-play/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/comme-des-gar-ons-play/footwear/sneakers/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/fragrances/brand/comme-des-garcons-parfum/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/comme-des-garcons-parfum/accessories/fragrances/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/shoes/brand/clarks-originals/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/clarks-originals/footwear/shoes/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/civilist/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/civilist/accessories/bags/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/wallets/brand/civilist/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/civilist/accessories/wallets/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/souvenirs/brand/civilist/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/civilist/accessories/souvenirs/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/civilist/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/civilist/accessories/hats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/socks/brand/civilist/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/civilist/accessories/socks/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/civilist/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/civilist/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/civilist/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/civilist/clothing/longsleeves/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/civilist/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/civilist/clothing/hoodies/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/civilist/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/civilist/clothing/sweats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/souvenirs/brand/chinatown-market/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/chinatown-market/accessories/souvenirs/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/chinatown-market/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/chinatown-market/accessories/bags/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/chinatown-market/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/chinatown-market/clothing/hoodies/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shorts/brand/chinatown-market/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/chinatown-market/clothing/shorts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/knitwear/brand/chinatown-market/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/chinatown-market/clothing/knitwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/chinatown-market/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/chinatown-market/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/chinatown-market/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/chinatown-market/clothing/sweats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/chinatown-market/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/chinatown-market/clothing/longsleeves/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/champion-woman/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/champion-woman/accessories/bags/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/champion-woman/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/champion-woman/clothing/outerwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/champion-reverse-weave/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/champion-reverse-weave/accessories/hats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/socks/brand/champion-reverse-weave/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/champion-reverse-weave/accessories/socks/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/champion-reverse-weave/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/champion-reverse-weave/clothing/hoodies/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/parkas/brand/champion-reverse-weave/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/champion-reverse-weave/clothing/parkas/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/champion-reverse-weave/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/champion-reverse-weave/clothing/outerwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/casablanca/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/casablanca/clothing/hoodies/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/casablanca/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/casablanca/clothing/outerwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/casablanca/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/casablanca/clothing/pants/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/casablanca/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/casablanca/clothing/sweats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shirts/brand/casablanca/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/casablanca/clothing/shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/carrots/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/carrots/accessories/hats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/carrots/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/carrots/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/carrots/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/carrots/clothing/hoodies/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/carrots/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/carrots/clothing/outerwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/carrots/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/carrots/clothing/longsleeves/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/carrots/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/carrots/clothing/pants/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/wallets/brand/carne-bollente/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/carne-bollente/accessories/wallets/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/socks/brand/carne-bollente/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/carne-bollente/accessories/socks/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/souvenirs/brand/carne-bollente/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/carne-bollente/accessories/souvenirs/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/carne-bollente/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/carne-bollente/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/carne-bollente/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/carne-bollente/clothing/longsleeves/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/polo/brand/carne-bollente/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/carne-bollente/clothing/polo/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shirts/brand/carne-bollente/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/carne-bollente/clothing/shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/carne-bollente/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/carne-bollente/clothing/hoodies/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/knitwear/brand/carne-bollente/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/carne-bollente/clothing/knitwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/souvenirs/brand/carhartt-wip/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/carhartt-wip/accessories/souvenirs/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/parkas/brand/carhartt-wip/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/carhartt-wip/clothing/parkas/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/carhartt-wip/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/carhartt-wip/clothing/sweats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/call-me-917/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/call-me-917/clothing/pants/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/call-me-917/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/call-me-917/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/call-me-917/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/call-me-917/clothing/longsleeves/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/polo/brand/by-parra/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/by-parra/clothing/polo/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/by-parra/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/by-parra/clothing/sweats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/by-parra/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/by-parra/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/by-parra/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/by-parra/clothing/longsleeves/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/by-parra/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/by-parra/clothing/hoodies/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/bronze-56k/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/bronze-56k/accessories/hats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/bronze-56k/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/bronze-56k/clothing/hoodies/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/bronze-56k/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/bronze-56k/clothing/pants/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/bronze-56k/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/bronze-56k/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/bronze-56k/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/bronze-56k/clothing/sweats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/sandals/brand/birkenstock/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/birkenstock/footwear/sandals/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/billionaire-boys-club/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/billionaire-boys-club/accessories/hats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shirts/brand/billionaire-boys-club/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/billionaire-boys-club/clothing/shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/billionaire-boys-club/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/billionaire-boys-club/clothing/longsleeves/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/polo/brand/billionaire-boys-club/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/billionaire-boys-club/clothing/polo/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/billionaire-boys-club/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/billionaire-boys-club/clothing/hoodies/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/billionaire-boys-club/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/billionaire-boys-club/clothing/pants/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/billionaire-boys-club/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/billionaire-boys-club/clothing/sweats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/billionaire-boys-club/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/billionaire-boys-club/clothing/outerwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/billionaire-boys-club/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/billionaire-boys-club/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/being-hunted/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/being-hunted/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/grooming/brand/baxter-of-california/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/baxter-of-california/accessories/grooming/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/souvenirs/brand/barbour/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/barbour/accessories/souvenirs/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/barbour/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/barbour/accessories/hats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/dlya-sobak/brand/barbour/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/barbour/accessories/dlya-sobak/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/bags/brand/barbour/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/barbour/accessories/bags/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/barbour/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/barbour/clothing/outerwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/shoes/brand/barbour/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/barbour/footwear/shoes/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/hats/brand/babylon-la/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/babylon-la/accessories/hats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/babylon-la/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/babylon-la/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/babylon-la/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/babylon-la/clothing/hoodies/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shirts/brand/babylon-la/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/babylon-la/clothing/shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/babylon-la/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/babylon-la/clothing/longsleeves/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/assid/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/assid/clothing/hoodies/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/shirts/brand/assid/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/assid/clothing/shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/assid/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/assid/clothing/longsleeves/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/assid/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/assid/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/arte-antwerp/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/arte-antwerp/clothing/hoodies/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/arte-antwerp/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/arte-antwerp/clothing/longsleeves/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/arte-antwerp/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/arte-antwerp/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/knitwear/brand/arte-antwerp/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/arte-antwerp/clothing/knitwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/arte-antwerp/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/arte-antwerp/clothing/sweats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/arte-antwerp/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/arte-antwerp/clothing/pants/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/socks/brand/aries/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/aries/accessories/socks/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/knitwear/brand/aries/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/aries/clothing/knitwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/aries/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/aries/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/aries/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/aries/clothing/pants/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/aries/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/aries/clothing/hoodies/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/aries/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/aries/clothing/sweats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/aries/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/aries/clothing/longsleeves/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/alpha-industries/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/alpha-industries/clothing/hoodies/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/sweats/brand/alpha-industries/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/alpha-industries/clothing/sweats/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/outerwear/brand/alpha-industries/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/alpha-industries/clothing/outerwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/alpha-industries/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/alpha-industries/clothing/longsleeves/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/pants/brand/alltimers/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/alltimers/clothing/pants/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/sneakers/brand/adidas-originals/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/adidas-originals/footwear/sneakers/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/longsleeves/brand/aded/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/aded/clothing/longsleeves/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/hoodies/brand/aded/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/aded/clothing/hoodies/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/t-shirts/brand/aded/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/aded/clothing/t-shirts/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/aded/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/aded/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/brand/adidas-originals/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/adidas-originals/footwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/alltimers/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/alltimers/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/alpha-industries/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/alpha-industries/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/aries/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/aries/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/aries/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/aries/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/arte-antwerp/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/arte-antwerp/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/assid/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/assid/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/babylon-la/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/babylon-la/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/babylon-la/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/babylon-la/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/brand/barbour/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/barbour/footwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/barbour/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/barbour/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/barbour/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/barbour/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/baxter-of-california/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/baxter-of-california/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/being-hunted/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/being-hunted/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/billionaire-boys-club/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/billionaire-boys-club/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/billionaire-boys-club/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/billionaire-boys-club/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/brand/birkenstock/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/birkenstock/footwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/bronze-56k/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/bronze-56k/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/bronze-56k/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/bronze-56k/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/by-parra/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/by-parra/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/call-me-917/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/call-me-917/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/carhartt-wip/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/carhartt-wip/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/carhartt-wip/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/carhartt-wip/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/carne-bollente/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/carne-bollente/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/carne-bollente/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/carne-bollente/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/carrots/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/carrots/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/carrots/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/carrots/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/casablanca/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/casablanca/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/champion-reverse-weave/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/champion-reverse-weave/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/champion-reverse-weave/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/champion-reverse-weave/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/champion-woman/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/champion-woman/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/champion-woman/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/champion-woman/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/chinatown-market/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/chinatown-market/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/chinatown-market/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/chinatown-market/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/civilist/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/civilist/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/civilist/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/civilist/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/brand/clarks-originals/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/clarks-originals/footwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/comme-des-garcons-parfum/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/comme-des-garcons-parfum/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/brand/comme-des-gar-ons-play/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/comme-des-gar-ons-play/footwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/comme-des-gar-ons-play/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/comme-des-gar-ons-play/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/comme-des-gar-ons-shirt/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/comme-des-gar-ons-shirt/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/comme-des-gar-ons-wallets/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/comme-des-gar-ons-wallets/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/brand/converse/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/converse/footwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/daily-paper/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/daily-paper/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/daily-paper/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/daily-paper/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/brand/diemme/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/diemme/footwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/dr-le-de-monsieur/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/dr-le-de-monsieur/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/dr-le-de-monsieur/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/dr-le-de-monsieur/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/engineered-garments/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/engineered-garments/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/engineered-garments/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/engineered-garments/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/brand/fila/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/fila/footwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/brand/fila-woman/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/fila-woman/footwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/fjallraven/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/fjallraven/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/fucking-awesome/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/fucking-awesome/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/fucking-awesome/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/fucking-awesome/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/gasius/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/gasius/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/good-morning-tapes/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/good-morning-tapes/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/grind-london/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/grind-london/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/grind-london/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/grind-london/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/h-las/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/h-las/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/h-las/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/h-las/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/brand/hoka-one-one/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/hoka-one-one/footwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/human-with-attitude/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/human-with-attitude/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/human-with-attitude/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/human-with-attitude/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/idea/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/idea/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/idea/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/idea/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/brand/jason-markk/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/jason-markk/footwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/junior-executive/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/junior-executive/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/kappa/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/kappa/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/kappa/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/kappa/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/brand/keen/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/keen/footwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/know-wave/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/know-wave/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/lack-of-guidance/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/lack-of-guidance/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/liam-hodges/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/liam-hodges/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/l-i-e-s-records/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/l-i-e-s-records/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/life-s-a-beach/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/life-s-a-beach/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/maharishi/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/maharishi/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/maharishi/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/maharishi/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/marvis/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/marvis/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/brand/mephisto/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/mephisto/footwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/mki-miyuki-zoku/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/mki-miyuki-zoku/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/mki-miyuki-zoku/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/mki-miyuki-zoku/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/m-rc-noir/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/m-rc-noir/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/m-rc-noir/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/m-rc-noir/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/nasaseasons/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/nasaseasons/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/nasaseasons/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/nasaseasons/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/needles/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/needles/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/brand/new-balance/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/new-balance/footwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/nigel-cabourn/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/nigel-cabourn/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/nigel-cabourn/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/nigel-cabourn/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/norse-projects/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/norse-projects/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/olaf-hussein/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/olaf-hussein/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/palace-skateboards/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/palace-skateboards/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/p-a-m/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/p-a-m/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/p-a-m/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/p-a-m/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/patagonia/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/patagonia/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/patagonia/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/patagonia/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/patta/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/patta/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/patta/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/patta/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/platformx/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/platformx/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/pleasures/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/pleasures/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/brand/pleasures/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/pleasures/footwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/pleasures/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/pleasures/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/polar-skate-co/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/polar-skate-co/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/polar-skate-co/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/polar-skate-co/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/polythene-optics/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/polythene-optics/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/polythene-optics/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/polythene-optics/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/pop-trading-company/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/pop-trading-company/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/pop-trading-company/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/pop-trading-company/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/brand/premiata/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/premiata/footwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/primus/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/primus/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/rassvet/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/rassvet/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/rassvet/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/rassvet/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/brand/rassvet/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/rassvet/footwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/rave-skateboards/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/rave-skateboards/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/rave-skateboards/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/rave-skateboards/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/reception/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/reception/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/resort-corps/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/resort-corps/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/ripndip/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/ripndip/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/ripndip/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/ripndip/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/rokit/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/rokit/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/rokit/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/rokit/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/sergio-tacchini/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/sergio-tacchini/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/south-devon-chilli-farm/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/south-devon-chilli-farm/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/sporadic/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/sporadic/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/sporadic/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/sporadic/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/sss-world-corp/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/sss-world-corp/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/still-good/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/still-good/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/still-good/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/still-good/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/stussy/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/stussy/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/stussy/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/stussy/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/brand/suicoke/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/suicoke/footwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/sun-buddies/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/sun-buddies/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/taikan/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/taikan/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/brand/the-north-face/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/the-north-face/footwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/the-north-face/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/the-north-face/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/the-north-face/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/the-north-face/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/the-trilogy-tapes/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/the-trilogy-tapes/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/tommy-jeans/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/tommy-jeans/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/tommy-jeans/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/tommy-jeans/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/brand/tommy-jeans/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/tommy-jeans/footwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/footwear/brand/tommy-jeans-woman/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/tommy-jeans-woman/footwear/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/accessories/brand/tommy-jeans-woman/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/tommy-jeans-woman/accessories/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/tommy-jeans-woman/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/tommy-jeans-woman/clothing/', false, '301 Moved permanently');
	if (CSite::InDir('/catalog/clothing/brand/universal-works/')) LocalRedirect('https://www.itkkit.ru/catalog/brand/universal-works/clothing/', false, '301 Moved permanently');
}


if (CSite::InDir('/help/delivery/') && SITE_SERVER_NAME == 'www.itkkit.com') LocalRedirect('https://www.itkkit.com/help/delivery-and-payment/', false, '301 Moved permanently');

function getAllColors($filter = array(), $available = 'AVAILABLE')
{
    $arColors = [];
    $resColors = CIBlockElement::GetList(array(), array('IBLOCK_ID'=>1, '!=PROPERTY_COLORS'=>false, 'ACTIVE'=>'Y', 'TAGS'=>$available, $filter), false, false, array('ID', 'IBLOCK_ID', 'NAME', 'PROPERTY_COLORS'));
    while ($color = $resColors->Fetch()){
        array_push($arColors, strtolower($color['PROPERTY_COLORS_VALUE'][0]));
    }
    $resultColors = array_unique($arColors);

    return $resultColors;
}

function d($data,$defaultUserId = "403099"){
    global $USER;
    if($USER->GetId() == $defaultUserId){
        echo "<pre>".print_r($data,true)."</pre>";
    }
}

/*AddEventHandler("iblock", "OnBeforeIBlockElementUpdate", Array("SizeGridUpdate", "OnAfterIBlockElementUpdateHandler"));
class SizeGridUpdate
{
	public static $disableHandler = false;
	function OnAfterIBlockElementUpdateHandler(&$arFields)
	{
		if (self::$disableHandler)
			return;

		CModule::IncludeModule('iblock');

		if ($arFields['IBLOCK_ID'] == '24'){
			$arFilter = [];
			$productIds = [];
			$productBrands = [];
			$productSizes = [];
			foreach ($arFields['PROPERTY_VALUES'][173] as $row){
				if ($row['VALUE'] !== ''){
					$productIds[] = $row['VALUE'];
				}
			}
			foreach ($arFields['PROPERTY_VALUES'][170] as $row){
				if ($row['VALUE'] !== ''){
					$productBrands[] = $row['VALUE'];
				}
			}
			foreach ($arFields['PROPERTY_VALUES'][171] as $row){
				if ($row['VALUE'] !== ''){
					$productSizes[$row['VALUE']] = $row['DESCRIPTION'];
				}
			}
			if (!empty($productIds)){
				$arFilter['ID'] = $productIds;
			}
			if (!empty($productBrands)){
				$arFilter['PROPERTY_CML2_MANUFACTURER'] = $productBrands;
			}

			$arProducts = [];
			$resProduct = CIBlockElement::GetList(array(), array('IBLOCK_ID'=>1, 'SECTION_ID'=>355, 'INCLUDE_SUBSECTIONS'=>'Y', 'ACTIVE'=>'Y', $arFilter), false, false, array('ID'));
			while ($product = $resProduct->Fetch()){
				$arProducts[] = $product['ID'];
			}
			foreach ($arProducts as $product){
				$resOffers = CIBlockElement::GetList(array(), array('IBLOCK_ID'=>2, 'ACTIVE'=>'Y', 'PROPERTY_CML2_LINK'=>$product), false, false, array('ID', 'IBLOCK_ID', 'NAME', 'PROPERTY_SIZE'));
				while ($offer = $resOffers->Fetch()){
					if (!empty($productSizes[$offer['PROPERTY_SIZE_VALUE']])){
						
						CIBlockElement::SetPropertyValuesEx($offer['ID'], 2, array('SIZE_EU'=>$productSizes[$offer['PROPERTY_SIZE_VALUE']]));
					}
				}
			}
		}
	}
}*/

AddEventHandler("iblock", "OnAfterIBlockElementUpdate", "optimizeImage");

function optimizeImage(&$arFields) {


    $id_arr = [];

    if (isset($arFields['PREVIEW_PICTURE_ID'])) {
        $id_arr[] = $arFields['PREVIEW_PICTURE_ID'];
    }

    if (isset($arFields['DETAIL_PICTURE_ID'])) {
        $id_arr[] = $arFields['DETAIL_PICTURE_ID'];
    }

    if (count($id_arr) > 0) {
        $image_path_preview = CFile::GetPath($arFields['PREVIEW_PICTURE_ID']);
        $image_path_detail = CFile::GetPath($arFields['DETAIL_PICTURE_ID']);

        $root_dir = dirname(dirname(__DIR__));

        foreach ($id_arr as $id) {
            $image_path = CFile::GetPath($id);

            if (strlen($image_path) > 0) {
                $image_ext = explode('.', $image_path)[1];

                if ($image_ext == 'jpg' || $image_ext == 'jpeg') {
                    // mogrify jpg
          $run_opt = exec('mogrify -sampling-factor 4:2:0 -verbose -quality 70 -strip -interlace JPEG ' . $root_dir.$image_path . ' 2>&1', $out_log_jpg);
                    // $run_opt = shell_exec('mogrify -sampling-factor 4:2:0 -verbose -quality 70 -strip -interlace JPEG /home/dev/16623814e17d63af4009cb300d4d0955.jpg');
                } else if ($image_ext == 'png') {
                    // mogrify png
          $run_opt = exec('mogrify -quality 70 -verbose -strip' . $root_dir.$image_path . ' 2>&1', $out_log_png);
                    // $run_opt = shell_exec('mogrify -quality 70 -verbose -strip /home/dev/3a0e0321ab4d842d6dad9b5314127bb5.jpg');
                }
            }

        }

    }

}

function detect_plural_singular_ending($word, $word_case) {
  if (empty($word_case)) $word_case = 'im';

  $word = strtolower($word);

  $plural = array(
    'ры', 'ки', 'сы', 'ты', 'ия', 'ии', 'ни', 'ди', 'вы'
  );

  $exceptions = array(
    'парфюмерия',
    'одежда',
    'обувь',
    'косметика'
  );

  $additional_ex = array(
    'уход за обувью'
  );

  $word = strtolower($word);

  if (in_array($word,  $exceptions)) {
    if ($word_case == 'vi') {
      if ($word == 'одежда') {
        return array('ую', 'одежду');
      } else if ($word == 'парфюмерия') {
        return array('ую', 'парфюмерию');
      } else if ($word == 'косметика') {
        return array('ую', 'косметику');
      }
      return 'ую';
    }
    return 'ая';
  }

  $word = substr($word, -2);

  if (in_array($word, $plural)) {
    return 'ые';
  }

  return false;
}

function getRandomBrands($exBrand, $count = 7)
{
	$brands = [];
	$resBrands = CIBlockElement::GetList(array('rand'=>'asc'), array('IBLOCK_ID'=>3, '!=ID'=>$exBrand, 'ACTIVE'=>'Y'), false, array('nPageSize'=>$count), array('NAME'));
	while ($arBrands = $resBrands->Fetch()){
		$brands[] = $arBrands['NAME'];
	}

	return implode(', ', $brands);
}

function getTagPageDescription($iblockId, $sectionId, $brandId, $brandName = '', $sectionName = '', $color = '', $tag = '')
{
	$result = '';
	$resultHead = '<div class="grid-row" style="margin: 20px 0 100px;"><div class="col-md-6"><div class="catalog-section__brand-text">';
	$resultFoot = '</div></div></div>';
	$subSections = [];
	$minMaxPrice = [];

	$curPage = explode('?', $_SERVER['REQUEST_URI'], 2)[0];
	$resSeoText = CIBlockElement::GetList(array(), array('IBLOCK_ID'=>23, 'NAME'=>$curPage), false, array(), array('ID', 'DETAIL_TEXT'));
	if ($arSeoText = $resSeoText->GetNextElement()){
		$fields = $arSeoText->GetFields();
		$result = $fields['~DETAIL_TEXT'];
	} else {
		$curSectionDepth = CIBlockSection::GetList(array('name'=>'asc'), array('IBLOCK_ID'=>$iblockId, 'ID'=>$sectionId), true, array('ID', 'IBLOCK_ID', 'DEPTH_LEVEL'))->GetNext()['DEPTH_LEVEL'];
		$tagFilter = [];
		/*if ($color !== ''){
			$tagFilter = array('=PROPERTY_COLORS'=>$color);
		}*/
		if ($tag !== ''){
			$resTag = CIBlockElement::GetList(array(),array('IBLOCK_ID'=>21, '=IBLOCK_SECTION_ID'=>585, 'CODE'=>$tag), false, array() ,array('*','PROPERTY_*'));
		    if ($arTag = $resTag->GetNextElement()){
		        $arTagFields = $arTag->GetFields();
		        $arTagProps = $arTag->GetProperties();
		        $tagTitle = $arTagProps['H1_SEO']['VALUE'];
		        foreach ($arTagProps['FILTER']['VALUE'] as $key => $value){
	                switch (str_replace(array('%', '!', '!%'), '', $value)){
	                    case 'NAME':
	                    case 'DETAIL_TEXT':
	                    case 'PREVIEW_TEXT':
	                        if (substr($value, 0, 2) == '!%'){
	                            $arFilters[] = array('!%'.substr($value, 2) => $arTagProps['FILTER']['DESCRIPTION'][$key]);
	                        } elseif (substr($value, 0, 1) == '!'){
	                            $arFilters[] = array('!='.substr($value, 1) => $arTagProps['FILTER']['DESCRIPTION'][$key]);
	                        } else {
	                            $arFilters[] = array('%'.$value => $arTagProps['FILTER']['DESCRIPTION'][$key]);
	                        }
	                        break;
	                    case 'ID':
	                        $arFilters[] = array('='.$value => explode(',',$arTagProps['FILTER']['DESCRIPTION'][$key]));
	                        break;
	                    default:
	                        $arPropsResult = [];
	                        foreach (explode(',',$arTagProps['FILTER']['DESCRIPTION'][$key]) as $prop){
	                            $arPropsResult[] = trim($prop);
	                        }
	                        if (substr($value, 0, 2) == '!%'){
	                            $arFilters[] = array('!%PROPERTY_'.substr($value, 2) => $arPropsResult);
	                        } elseif (substr($value, 0, 1) == '%'){
	                            $arFilters[] = array('%PROPERTY_'.substr($value, 1) => $arPropsResult);
	                        } elseif (substr($value, 0, 1) == '!'){
	                            $arFilters[] = array('!=PROPERTY_'.substr($value, 1) => $arPropsResult);
	                        } else {
	                            $arFilters[] = array('=PROPERTY_'.$value => $arPropsResult);
	                        }
	                        break;
	                }
	            }

	            $tagFilter = call_user_func_array('array_merge', $arFilters);
		    }
		}
		if ($tag !== ''){
			$resItems = CIBlockElement::GetList(array(), array('IBLOCK_ID'=>$iblockId, 'INCLUDE_SUBSECTIONS'=>'Y', 'PROPERTY_CML2_MANUFACTURER'=>$brandId, 'ACTIVE'=>'Y', '!=DETAIL_PICTURE'=>false, 'TAGS'=>'AVAILABLE', $tagFilter), false, array(), array('ID', 'IBLOCK_ID', 'NAME', 'IBLOCK_SECTION_ID', 'PROPERTY_RETAIL_PRICE_MIN'));
		} elseif ($color !== ''){
			$resItems = CIBlockElement::GetList(array(), array('IBLOCK_ID'=>$iblockId, 'INCLUDE_SUBSECTIONS'=>'Y', 'PROPERTY_CML2_MANUFACTURER'=>$brandId, '=PROPERTY_COLORS'=>$color, 'ACTIVE'=>'Y', '!=DETAIL_PICTURE'=>false, 'TAGS'=>'AVAILABLE'), false, array(), array('ID', 'IBLOCK_ID', 'NAME', 'IBLOCK_SECTION_ID', 'PROPERTY_RETAIL_PRICE_MIN'));
		} else {
			$resItems = CIBlockElement::GetList(array(), array('IBLOCK_ID'=>$iblockId, 'SECTION_ID'=>$sectionId, 'INCLUDE_SUBSECTIONS'=>'Y', 'PROPERTY_CML2_MANUFACTURER'=>$brandId, 'ACTIVE'=>'Y', '!=DETAIL_PICTURE'=>false, 'TAGS'=>'AVAILABLE'), false, array(), array('ID', 'IBLOCK_ID', 'NAME', 'IBLOCK_SECTION_ID', 'PROPERTY_RETAIL_PRICE_MIN'));
		}
		$itemsCount = $resItems->SelectedRowsCount();
	    while ($items = $resItems->Fetch()){
	    	$subSections[] = $items['IBLOCK_SECTION_ID'];
	    	$minMaxPrice[] = $items['PROPERTY_RETAIL_PRICE_MIN_VALUE'];
	    }

	    $arrCountries = getHlCountries();
	    $cur_type = ($_SESSION['LAST_COUNTRY'] == 'RU') ? 'RUB': KDXCurrency::$CurrentCurrency;
	    $cur_name = ($_SESSION['LAST_COUNTRY'] == 'RU') ? 'rub.' : KDXCurrency::GetCurrencyName(KDXCurrency::$CurrentCurrency);
	    $useVAT = $arrCountries[$_SESSION['LAST_COUNTRY']]['UF_USE_VAT'];

	    $minPrice = min($minMaxPrice);
	    $maxPrice = max($minMaxPrice);
	    $minPrice = KDXCurrency::convert($useVAT=="N" ? $minPrice / 1.21 : $minPrice, KDXCurrency::$CurrentCurrency);
	    $minPrice = $cur_name == 'rub.' ? $minPrice.' '.$cur_name : $cur_name.$minPrice;
	    $maxPrice = KDXCurrency::convert($useVAT=="N" ? $maxPrice / 1.21 : $maxPrice, KDXCurrency::$CurrentCurrency);
	    $maxPrice = $cur_name == 'rub.' ? $maxPrice.' '.$cur_name : $cur_name.$maxPrice;

	    $arSubSections = array_unique($subSections);
	    $arItemSection = [];
	    foreach ($arSubSections as $ssec){
	    	$arItemSection[] = strtolower(CIBlockSection::GetList(array('name'=>'asc'), array('IBLOCK_ID'=>$iblockId, 'ID'=>$ssec), true, array('ID', 'IBLOCK_ID', 'UF_EN_NAME'))->GetNext()['UF_EN_NAME']);
	    }

	    if ($curSectionDepth == 1 && $color == '' && $tag == ''){
	    	$result = $resultHead;
	    	if ($minPrice == $maxPrice){
	    		$result .= 'Shop '.$brandName.' '.strtolower($sectionName).' from the latest collections at itk online store. Complete your look with the newest '.strtolower($sectionName).' of your favorite brand name: '.implode(', ', $arItemSection).' & more. Our offering includes '.$itemsCount.' items at the price of '.$minPrice.'. Be unique, feel comfortable and stylish with itk Store. We guarantee 100% authenticity on all our products and propose free shipping on all orders over €350.';
	    	} else {
	    		$result .= 'Shop '.$brandName.' '.strtolower($sectionName).' from the latest collections at itk online store. Complete your look with the newest '.strtolower($sectionName).' of your favorite brand name: '.implode(', ', $arItemSection).' & more. Our offering includes '.$itemsCount.' items in the '.$minPrice.' – '.$maxPrice.' price range. Be unique, feel comfortable and stylish with itk Store. We guarantee 100% authenticity on all our products and propose free shipping on all orders over €350.';
	    	}
	    	$result .= $resultFoot;
	    } elseif ($curSectionDepth > 1 && $color == '' && $tag == ''){
	    	$getColors = getAllColors(array('=SECTION_ID'=>$sectionId, '=PROPERTY_CML2_MANUFACTURER'=>$brandId));
	    	$arColors = [];
	    	foreach ($getColors as $col){
	    		$arColors[] = strtolower($col);
	    	} 
	    	$result = $resultHead;
	    	if ($minPrice == $maxPrice){
	    		$result .= 'Shop '.$brandName.' '.strtolower($sectionName).' for '.$minPrice.' at itk online store. Product range includes '.$itemsCount.' '.$brandName.' '.strtolower($sectionName).' in different colors: '.implode(', ', $arColors).'. Enjoy 100% authenticity and free shipping on all orders over €350. Find more leading streetwear brands including: '.getRandomBrands($brandId).', etc.';
	    	} else {
	    		$result .= 'Shop '.$brandName.' '.strtolower($sectionName).' at itk online store. Product range includes '.$itemsCount.' '.$brandName.' '.strtolower($sectionName).' in different colors: '.implode(', ', $arColors).'. Enjoy 100% authenticity, free shipping on all orders over €350 and competitive prices from '.$minPrice.' to '.$maxPrice.'. Find more leading streetwear brands including: '.getRandomBrands($brandId).', etc.';
	    	}
	    	$result .= $resultFoot;
	    } elseif ($color !== '' && $tag == ''){
	    	$result = $resultHead;
	    	if ($minPrice == $maxPrice){
	    		$result .= 'Shop '.ucfirst($color).' '.$brandName.' '.strtolower($sectionName).' at itk online store. Our offer includes '.$itemsCount.' '.$color.' '.$brandName.' '.strtolower($sectionName).' at the price of '.$minPrice.'. Enjoy the best shopping experience with itk: 100% authentic products, competitive prices, free shipping on all orders over €350. Here you will also find a wide range of the most recognizable brands from all over the world to create your own unique style.';
	    	} else {
	    		$result .= 'Shop '.ucfirst($color).' '.$brandName.' '.strtolower($sectionName).' at itk online store. Our offer includes '.$itemsCount.' '.$color.' '.$brandName.' '.strtolower($sectionName).' in the '.$minPrice.' – '.$maxPrice.' price range. Enjoy the best shopping experience with itk: 100% authentic products, competitive prices, free shipping on all orders over €350. Here you will also find a wide range of the most recognizable brands from all over the world to create your own unique style.';
	    	}
	    	$result .= $resultFoot;
	    } elseif ($tag !== ''){
	    	$result = $resultHead;
	    	if ($minPrice == $maxPrice){
	    		$result .= 'Shop '.$tagTitle.' at itk online store. Our offer includes '.$itemsCount.' '.$tagTitle.' at the price of '.$minPrice.'. Enjoy the best shopping experience with itk: 100% authentic products, competitive prices, free shipping on all orders over €350. Here you will also find a wide range of the most recognizable brands from all over the world to create your own unique style.';
	    	} else {
	    		$result .= 'Shop '.$tagTitle.' at itk online store. Our offer includes '.$itemsCount.' '.$tagTitle.' in the '.$minPrice.' – '.$maxPrice.' price range. Enjoy the best shopping experience with itk: 100% authentic products, competitive prices, free shipping on all orders over €350. Here you will also find a wide range of the most recognizable brands from all over the world to create your own unique style.';
	    	}
	    	$result .= $resultFoot;
	    }
	}

    echo $result;
}

function isBot()
{
	if (!empty($_SERVER['HTTP_USER_AGENT'])) {
		$options = array(
			'YandexBot', 'YandexAccessibilityBot', 'YandexMobileBot','YandexDirectDyn',
			'YandexScreenshotBot', 'YandexImages', 'YandexVideo', 'YandexVideoParser',
			'YandexMedia', 'YandexBlogs', 'YandexFavicons', 'YandexWebmaster',
			'YandexPagechecker', 'YandexImageResizer','YandexAdNet', 'YandexDirect',
			'YaDirectFetcher', 'YandexCalendar', 'YandexSitelinks', 'YandexMetrika',
			'YandexNews', 'YandexNewslinks', 'YandexCatalog', 'YandexAntivirus',
			'YandexMarket', 'YandexVertis', 'YandexForDomain', 'YandexSpravBot',
			'YandexSearchShop', 'YandexMedianaBot', 'YandexOntoDB', 'YandexOntoDBAPI',
			'Googlebot', 'Googlebot-Image', 'AdsBot-Google'
		);
 
		foreach ($options as $row) {
			if (stripos($_SERVER['HTTP_USER_AGENT'], $row) !== false) {
				return true;
			}
		}
	}
 
	return false;
}

function getDomain()
{
    if (isset($_SERVER['HTTPS'])){
        $protocol = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != "off") ? "https" : "http";
    } else {
        $protocol = 'http';
    }
    return $protocol . "://" . $_SERVER['SERVER_NAME'];
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

#MANDRILL API отправка почты
function get_order_content_mail($order_id){
    $arrContextOptions = array(
        "ssl" => array(
            "verify_peer" => false,
            "verify_peer_name" => false,
        ),
    );
//$homepage = file_get_contents('https://itkkit.com/bitrix/admin/sale_print.php?PROPS_ENABLE=Y&doc=waybillnew_2&ORDER_ID='.$order_id, false, stream_context_create($arrContextOptions));
//echo $homepage;
//die();

    $conteudosite = file_get_contents('https://itkkit.com/bitrix/admin/sale_print.php?PROPS_ENABLE=Y&doc=waybillnew_2&ORDER_ID=' . $order_id, false, stream_context_create($arrContextOptions));


    $dom = new DOMDocument();
    @$dom->loadHTML($conteudosite);

    $data_table_row = $dom->getElementById("table_row");
    $html_table_row = $dom->saveHTML($data_table_row);

#Убираем Латышский язык
    $html_table_row = str_replace('Prece', '', $html_table_row);
    $html_table_row = str_replace('Mērv.', '', $html_table_row);
    $html_table_row = str_replace('Daudzums', '', $html_table_row);
    $html_table_row = str_replace('Cena', '', $html_table_row);
    $html_table_row = str_replace('PVN', '', $html_table_row);
    $html_table_row = str_replace('Summa', '', $html_table_row);
    $html_table_row = str_replace('Atlaide', '', $html_table_row);

    $html_table_row = str_replace('<table', '<div class="table"', $html_table_row);
    $html_table_row = str_replace('table>', 'div>', $html_table_row);

    $html_table_row = str_replace('<th', '<div class="th"', $html_table_row);
    $html_table_row = str_replace('th>', 'div>', $html_table_row);

    $html_table_row = str_replace('<tr', '<div class="tr"', $html_table_row);
    $html_table_row = str_replace('tr>', 'div>', $html_table_row);

    $html_table_row = str_replace('<td', '<div class="td"', $html_table_row);
    $html_table_row = str_replace('td>', 'div>', $html_table_row);
    
    $html_table_row=str_replace("'", "", $html_table_row);


    $data_discount_row = $dom->getElementById("discount_row");
    $html_discount_row = $dom->saveHTML($data_discount_row);

    $data_summary_row = $dom->getElementById("summary_row");
    $html_summary_row = $dom->saveHTML($data_summary_row);
#Убираем Латышский язык
    $html_summary_row = str_replace('/Kopā bez PVN', '', $html_summary_row);
    $html_summary_row = str_replace('/PVN', '', $html_summary_row);
    $html_summary_row = str_replace('/Piegāde', '', $html_summary_row);
    $html_summary_row = str_replace('/Kopā', '', $html_summary_row);

    $html_result = $html_table_row . "" . $html_discount_row . "" . $html_summary_row;

//echo '<pre>';
//var_dump ($html_result);
//echo '</pre>';
//die();

    $html_result = addslashes($html_result);

    $html_result = str_replace(array("\r\n", "\r", "\n"), ' ', $html_result);
    $html_result = str_replace('  ', '', $html_result);
    
    return $html_result;
}


#MANDRILL API отправка почты
function mandrill_send_mail($template, $to, $subject){
    $variables_array=explode('|',$subject);
    if (count($variables_array)<2) return false;
    unset($variables_array[0]); 
    unset($variables_array[1]); 
    addmessage2log($template);
    addmessage2log($variables_array);
    $vars_to_template=[];
    $temp_array=[];
    $var_string='';
    $html_result='';
    
    foreach ($variables_array as $var) {
        $temp_array['name']=explode('=',$var)[0];
        $var_string.='{"name":"'.$temp_array['name'].'",';
        if (isset(explode('=',$var)[1])) {
            $temp_array['value']=explode('=',$var)[1];
        } else {
            $temp_array['value']='';
        }
        $var_string.='"content":"'.$temp_array['value'].'"},';
        $vars_to_template[]=$temp_array;
    }
    $var_string = substr($var_string,0,-1);
    addmessage2log($vars_to_template);
    addmessage2log($var_string);
    
    //признак для вставки контента - $template=='SKARYUK_SALE_NEW_ORDER_CONTENT'
    //номер заказа - $variables_array[0]['value']
    if ($template=='SKARYUK_SALE_NEW_ORDER_CONTENT') {
        //получение контента.
        $order_id=$vars_to_template[0]['value'];
        $html_result=get_order_content_mail($order_id);
        addmessage2log($html_result);
    }
    
    if ($template=='SKARYUK_SALE_NEW_ORDER_CONTENT_RU') {
        //получение контента.
        $order_id=$vars_to_template[0]['value'];
        $html_result=get_order_content_mail($order_id);
        addmessage2log($html_result);
    }
    
    
    $content='{'
            . '"key":"2A0RhnvvhMqoNXHZH85L1g",'
            . '"template_name":"'.$template.'",'
            . '"template_content":['
            . '{"name":"order_content","content":"'
                . $html_result 
                . '"}'
            . '],'
                . '"message":{'
                    . '"html":"",'
                    . '"text":"",'
                    . '"subject":"",'
                    . '"from_email":"",'
                    . '"from_name":"",'
                    . '"to":[{"email":"'.$to.'"}],'
                    . '"headers":{},'
                    . '"important":false,'
                    . '"track_opens":false,'
                    . '"track_clicks":false,'
                    . '"auto_text":false,'
                    . '"auto_html":false,'
                    . '"inline_css":false,'
                    . '"url_strip_qs":false,'
                    . '"preserve_recipients":false,'
                    . '"view_content_link":false,'
                    . '"bcc_address":"",'
                    . '"tracking_domain":"",'
                    . '"signing_domain":"",'
                    . '"return_path_domain":"",'
                    . '"merge":false,'
                    . '"merge_language":"mailchimp",'
                    . '"global_merge_vars":[],'
                    . '"merge_vars":['
                            . '{'
                                    . '"rcpt": "'.$to.'",'
                                    . '"vars":['
                                        . $var_string      
                                        //. '{"name":"FNAME","content":"Artem Belikov"},'
                                        //. '{"name":"ORDER_ID","content":"123456"}'
                                    . ']'
                            . '}'
                    . '],'
                    . '"tags":[],'
                    . '"google_analytics_domains":[],'
                    . '"google_analytics_campaign":"",'
                    . '"metadata":{"website":""},'
                    . '"recipient_metadata":[],'
                    . '"attachments":[],'
                    . '"images":[]'
                . '},'
                . '"async":false,'
                . '"ip_pool":"",'
                . '"send_at":""'
        . '}';

    $request='https://mandrillapp.com/api/1.0/messages/send-template';
    if( $curl = curl_init() ) {
        curl_setopt($curl, CURLOPT_URL, $request);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
        $response = curl_exec($curl);
        curl_close($curl);
        
        $result=json_decode($response, true)[0]['status'];
        if ($result=='sent') {
            return true; 
        } else {
            addmessage2log($to);
            addmessage2log($subject);
            addmessage2log($response);
            return false;
        }
    }
    return true;
}

#SMTP отправка почты
function smtp_mail($to, $subject, $message, $additional_headers='', $additional_parameters='')
{
//    addmessage2log('message');
//    addmessage2log($message);
    $decode_subject=iconv_mime_decode($subject);
    if (explode('|',$decode_subject)[0]=='MANDRIL') {
        if (isset(explode('|',$decode_subject)[1])) {
            return mandrill_send_mail(explode('|',$decode_subject)[1], $to, $decode_subject);
        } else {
            return false;
        }
    }
    
    require 'PHPMailer/src/Exception.php';
    require 'PHPMailer/src/PHPMailer.php';
    require 'PHPMailer/src/SMTP.php';

    $mail = new PHPMailer(true);   // Passing `true` enables exceptions
 
    try {
        //Server settings
        $mail->CharSet = "utf-8";
        $mail->SMTPDebug = 0;                                 // Enable verbose debug output
        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host = 'smtp.gmail.com';                  // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                               // Enable SMTP authentication
        $mail->Username = 'info@itkkit.com';             // SMTP username
        $mail->Password = '100Rokudarbs!';                           // SMTP password
        $mail->SMTPSecure = 'ssl';                            // Enable SSL encryption, TLS also accepted with port 465
        $mail->Port = 465;                                    // TCP port to connect to

        //Recipients
        $mail->setFrom('info@itkkit.com', 'itk store');          //This is the email your form sends From
        $mail->addAddress($to); // Add a recipient address
        //$mail->addAddress('contact@example.com');               // Name is optional
        //$mail->addReplyTo('info@example.com', 'Information');
        //$mail->addCC('cc@example.com');
        //$mail->addBCC('bcc@example.com');

        //Attachments
        //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
        //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

        //Content
        $mail->isHTML(true);                                  // Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body    = $message;
        //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
        
//        AddMessage2Log('SMTP_SEND');
         
        $mail->send();
        
        return true;
        //echo 'Message has been sent';
    } catch (Exception $e) {
        addmessage2log('Mailer Error: ' . $mail->ErrorInfo);
        return false;
    }
}


#Логирование почты
function custom_mail($to, $subject, $message, $additional_headers='', $additional_parameters='')
{
   if ($additional_parameters!='') {
      return @smtp_mail($to, $subject, $message, $additional_headers, $additional_parameters);
   } else {
      return @smtp_mail($to, $subject, $message, $additional_headers);
   }
}