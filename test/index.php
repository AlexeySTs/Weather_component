<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
define("PUBLIC_AJAX_MODE", true);
CJSCore::Init(array('ajax'));
use Bitrix\Highloadblock\HighloadblockTable;
use Mcart\Weather\Tables\CitiesTable;
use Mcart\Weather\Helper;
use Bitrix\Main\Loader;
use Bitrix\Main\Type;

Loader::includeModule('mcart.weather');
Loader::includeModule('highloadblock');

$APPLICATION->SetTitle("Тест");
?><?
$id = 1;
$hlblock  = HighloadBlockTable::getList([
    "filter" => [
        "TABLE_NAME" => 'weather_mcart',
    ],
    "select" => ["ID","TABLE_NAME", "NAME"],
    "limit" => 1,
    "order" => [
        "NAME" => "ASC",
    ],
])->fetch();

$entity   = HighloadBlockTable::compileEntity( $hlblock ); //генерация класса для работы с данным ХЛ

$entityClass = $entity->getDataClass();
$listWeather = $entityClass::getList([
    "select" => ['UF_TEMPERATURE','UF_CLOUDINESS','UF_PRECIPITATION','UF_HUMIDITY', 'UF_PRESURE', 'UF_WIND_SPEED', 'UF_DIRECTION_WIND','UF_START_PERIOD','UF_END_PERIOD'],
    "filter" => [
        "UF_CITY_ID" => 1,
    ],
    "order" => [
        "UF_START_PERIOD" => "ASC",
    ]
]);
if($res = $listWeather->fetch()) {
    p($res);
    echo (11);
}

?>

 <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>