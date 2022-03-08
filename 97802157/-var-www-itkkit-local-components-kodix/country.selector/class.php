<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/**
 * Created by Dmitry Salnikov.
 * Date: 28.07.14
 */

use GeoIp2\Database\Reader;

class CKodixCountrySelectorComponent extends CBitrixComponent
{
    public function onPrepareComponentParams($arParams)
    {
        $arParams['CACHE_TIME'] = $arParams['CACHE_TIME']?:'86400';
        $arParams['SITE_ID'] = SITE_ID;
        return $arParams;
    }

    public function executeComponent()
    {
        $obCache = new CPHPCache();


        if(false && $obCache->InitCache($this->arParams['CACHE_TIME'],serialize($this->arParams),'/kcache/'.__CLASS__))
        {
            die('1233');
            $this->arResult = $obCache->GetVars();
        }
        else
        {
            if (!CModule::IncludeModule('highloadblock')) {
                return false;
            }
            if ($hb_entity = \Bitrix\Highloadblock\HighloadBlockTable::getList(array('filter' => array('TABLE_NAME' => 'b_countries'),))->fetch()) {
                $classname = $hb_entity['NAME'] . 'Table';
                $hb_entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hb_entity);
                $hb_class = new $classname();

                $res = $hb_class->getList(array('order'=>array('ID')));
                while ($fields = $res->fetch()) {
                    if($fields["UF_MAIN_LIST"] == "Y")
                        $this->arResult["MAIN_COUNTRIES"][$fields["UF_CONTRY_CODE"]] = $fields;
                    else
                        $this->arResult["ALL_COUNTRIES"][$fields["UF_CONTRY_CODE"]] = $fields;
                }
            }

            //$this->arResult['ALL_COUNTRIES'] = kitDataCollector::getCountriesArray(false, array("NAME_LANG"=>"ASC"));

            $this->arResult["SHOW_GEO_POPUP"] = false;
            $this->arResult["LAST_COUNTRY"] = getLastCountry();
            $_SESSION["LAST_COUNTRY"] = getLastCountry();


            if(!strlen($this->arResult["LAST_COUNTRY"])) {
                $this->arResult["SHOW_GEO_POPUP"] = true;
                $tmp = \Bitrix\Main\Application::getInstance()->getContext()->getServer();
                $reader = new Reader($_SERVER["DOCUMENT_ROOT"].'/../GeoLite2-Country.mmdb');
                $record = ($_SERVER["HTTP_CF_IPCOUNTRY"] != '') ? $_SERVER["HTTP_CF_IPCOUNTRY"] : $reader->country($tmp['REMOTE_ADDR']);
                $countryIsoCode = $record->country->isoCode;
                //$countryIsoCode = LANGUAGE_ID == 'en' ? "LV" : "RU";
                if (is_null($countryIsoCode) || $countryIsoCode == '')
                    $countryIsoCode = "LV";

                $filename = $_SERVER["DOCUMENT_ROOT"].'/../geoip.log';

                if (fileperms($filename) != 0777)
                    chmod($filename, 0777);

                $serverName = $_SERVER["SERVER_NAME"];
                $time = date('d.m.Y, H:i:s');
                $clientIP = ($_SERVER["HTTP_CF_IPCOUNTRY"] != '') ? $_SERVER["HTTP_CF_IPCOUNTRY"] : $_SERVER['REMOTE_ADDR'];
                $output = '[' . $serverName . ']' . ' - - IP address: ' . $clientIP . ' - - [' . $time . '] - - Country code: ' . $countryIsoCode . "\n";

                $file = fopen($filename, "a+");
                fwrite($file, $output);
                fclose($file);

                if ($countryIsoCode == 'GB'){
                    $countryIsoCode = 'UK';
                }
                if ($this->arResult["SHOW_GEO_POPUP"]){
                    switch ($countryIsoCode){
                        case 'US':
                            $_SESSION['KDX_CURRENCY'] = 'USD';
                            break;
                        case 'UK':
                        case 'GB':
                            $_SESSION['KDX_CURRENCY'] = 'GBP';
                            break;
                        case 'RU':
                            $_SESSION['KDX_CURRENCY'] = 'RUB';
                            break;
                        default:
                            $_SESSION['KDX_CURRENCY'] = 'EUR';
                            break;
                    }
                }

                $this->arResult["LAST_COUNTRY"] = $_SESSION['LAST_COUNTRY'] = $countryIsoCode;
            }

            $dbSites = CSite::GetList($by='sort',$sort='asc');

            while($arSite = $dbSites->Fetch())
            {
                if(SITE_ID==$arSite['LID']) {
                    $arSite["CURRENT"] = "Y";
                    $this->arResult["CURRENT"] = $arSite['LANGUAGE_ID'];
                }
                if(in_array($arSite['LANGUAGE_ID'],$this->arParams['LANGS']))
                    $this->arResult['SITES'][ $arSite['LANGUAGE_ID'] ] = $arSite;
            }

            if($obCache->StartDataCache())
                $obCache->EndDataCache($this->arResult);
        }

        $this->includeComponentTemplate();
    }
}
