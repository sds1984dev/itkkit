<?php

require_once ("get_empty_anons.php");

define('MAX_SIZE', 320);
define('COUNT', 5);

//ob_start();
$arItems = get_empty_anons();

if (is_array($arItems) && count($arItems) > 0)
{
    for ($i = 0; $i < COUNT; $i++)
    {
        $arPicture = CFile::GetFileArray($arItems[$i]["DETAIL_PICTURE"]);
        $isImage = CFile::IsImage($arPicture["FILE_NAME"]);

        if ($isImage)
        {
            $arSize = array('width' => MAX_SIZE, 'height' => MAX_SIZE);
            $previewImage = CFile::ResizeImageGet($arPicture, $arSize, BX_RESIZE_IMAGE_PROPORTIONAL_ALT);
            $image = new Imagick('/var/www/itkkit' . $previewImage['src']);
            $image->setImageCompressionQuality(96);
            $arFields = array("PREVIEW_PICTURE" => CFile::MakeFileArray('/var/www/itkkit' . $previewImage['src']));
            $element = new CIBlockElement;
            $element->Update($arItems[$i]["ID"], $arFields);

        }
        //echo $arItems[$i]["ID"] . "===" . $arItems[$i]["NAME"] . "</br>";
        if ($i == count($arItems)) break;
    }
    //ob_end_flush();
}

?>