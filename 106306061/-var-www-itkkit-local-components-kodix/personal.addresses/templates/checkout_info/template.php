<div class="checkout_sec_title">1. <?=GetMessage('Данные о покупателе')?></div>
<a href="/checkout/?logout=yes" class="checkout_edit_link"><?=GetMessage('Выйти')?></a>
<div class="checkout_sec_info">
    <?
        $User = CUser::GetList(($by="id"), ($order="desc"), array('ID'=>$USER->GetID()))->Fetch();
        echo $User['NAME'] ? $User['NAME'] : '';
        echo $User['LAST_NAME'] ? ' '.$User['LAST_NAME'] : '';
        echo $User['PERSONAL_PHONE'] ? ', '. $User['PERSONAL_PHONE'] : '';
        echo $User['EMAIL'] ? ', '.$User['EMAIL'] : '';
    ?>
</div>