<?php
/**
 * Created by:  KODIX 03.04.2015 11:21
 * Email:       support@kodix.ru
 * Web:         www.kodix.ru
 * Developer:   Kostin Denis
 */

$MESS['NO_ACCOUNT'] = 'Do not have an account yet?';
$MESS['CART_SIZE'] = 'Size';
$MESS["EDIT_ADDRESS"] = "Edit";

$MESS['CP_KO_ADDRESS'] = 'Shipping and payment addresses';
$MESS['CP_KO_DELIVERY'] = 'Shipping';
$MESS['CP_KO_CHOOSE_PROFILE'] = 'Choose address';
$MESS['CP_KO_NO_DELIVERY_TO_PROFILE'] = 'No available of deliveries services for this address';
$MESS['CP_KO_PAY_SYSTEM'] = 'Payment';
$MESS['CP_KO_CHOOSE_DELIVERY'] = 'Choose shipping service';
$MESS['CP_KO_NO_PAY_TO_DELIVERY'] = 'There are no available methods of payment for the selected service DELIVERY';
$MESS['CP_KO_CHOOSE_PAY_SYSTEM'] = 'Choose payment service';

$MESS['CP_KO_NOT_AVAILABLE'] = 'These products are out of stock now';
$MESS['CP_KO_DELIVERY_COMMENT'] = 'Order comments';
$MESS['CP_KO_USER_EXISTS'] = 'Already an ITKKIT Member?';
$MESS['CP_KO_USER_ENTER'] = 'Sign in!';
$MESS['CP_KO_EMPTY_BASKET'] = 'Your shopping cart is empty';

$MESS['CP_KO_PRICE'] = 'Products total without VAT:';
$MESS['CP_KO_PRICE_EU'] = 'Products total with VAT:';
$MESS['CP_KO_DISCOUNT'] = 'Your discount';
$MESS['CP_KO_TOTAL'] = 'Total';
$MESS['CP_KO_TOTAL_IN_EURO'] = 'Total in euro';
$MESS['CP_KO_COUPON'] = 'Input a coupon code';
$MESS['CP_KO_COUPON2'] = 'Coupon code';
$MESS['CP_KO_APPLY_COUPON'] = 'Apply';
$MESS['CP_KO_ERROR_COUPON'] = 'Promo code not found';
$MESS['CP_KO_ORDER'] = 'Proceed to checkout';
$MESS['CP_ORDER_NOTE_1'] = 'By clicking "Proceed to checkout" button above<br>you confirm that you’ve read and accepted<br>';
$MESS['CP_ORDER_NOTE_2'] = 'ITK KIT Store\'s terms & condition';

$MESS['CP_KO_HELLO'] = 'Hello there, ';
$MESS['CP_KO_PCS'] = 'pcs.';

$MESS['CP_KO_PAY_NAME_1'] = 'Cash';
$MESS['CP_KO_PAY_NAME_2'] = 'Debit/credit card';
$MESS['CP_KO_PAY_NAME_3'] = 'PayPal';
$MESS['CP_KO_PAY_NAME_4'] = 'ChronoPay';
$MESS['CP_KO_PAY_NAME_5'] = 'Smart PayPal';


$MESS['FREE_SHIP'] = 'Free!';

$MESS['DAYS_DELIVERY'] = 'working days';

$MESS['CP_KO_CURRENCY_NOTE'] = 'All orders make and paid in euro. Internal site\'s rate :<br><b>EURO_PRICE = CURRENCY_PRICE</b>';
$MESS['CP_KO_CURRENCY_ATTENTION'] = 'Attention, please! Internal site\'s rate may be different with the rate of issuing bank of your card.';
$MESS['CP_KO_WRITEOFF'] = 'Write off';


$MESS['корзина'] = 'Cart';
$MESS['Все заказы оформляются и оплачиваются в евро.'] = 'All orders are in euro. Site currency rate: '.KDXCurrency::format(1).' = '.KDXCurrency::convertAndFormat(1,KDXCurrency::$CurrentCurrency).'. Attention! The internal exchange rate of site may be different from the rate of the issuing bank of your card.';
$MESS['ADDRESS_RIGA_18'] = 'Riga, LV - 1050 Z.A. Meierovica Blvd., 18';
$MESS['TIME_WORK_18'] = 'Mon - Fr 11:00 - 20:00 Sat - Sun 12:00 - 18:00';
$MESS['IN_SHOP_RIGA'] = 'You can pick up your order only in our Riga store';
$MESS['Регистрация'] = 'Registration';
$MESS['Данные о покупателе'] = 'Buyer info';
$MESS['Выйти'] = 'Logout';
$MESS['нет в наличии'] = 'out of stock';
$MESS['Очистить корзину'] = 'Empty cart';
$MESS['оформление'] = 'ordering';
$MESS['Войти'] = 'Log in';
$MESS['Доставка'] = 'Delivery';
$MESS['Изменить'] = 'Edit';
$MESS['Самовывоз'] = 'Pickup';
$MESS['Оплата'] = 'Payment';
$MESS['Пароль'] = 'Password';
$MESS['Запомнить меня'] = 'Remember me';
$MESS['Забыли пароль?'] = 'Forgot your password?';
$MESS['Комментарий'] = 'Comment';
$MESS['Службы доставки'] = 'Shipping Services';
$MESS['Адрес доставки'] = 'Shipping Address';
$MESS['Доставка по адресу'] = 'Shipping Address';
$MESS['Новый адрес'] = 'New Address';
$MESS['Продолжить'] = 'Continue';
$MESS['Оформить заказ'] = 'Checkout';
$MESS['Комментарий к заказу'] = 'Comment';
$MESS['Вы можете добавить комментарий к заказу'] = 'You can add a comment to an order';
$MESS["SAVE_ADDRESS"] = "Save";

$MESS["CP_KO_DISCLAIMER"] = "Use our catalog to fill it out.";
$MESS["CP_KO_TO_CATALOG"] = "To catalog";
$MESS["VAT"] = "VAT 21%";
$MESS["CP_KO_AGREEMENT"] = "By placing an order you accept our <a href='/help/privacy-policy/'>privacy policy</a>, <a href='/help/delivery/'>delivery and payment conditions</a>, <a href='/help/return-and-exchanges/'>customer service</a>";
$MESS['DELIVERY_COST_INFO'] = "For orders with more than one pair of shoes you will be automatically charged €10 for every extra pair.";
$MESS['DELIVERY_COST_DECKS_INFO'] = "For decks orders you will be automatically charged €10 for every extra unit.";