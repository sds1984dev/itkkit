<?
use Bitrix\Main; 
Main\EventManager::getInstance()->addEventHandler(
    'sale',
    'OnSaleOrderSaved',
    'myFunction'
);

//в обработчике получаем сумму, с которой планируются некоторые действия в дальнейшем:

function myFunction(Main\Event $event)
{
    // /** @var Order $order */
    // $order = $event->getParameter("ENTITY");
    // $oldValues = $event->getParameter("VALUES");
    $isNew = $event->getParameter("IS_NEW");

    if ($isNew)
    {
        echo '<script>
        alert( "Hello world" );
        </script>';
        var_dump("test");
    }
}
?>