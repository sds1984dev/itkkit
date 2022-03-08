<?php
/**
 * Date: 05.04.2015
 * Time: 22:30
 */
$APPLICATION->SetPageProperty("MAIN_CLASS", "main--full-height");
$APPLICATION->SetPageProperty("PAGE_WRAPPER_CLASS", "grid-container--center");
?>

<style>
    .checkout-timer {
        margin-bottom: 10px;
    }

    @media (min-width: 588px) {
        .checkout-timer {
            text-align: center;
        }
    }
</style>

<section class="checkout-section--success">
    <div class="checkout-section__msg">
        <svg class="icon icon-check_message">
            <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/local/templates/kit_new/resources/svg/app.svg#check_message"></use>
        </svg>
        <h1 class="heading--h1"><?=GetMessage('CP_KO_ORDER')?> #<?=$arResult['ORDER']->id?> <?=GetMessage('CP_KO_ORDER_ISSUED')?></h1>
        <!--<p><?/*=GetMessage('CP_KO_ORDER_OPERATOR')*/?></p>-->
        <?
        /*global $USER;
        if ($arResult['ORDER']->pay_system_id == 5 && $USER->GetID() == 403099){?>
            <script src="https://www.paypal.com/sdk/js?client-id=AUqeUL94_UCABd5TnRBDSpJie58AWO4wOvjoj-PMaqY9hdw_ag8aIJM6gYUeccTRh0V_KjGH0TJ_UVMJ"></script>
            <div id="paypal-button-container"></div>
            <script>
                paypal.Buttons({
                    createOrder: function(data, actions) {
                        // This function sets up the details of the transaction, including the amount and line item details.
                        return actions.order.create({
                            purchase_units: [{
                                amount: {
                                    value: '0.01'
                                }
                            }]
                        });
                    },
                    onApprove: function(data, actions) {
                        // This function captures the funds from the transaction.
                        return actions.order.capture().then(function(details) {
                            // This function shows a transaction success message to your buyer.
                            console.log(details.payer)
                            alert('Transaction completed by ' + details.payer.name.given_name);
                        });
                    }
                }).render('#paypal-button-container');
            </script>
        <?} else {*/?>
            <?showPayButton($arResult['ORDER'])?>
<!--        <a class="link btn btn--primary btn--inline btn--block-mobile" href="#">Оплатить заказ</a>-->
            <div class="checkout-timer"><?=GetMessage('CP_KO_ORDER_TIMER')?></div>
        <?//}?>

        <div class="compass_wrapper">
            <div class="compass"></div>
        </div>
    </div>
</section>

<?//=GetMessage('CP_KO_ORDER_ID')?>
<?//=GetMessage('CP_KO_NOTE')?>
<!--<div class="compass_wrapper">-->
<!--    <div class="compass"></div>-->
<!--</div>-->
<?if($_SERVER['REQUEST_METHOD'] == 'POST'){?>
    <?
    $cart = new KDXCart(false, $arResult['ORDER']->id);
    ?>
    <script>
        
        gtag('get', 'UA-52518243-2', 'client_id', (id) => {
            let CID = '';
            if (typeof(id) == "undefined") {
                let cookies = document.cookie;
                let cookiearray = cookies.split(';');
                let client_id = '';
                for(var i=0; i<cookiearray.length; i++) {
                  let name = cookiearray[i].split('=')[0];
                  let value = cookiearray[i].split('=')[1];
                  if (name.trim() == "_ga") {
                    console.log(name + "====" + value);
                    client_id = value;
                    break;
                  }
               }
               CID = client_id.split('.')[2] + "." + client_id.split('.')[3];
            } else {
                CID = id;
            }
            $.post(
                "https://www.google-analytics.com/collect",
                {
                        v: '1',
                        tid: 'UA-52518243-2',
                        cid: CID,
                        dh : 'itkkit.com',
                        dp : '/payment_success',
                        dt : 'Заказ оформлен',
                        ti : '<?=$arResult['ORDER']->id?>',
                        tr : '<?=$arResult['ORDER']->price?>',
                        ts : '<?=$arResult['ORDER']->price_delivery?>',
                        <?$i = 0;?>
                        <?foreach ($cart->getAvailable() as $item):?>
                        <?
                            $i++;
                            $sectionID = '';
                            $dbGroups = CIBlockElement::GetElementGroups($item['PROPS']['PARENT_ID'], true);
                            while($group = $dbGroups->Fetch())
                                $sectionID = $group['ID'];
                            $nav = CIBlockSection::GetNavChain(false, $sectionID);
                            $path = '/catalog';
                            foreach ($nav as $arSectionPath) {
                                foreach ($arSectionPath as $p){
                                    $path .= '/'.$p['CODE'];
                                }
                            }

                            $brandId = null;
                            $iterator = CIBlockElement::GetProperty(1, $item['PROPS']['PARENT_ID'],array(),array('ID' => 1));
                            while ($row = $iterator->Fetch())
                            {
                              $brandId = $row['VALUE'];
                            }

                            if (!is_null($brandId)) {
                                $k = 0;
                                $brands = '';
                                $dbBrands = CIBlockElement::GetList(Array("SORT"=>"ASC"), Array("IBLOCK_ID"=>3,"ID"=>$brandId));
                                while($arBrand = $dbBrands->GetNext())
                                {
                                    if ($k > 0) {
                                        $brands .= ','.$arBrand['NAME'];
                                    } else {
                                        $brands .= $arBrand['NAME'];
                                    }
                                    $k++;
                                }
                                unset($k);
                            }
                        ?>
                        pa : 'purchase',
                        pr<?=$i?>id : '<?=$item['PROPS']['PARENT_ID']?>',
                        pr<?=$i?>nm : '<?=$item['NAME']?>',
                        pr<?=$i?>ca : '<?=$path?>',
                        pr<?=$i?>br : '<?=$brands?>',
                        pr<?=$i?>pr : '<?=$item['PRICE']?>',
                        pr<?=$i?>qt : '<?=$item['QUANTITY']?>',
                    <?endforeach;?>
                    <?unset($i);?>
                }
        )
        });

        window.parent.KDX.setPage('/checkout/?SUCCESS=Y&ORDER_ID=' + <?=$arResult['ORDER']->id?>);
        KDXSale.updateCart(true);

        ga('require', 'ecommerce');
        ga('ecommerce:addTransaction', {
            'id': '<?=$arResult['ORDER']->id?>',                    // Transaction ID. Required.
            'revenue': '<?=$arResult['ORDER']->price?>',               // Grand Total.
            'shipping': <?=$arResult['ORDER']->price_delivery?>

        });
        <?
		
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
        $md5email = '{ event: "setHashedEmail", email: "'.md5($USER->GetEmail()).'" },';
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
            <?=json_encode($criteoView)?>
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
                /*$('.compass').animateRotate(degRandom, duraRandom, '', function(){
                    $('.compass').attr('data-deg',degRandom);
                });*/
            },delayRandom);
            var obj=document.getElementById('timer_inp');
            $.int = setInterval(function(){
                if (obj.innerHTML==0)
                {
                    if($('.btn_buy').is('a')){
                        //window.location = $('.btn_buy.mod_std').attr('href');
                    }

                    if($('#payment-paypal').is('form'))
                    {
                        $('#payment-paypal').submit();
                    }
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