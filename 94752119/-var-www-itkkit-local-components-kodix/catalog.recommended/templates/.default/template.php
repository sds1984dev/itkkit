<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>

<?if(empty($arResult))
    return false;
?>

<div class="recommend items-list">
    <h2>Мы рекомендуем</h2>
    <ul class="catalog-small">
        <?foreach($arResult as $item){?>
        <li>
            <h3><a href="<?=$item["DETAIL_PAGE_URL"]?>"><?=$item["NAME"]?></a></h3>
            <div class="price"><strong><?=format_price($item["RETAIL_PRICE"]);?></strong></div>

            <div class="img">
                <?$detail=kdxCFile::ResizeImageGet($item["DETAIL_PICTURE"], array('width'=>87, 'height'=>121), BX_RESIZE_IMAGE_PROPORTIONAL, true);?>
                <?$preview=kdxCFile::ResizeImageGet($item["PREVIEW_PICTURE"], array('width'=>87, 'height'=>121), BX_RESIZE_IMAGE_PROPORTIONAL, true);?>
                <a href="<?=$item["DETAIL_PAGE_URL"]?>">
                    <img class="changePicOnHover" src="<?=$preview["src"];?>" outPicture="<?=$preview["src"];?>" overPicture="<?=$detail["src"];?>" alt="<?=$item["NAME"]?>" src="./img/dummy/item/additional4.jpg">
                </a>
            </div>
        </li>
        <?}?>
    </ul>
</div>
<!-- //recommend -->