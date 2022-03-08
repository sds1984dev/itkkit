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
                        "ORDER_ID" => "NULL"
                    ),
                false,
                false,
                array("ID", "PRODUCT_ID", "QUANTITY", "DELAY", "CAN_BUY")
            );
        while ($arItem = $dbBasketItems->Fetch())
        {
            $product = CCatalogSku::GetProductInfo($arItem['PRODUCT_ID']);
            $dbItemSection = CIBlockElement::GetElementGroups($product['ID']);

            while ($section = $dbItemSection->Fetch()) {
                if (in_array($section['ID'], $arSectionsFootwear)) $itemsCountInSections["FOOTWEAR"] += $arItem['QUANTITY'];
                if (in_array($section['ID'], $arSectionsDecks)) $itemsCountInSections["DECKS"]++;
            }
        }
        return $itemsCountInSections;
}
?>