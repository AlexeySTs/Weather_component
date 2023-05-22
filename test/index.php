<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

use Mcart\Weather\Weather;
use Mcart\Weather\Tables\CitiesTable;
use Bitrix\Main\Loader;

    if (!Loader::includeModule('mcart.weather'))
    {
        ShowError('Error not include mcart.weather module');
        return;
    };

    $list_cities = CitiesTable::getList([
        'select' => ['LAST_USE','ID'],
    ]);

    $date_del = (time() - (60 * 60 * 24 * 5));

    while($query = $list_cities->fetch()){
        $last_use = ($query['LAST_USE']->getTimestamp());
        if($last_use < $date_del) {
            CitiesTable::delete($query['ID']);
        }
    };
?>
 <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>