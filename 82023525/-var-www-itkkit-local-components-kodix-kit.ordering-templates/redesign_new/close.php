<?php
?>
<div class="success_block">
    <h1 class="success_title"><?=GetMessage('CP_KO_CLOSE_CART')?></h1>
    <div class="success_note" style="width: 50%;"><?$APPLICATION->IncludeComponent(
            "bitrix:main.include",
            ".default",
            array(
                "AREA_FILE_SHOW" => "sect",
                "AREA_FILE_SUFFIX" => "close_checkout",
                "AREA_FILE_RECURSIVE" => "Y",
            ),
            false
        );?></div>
</div>