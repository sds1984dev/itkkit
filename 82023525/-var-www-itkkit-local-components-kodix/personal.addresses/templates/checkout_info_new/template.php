<div class="checkout__step checkout__step--current">
    <div class="checkout__step-header">
        <div class="checkout__step-heading">1. <?=GetMessage('Данные о покупателе')?></div>
        <a class="btn link link--secondary checkout__step-edit js_checkout-edit" href="/checkout/?logout=yes">
            <?=GetMessage('Выйти')?>
        </a>
    </div>
    <div class="checkout__step-summary">
        <?
        $User = CUser::GetList(($by="id"), ($order="desc"), array('ID'=>$USER->GetID()))->Fetch();
        echo $User['NAME'] ? $User['NAME'] : '';
        echo $User['LAST_NAME'] ? ' '.$User['LAST_NAME'] : '';
        echo $User['PERSONAL_PHONE'] ? ', '. $User['PERSONAL_PHONE'] : '';
        echo $User['EMAIL'] ? ', '.$User['EMAIL'] : '';
        ?>
    </div>
</div><!-- END CHECKOUT STEP-->