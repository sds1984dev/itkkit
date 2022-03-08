<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$APPLICATION->SetPageProperty('NOT_SHOW_PAGE_WRAPPER', 'Y');
$APPLICATION->SetPageProperty('NOT_SHOW_TITLE', 'Y');
$APPLICATION->SetPageProperty('SECTION_TITLE', '');
$APPLICATION->SetPageProperty('NOT_SHOW_FOOTER', 'Y');?>

<main>
    <div class="grid-container">
        <div class="grid-row">
            <div class="col-sm-12 col-lg-offset-3 col-lg-6">
                <h1 class="heading--h1"><?=GetMessage("AUTH_CHANGE_PASSWORD")?></h1>
                <p>
                    <?=$arResult["GROUP_POLICY"]["PASSWORD_REQUIREMENTS"];?>
                </p>
                <form method="post" action="<?=$arResult["AUTH_FORM"]?>" name="bform">
                    <?if (strlen($arResult["BACKURL"]) > 0){?>
                        <input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
                    <?}?>
                    <input type="hidden" name="AUTH_FORM" value="Y">
                    <input type="hidden" name="TYPE" value="CHANGE_PWD">
                    <div class="form-row form-row--lg-gap">
                        <input class="form-input"
                               id="USER_LOGIN_CHANGE_PWD"
                               name="USER_LOGIN"
                               type="text"
                               placeholder="<?=GetMessage('AUTH_LOGIN')?>"
                               value="<?=$arResult["LAST_LOGIN"]?>">
                    </div>
                    <div class="form-row form-row--lg-gap">
                        <input class="form-input"
                               type="text"
                               id="USER_CHECKWORD"
                               name="USER_CHECKWORD"
                               placeholder="<?=GetMessage('AUTH_CHECKWORD')?>"
                               value="<?=$arResult["USER_CHECKWORD"]?>">
                    </div>
                    <div class="form-row form-row--lg-gap">
                        <input class="form-input"
                               type="password"
                               id="USER_PASSWORD"
                               name="USER_PASSWORD"
                               placeholder="<?=GetMessage('AUTH_NEW_PASSWORD_REQ')?>"
                               value="">
                    </div>
                    <div class="form-row form-row--lg-gap">
                        <input class="form-input"
                               type="password"
                               id="USER_CONFIRM_PASSWORD"
                               name="USER_CONFIRM_PASSWORD"
                               placeholder="<?=GetMessage('AUTH_NEW_PASSWORD_CONFIRM')?>"
                               value="">
                    </div>
                    <div class="error_msg_v2">
                        <?ShowMessage($arParams["~AUTH_RESULT"]);?>
                    </div>
                    <button class="btn btn--primary btn--inline-lg btn--block-md" type="submit" ><?=GetMessage('AUTH_CHANGE')?></button>
                </form>
            </div>
        </div>
    </div>
</main>

<script type="text/javascript">
document.bform.USER_LOGIN_CHANGE_PWD.focus();
</script>
</div>