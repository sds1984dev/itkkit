<?php
/**
 * Date: 05.04.2015
 * Time: 22:30
 */
?>
<div class="success_block">
    <h1 class="success_title"><?=GetMessage('CP_KO_THANKS')?></h1>
    <h1 class="success_title"><?=GetMessage('CP_KO_ORDER_ID')?> <font color="red"><?=$arResult['ORDER']->id?></font></h1>
    <div class="success_note"><?=GetMessage('CP_KO_NOTE')?></div>
    <?showPayButton($arResult['ORDER'])?>
    <br />
    <div style="text-align:center; margin-bottom:10px;"><?=GetMessage('CP_KO_ORDER_TIMER')?></div>

    <div class="compass_wrapper">
        <div class="compass"></div>
    </div>
</div>
<?if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $APPLICATION->set_cookie('CRITEO_TRANSACTION','Y');
    ?>
<script>
    window.parent.KDX.setPage('/cart/?SUCCESS=Y&ORDER_ID=' + <?=$arResult['ORDER']->id?>);
    KDXSale.updateCart(true);

    ga('require', 'ecommerce');

    ga('ecommerce:addTransaction', {
        'id': '<?=$arResult['ORDER']->id?>',                    // Transaction ID. Required.
        'revenue': '<?=$arResult['ORDER']->price?>',               // Grand Total.
        'shipping': <?=$arResult['ORDER']->price_delivery?>

    });
    <?
    $cart = new KDXCart(false, $arResult['ORDER']->id);
    foreach ($cart->getAvailable() as $item) {
        ?>ga('ecommerce:addItem', {
            id: '<?=$arResult['ORDER']->id?>',
            name: '<?=str_replace("'",'',$item['NAME'])?>',
            sku: '<?=$item['PRODUCT_ID']?>',
            price: '<?=$item['PRICE']?>',
            quantity: '<?=$item['QUANTITY']?>'
        });
    <?}?>
    ga('ecommerce:send');

</script>
    <?
    global $USER;
    $arSKUs = array();

    $arIDs = array();
    foreach ($cart->getAvailable() as $item)
    {
        $arIDs[] = $item['PRODUCT_ID'];
    }
    if(!empty($arIDs))
    {
        CModule::IncludeModule('iblock');
        $res = CIBlockElement::GetList(
            array(),
            array(
                'IBLOCK_ID' => KDXSettings::getSetting('SKU_IBLOCK_ID'),
                'ID' => $arIDs
            ),
            false,
            false,
            array('ID','IBLOCK_ID','PROPERTY_CML2_LINK')
        );

        while($arSKU = $res->Fetch())
        {
            $arSKUs[ $arSKU['ID'] ] = $arSKU['PROPERTY_CML2_LINK_VALUE'];
        }
    }
    ?>
<script type="text/javascript">
    if(typeof rrApiOnReady != 'undefined') {
        rrApiOnReady.push(function () {
            rrApi.setEmail("<?=$USER->GetEmail()?>");
            try {
                rrApi.order({
                    transaction: <?=$arResult['ORDER']->id?>,
                    items: [
                        <?
                        $ns = false;
                        foreach ($cart->getAvailable() as $item) {?>
                        <?if($ns){?>, <?}?>
                        {
                            id: <?=$arSKUs[ $item['PRODUCT_ID'] ]?>,
                            qnt: <?=$item['QUANTITY']?>,
                            price: <?=$item['PRICE']?>
                        }
                        <?$ns=true;}?>
                    ]
                });
            } catch (e) {
            }
        })
    }
</script>
    <?
        global $USER;
        $md5email = '';

        if($USER->IsAuthorized())
        {
            $md5email = '{ event: "setHashedEmail", email: "'.md5($USER->GetEmail()).'" }';
        }
        $criteoTovars = array();
        $cart = new KDXCart(false, $arResult['ORDER']->id);
        foreach ($cart->getAvailable() as $item) {
            if($item['PROPS']['PARENT_ID']){
                $criteoTovars[$item['PROPS']['PARENT_ID']] = array(
                    'id'=>$item['PROPS']['PARENT_ID'],
                    'price'=>round($item['PRICE']),
                    'quantity'=>$criteoTovars[$item['PROPS']['PARENT_ID']]['quantity'] + round($item['QUANTITY'])
                );
            }
        }
        sort($criteoTovars);
        $criteoView = array('event'=>'trackTransaction', 'id'=>$arResult['ORDER']->id, 'item'=>$criteoTovars);
    ?>
    <script type="text/javascript" src="//static.criteo.net/js/ld/ld.js" async="true"></script>
    <script type="text/javascript">
        window.criteo_q = window.criteo_q || [];
        window.criteo_q.push(
            { event: "setAccount", account: 23230 },
            { event: "setSiteType", type: "d" },
            <?=$md5email?>
            <?=','.json_encode($criteoView)?>
        );
    </script>
<?}?>
<script>
    <?if(!isAjax()){?>
    $(function(){
    <?}?>
    if($('.compass').length){
        $('.compass').attr('data-deg',0);
        redirectTimeOut =  10; // sec
        degRandom = 0,
            duraRandom =  1,
            delayRandom = 1;
        setInterval(function(){
            degRandom += 1;
            $('.compass').animateRotate(degRandom, duraRandom, '', function(){
                $('.compass').attr('data-deg',degRandom);
            });


        },delayRandom);
        var obj=document.getElementById('timer_inp');
        $.int = setInterval(function(){
            if (obj.innerHTML==0)
            {
                window.location = $('.btn_buy.mod_std').attr('href');
                clearInterval($.int);
            }
            else
            {
                obj.innerHTML--;
            }
        },1000);
    }
    <?if(!isAjax()){?>
    });
    <?}?>
</script>