<?php
/**
 * User: denis-takumi
 * Date: 11.04.2015
 * Time: 14:25
 */
$_SERVER["DOCUMENT_ROOT"] = __DIR__ . '/../..';
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');

CModule::IncludeModule('iblock');
CModule::IncludeModule('catalog');
CModule::IncludeModule('sale');

$IBE = new CIBlockElement();
$IBS = new CIBlockSection();

ini_set('memory_limit','1G');
set_time_limit(0);

$arSKUs = array();
$arProducts = array();
$arSections = array();
// read sku

$FILE = fopen(__DIR__.'/sku.csv','r');
$bFirst=true;
$header = array();
while(!feof($FILE))
{
    $line =  trim(fgets($FILE));

    if(empty($line))
        continue;

    if($bFirst)
    {
        $header = explode(';',$line);
        $bFirst = false;
    }
    else
    {
        $data = explode(';',$line);
        $arSKU = array();
        foreach($header as $key => $field)
        {
            $arSKU[ $field ] = str_replace(array('~~','<br>'),array(';',"\n\r"),$data[ $key ]);
        }

        $arSKUs[ $arSKU['CML2_LINK'] ][] = $arSKU;
    }
}
fclose($FILE);

//read models
$FILE = fopen(__DIR__.'/elements.csv','r');
$bFirst=true;
$header = array();
while(!feof($FILE))
{
    $line =  trim(fgets($FILE));

    if(empty($line))
        continue;

    if($bFirst)
    {
        $header = explode(';',$line);
        $bFirst = false;
    }
    else
    {
        $data = explode(';',$line);
        $arProduct = array();
        foreach($header as $key => $field)
        {
            $arProduct[ $field ] = str_replace(array('~~','<br>'),array(';',"\n\r"),$data[ $key ]);
        }

        $arProducts[ $arProduct['IBLOCK_SECTION_ID'] ][ $arProduct['ID'] ] = $arProduct;
    }
}
fclose($FILE);

//read sections
$FILE = fopen(__DIR__.'/sections.csv','r');
$bFirst=true;
$header = array();
while(!feof($FILE))
{
    $line =  trim(fgets($FILE));

    if(empty($line))
        continue;

    if($bFirst)
    {
        $header = explode(';',$line);
        $bFirst = false;
    }
    else
    {
        $data = explode(';',$line);
        $arSection = array();
        foreach($header as $key => $field)
        {
            $arSection[ $field ] = str_replace(array('~~','<br>'),array(';',"\n\r"),$data[ $key ]);
        }

        $arSections[ $arSection['ID'] ] = $arSection;
    }
}
fclose($FILE);

ksort($arSections);

foreach($arSections as &$arSection)
{
    $arFields = array(
        'IBLOCK_ID' => KDXSettings::getSetting('CATALOG_IBLOCK_ID'),
        'NAME' => $arSection['NAME_RU'],
        'UF_EN_NAME' => $arSection['NAME_EN'],
        'UF_MENU_NAME' => 'Все '.$arSection['NAME_RU'],
        'UF_EN_MENU_NAME' => 'All '.$arSection['NAME_EN'],
        'CODE' => $arSection['CODE'],
    );

    if( is_array($arSections[ $arSection['IBLOCK_SECTION_ID'] ]))
        $arFields['IBLOCK_SECTION_ID'] = $arSections[ $arSection['IBLOCK_SECTION_ID'] ]['NEW_ID'];

    $SECTION_ID = $IBS->Add($arFields);

    if(!$SECTION_ID)
    {
        print_r($arSection);
        print_r($IBS->LAST_ERROR);

    }
    else
    {
        $arSection['NEW_ID'] = $SECTION_ID;

        foreach( $arProducts[ $arSection['ID'] ] as &$arProduct)
        {
            $arBrand = array();

            if(!empty($arProduct['MANUFACTURER'])) {
                $arBrand = CIBlockElement::GetList(
                    array(
                        'NAME' => 'ASC',
                        'SORT' => 'ASC'
                    ),
                    array(
                        'IBLOCK_ID' => KDXSettings::getSetting('BRANDS_IBLOCK_ID'),
                        'CODE' => $arProduct['MANUFACTURER'],
                    ),
                    false,
                    false,
                    array('ID')
                )->Fetch();

                if (!$arBrand) {
                    $arBrand['ID'] = $IBE->Add(array(
                        'IBLOCK_ID' => KDXSettings::getSetting('BRANDS_IBLOCK_ID'),
                        'NAME' => $arProduct['MANUFACTURER'],
                        'CODE' => $arProduct['MANUFACTURER'],
                    ));
                }
            }



            $arProps = array(
                'EN_NAME' => $arProduct['NAME_EN'],
                'EN_DETAIL_TEXT' => $arProduct['DESC_EN'],
                'CML2_ARTICLE' => $arProduct['ARTICLE'],
                'CML2_MANUFACTURER' => $arBrand['ID'],
                'COLOR' => $arProduct['COLOR'],
            );

            $arGallery = json_decode($arProduct['JSON(GALLERY)']);
            if(is_array($arGallery))
            {
                foreach($arGallery as $file)
                {
                    $file = 'https://shop.fott.ru' . $file;

                    $arFile = CFile::MakeFileArray($file);
                    $arFile['MODULE_ID'] = 'kodix.alfalinkexchange';

                    $FILE_ID = CFile::SaveFile($arFile,'kodix.alfalinkexchange');
                    $arProps['GALLERY'][] = $FILE_ID;
                }
            }


            $arFields = array(
                'IBLOCK_ID' => KDXSettings::getSetting('CATALOG_IBLOCK_ID'),
                'IBLOCK_SECTION_ID' => $SECTION_ID,
                'NAME' => $arProduct['NAME_RU']?:$arProduct['NAME_EN'],
                'DETAIL_TEXT' => $arProduct['DESC_RU']?:$arProduct['DESC_EN'],
                'CODE' => $arProduct['CODE'],
                'PROPERTY_VALUES' => $arProps,
            );

            if(is_array($arProps['GALLERY']))
            {
                $arFields['DETAIL_PICTURE'] = CFile::MakeFileArray( CFile::CopyFile( current($arProps['GALLERY']) ) );
            }

            $PRODUCT_ID = $IBE->Add($arFields);

            if(!$PRODUCT_ID)
            {
                print_r($arProduct);
                print_r($IBE->LAST_ERROR);
            }
            else{

                foreach($arSKUs[$arProduct['ID']] as $arSKU)
                {
                    $arProps = array(
                        'CML2_LINK' => $PRODUCT_ID,
                        'SIZE' => $arSKU['SIZE'],
                    );

                    $arFields = array(
                        'NAME' => $arSKU['NAME'],
                        'PROPERTY_VALUES' => $arProps,
                        'IBLOCK_ID' => KDXSettings::getSetting('SKU_IBLOCK_ID'),
                    );

                    $SKU_ID = $IBE->Add($arFields);

                    if(!$SKU_ID)
                    {
                        print_r($arSKU);
                        print_r($IBE->LAST_ERROR);
                    }
                    else
                    {
                        CCatalogProduct::Add(array('ID' => $SKU_ID, 'QUANTITY' => $arSKU['QUANTITY']));

                        CPrice::Add(array(
                            'PRODUCT_ID' => $SKU_ID,
                            'CATALOG_GROUP_ID' => KDXSettings::getSetting('RETAIL_PRICE_ID'),
                            'PRICE' => KDXCurrency::convert($arSKU['RETAIL_PRICE'], KDXSettings::getSetting('BASE_PRICE_ID'), 'RUB'),
                            'CURRENCY' => KDXSettings::getSetting('DEFAULT_CURRENCY')
                        ));

                        CPrice::Add(array(
                            'PRODUCT_ID' => $SKU_ID,
                            'CATALOG_GROUP_ID' => KDXSettings::getSetting('BASE_PRICE_ID'),
                            'PRICE' => KDXCurrency::convert($arSKU['BASE_PRICE'], KDXSettings::getSetting('BASE_PRICE_ID'), 'RUB'),
                            'CURRENCY' => KDXSettings::getSetting('DEFAULT_CURRENCY')
                        ));

                    }
                }

            }

        }
    }
}



require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_after.php');
