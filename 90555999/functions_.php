<?
function CalculateBasketItems() {
	$arSectionsFootwear = array(405, 347, 353);
        $arSectionsDecks = array(424);
	$itemsCountInSections = array(
                "FOOTWEAR" => 0,
                "DECKS" => 0
        );
        $arBasketItems = array();
        $dbBasketItems = CSaleBasket::GetList(
                array(
                        "NAME" => "ASC",
                        "ID" => "ASC"
                    ),
                array(
                        "FUSER_ID" => CSaleBasket::GetBasketUserID(),
                        //"LID" => SITE_ID,
                        "ORDER_ID" => "NULL"
                    ),
                false,
                false,
                array("ID", "PRODUCT_ID", "QUANTITY", "DELAY", "CAN_BUY")
            );
        while ($arItems = $dbBasketItems->Fetch())
        {
            // if (strlen($arItems["CALLBACK_FUNC"]) > 0)
            // {
            //     CSaleBasket::UpdatePrice($arItems["ID"], 
            //                              $arItems["CALLBACK_FUNC"], 
            //                              $arItems["MODULE"], 
            //                              $arItems["PRODUCT_ID"], 
            //                              $arItems["QUANTITY"]);
            //     $arItems = CSaleBasket::GetByID($arItems["ID"]);

            // }
            $product = CCatalogSku::GetProductInfo($arItems['PRODUCT_ID']);
            $dbItemSection = CIBlockElement::GetElementGroups($product['ID']);
            while ($section = $dbItemSection->Fetch()) {
                if (in_array($section['ID'], $arSectionsFootwear)) $itemsCountInSections["FOOTWEAR"]++;
                if (in_array($section['ID'], $arSectionsDecks)) $itemsCountInSections["DECKS"]++;
            }
        }
        return $itemsCountInSections;
}
?>