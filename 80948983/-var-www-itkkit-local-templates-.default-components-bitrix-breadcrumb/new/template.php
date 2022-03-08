<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$curPage = $GLOBALS['APPLICATION']->GetCurPage($get_index_page=false);
if ($curPage != SITE_DIR){
	if (empty($arResult) || (!empty($arResult[count($arResult)-1]['LINK']) && $curPage != urldecode($arResult[count($arResult)-1]['LINK'])))
		$arResult[] = array('TITLE' => htmlspecialcharsback($GLOBALS['APPLICATION']->GetTitle(false, true)), 'LINK' => $curPage);
}

if(empty($arResult))
	return "";
$strReturn = '<ul class="breadcrumb" itemscope="" itemtype="http://schema.org/BreadcrumbList">';
$num_items = count($arResult);
for($index = 0, $itemSize = $num_items; $index < $itemSize; $index++){
	$count = $index;
	$title = htmlspecialcharsex($arResult[$index]["TITLE"]);
	
	if ($arResult[$index]["LINK"] <> "" && $index != $itemSize-1){
		if ($arResult[$index]['LINK'] == '/catalog/')
			continue;
		if (strpos($arResult[$index]['LINK'], "index.php") > 0) continue;
		$strReturn .= '<li itemscope="" itemprop="itemListElement" itemtype="http://schema.org/ListItem"><a itemprop="item" href="'.$arResult[$index]["LINK"].'"><span itemprop="name">'.$title.'</span><meta itemprop="position" content="'.++$count.'"></a></li>';
		$strReturn .= '<li>></li>';
	} else {
		$strReturn .= '<li><p>'.$title.'</p></li>';
	}
}
$strReturn .= '</ul>';
return $strReturn;
?>