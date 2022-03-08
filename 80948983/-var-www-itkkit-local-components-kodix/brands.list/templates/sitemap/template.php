<ul class="sitemap-item__wrap">
    <?if (is_array($arResult['BRANDS']) && count($arResult['BRANDS']) > 0){
        $i = 0; $first_russian = true;
        foreach($arResult['UPPERCASE_ALPHABET'] as $alpha){
            if (is_array($arResult['BRANDS'][$alpha]) && count($arResult['BRANDS'][$alpha]) > 0){?>
                <li class="_parent">
                    <p><?=$alpha?></p>
                    <ul>
                        <?foreach($arResult['BRANDS'][$alpha] as $brand) {?>
                            <li>
                                <a href="<?=$brand['DETAIL_PAGE_URL']?>" class="link link--primary"><?=$brand['NAME']?></a>
                            </li>
                        <?}?>
                    </ul>
                </li>
            <?}?>
        <?}?>
    <?}?>
</ul>