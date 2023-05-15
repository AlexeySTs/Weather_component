<?php 
$_SERVER["DOCUMENT_ROOT"] = '/home/bitrix/www'; //realpath(dirname(__FILE__) . "/. ./. ./../..");
$DOCUMENT_ROOT = $_SERVER["DOCUMENT_ROOT"];

define("NO_KEEP_STATISTIC", true); // Несобирать статистику
define("NOT_CHECK_PERMISSIONS",true); // Не проверять авторизацию
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");





use Bitrix\Highloadblock\HighloadblockTable;
use Bitrix\Main\Loader;

Loader::includeModule('highloadblock');

$lat = '55.45'; // Кординаты города
$lot = '65.33333'; // Кординаты города
$app_id = 'ae3ef61aecebb076a23df15e87a9dd24'; // API ключ
$url = 'https://api.openweathermap.org/data/2.5/forecast?lat='. $lat .'&lon=' . $lot . '&units=metric&lang=ru&appid='. $app_id;
$curl = curl_init();
$hlblockId = '1'; // id HL блока

curl_setopt ($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
$res = curl_exec($curl);

$info = json_decode($res, true); // Результат запроса в массиве.

function translateCloud($procent)  // Преобразование процентов из API в номер для списка пользовательского поля.
{
    $arrCloud = [
        '1' => [0,33],
        '3' => [34,67],
        '2' => [68,100],	 
    ];
    foreach ($arrCloud as $key=>$elem) {
        if (($procent >= $elem[0] AND $procent <= $elem[1])) {
            return $key;
        }
    }
    
    return '1';

}

function translatePrecipitation($prec)  // Преобразование слов из API в номер для списка пользовательского поля.
{
    $arrPrecipipitation = [
        '4' => 'Rain',
        '6' => 'Hail',
        '5' => 'Snow',	 
    ];
    foreach ($arrPrecipipitation as $key=>$elem) {
        if ($prec == $elem) {
            return $key;
        }
    }
    
    return 0;

}

$hlblock  = HighloadBlockTable::getList([
    "filter" => [
        "ID" => $hlblockId,
    ],
    "select" => ["ID","TABLE_NAME", "NAME"],
    "limit" => 1,
    "order" => [
        "NAME" => "ASC",
    ],
])->fetch();




$entity   = HighloadBlockTable::compileEntity( $hlblock ); //генерация класса для работы с данным 
$entityClass = $entity->getDataClass();

$connection = \Bitrix\Main\Application::getConnection();
$connection->truncateTable($hlblock["TABLE_NAME"]);

for($id = 0; $id <= 8; $id++) {
    $idNext = $id + 1;
    
    $data = array(
        'UF_TEMPERATURE' => round($info['list']["$id"]['main']['temp']),
        'UF_CLOUDINESS' => translateCloud($info['list']["$id"]['clouds']['all']),
        'UF_PRECIPITATION'=> translatePrecipitation($info['list']["$id"]['weather']['0']['main']),
        'UF_HUMIDITY'=> $info['list']["$id"]['main']['humidity'],
        'UF_PRESURE'=> $info['list']["$id"]['main']['pressure'], 
        'UF_WIND_SPEED'=> round($info['list']["$id"]['wind']['speed']), 
        'UF_DIRECTION_WIND'=> $info['list']["$id"]['wind']['deg'],
        'UF_START_PERIOD' => date('d.m.Y H:i:s',$info['list']["$id"]['dt']-10800),
        'UF_END_PERIOD' => date('d.m.Y H:i:s',$info['list']["$idNext"]['dt']-10800),
     );

     $result = $entityClass::add($data);
}


?>
