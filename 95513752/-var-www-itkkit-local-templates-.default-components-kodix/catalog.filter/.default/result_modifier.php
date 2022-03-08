<?php
/**
 * Created by:  KODIX 26.03.2015 15:38
 * Email:       support@kodix.ru
 * Web:         www.kodix.ru
 * Developer:   Kostin Denis
 */
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

foreach($arResult['PROPERTIES'] as $key => $arProp)
{
    unset($arResult['PROPERTIES'][$key]);
    $arResult['PROPERTIES'][$arProp['CODE']] = $arProp;
}