<?
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
use Bitrix\Highloadblock\HighloadblockTable;
use Bitrix\Main\Loader;
use Mcart\Weather\Helper;
use Mcart\Weather\Weather;

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

if (!Loader::includeModule('mcart.weather'))
{
	ShowError('Error not include mcart.weather module');
	return;
}

if (!Loader::includeModule('highloadblock'))
{
	ShowError('Error not include highloadblock module');
	return;
}

$APPLICATION->ShowAjaxHead();
?>
<div class="white-block">
<?
$request = \Bitrix\Main\Context::getCurrent()->getRequest();

if(!(!empty($request['city']) && !empty($request['city_fias_id']) && !empty($request['geo_lon']) && !empty($request['geo_lat']))) {
    echo 'Недостаточно данных';
    die();
}

echo '<h1>' . $request['city'] . '</h1>';

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

$params = [		
    'NAME' => $request['city'],
    'CITY_FIAS_ID' => $request['city_fias_id'],
    'LONGITUDE' => $request['geo_lon'],
    'LATITUDE' => $request['geo_lat'],
];

$weather_city =  Weather::getWeatherCity($params);

$rows = [];
foreach($weather_city as $elem) {
	$rows[] = [
		'data' => $elem,
	];
};

$columns = [ 
    ['id' => 'UF_START_PERIOD', 'name' => 'Начало периода', 'sort' => 'DATE', 'default' => true], 
    ['id' => 'UF_END_PERIOD', 'name' => 'Конец периода', 'sort' => 'DATE', 'default' => true], 
    ['id' => 'UF_CLOUDINESS', 'name' => 'Облачность', 'sort' => 'ID', 'default' => true], 
    ['id' => 'UF_DIRECTION_WIND', 'name' => 'Направление ветра', 'sort' => 'DATE', 'default' => true], 
    ['id' => 'UF_HUMIDITY', 'name' => 'Влажность', 'sort' => 'PAYER_INN', 'default' => true], 
    ['id' => 'UF_PRECIPITATION', 'name' => 'Осадки', 'sort' => 'PAYER_NAME', 'default' => true], 
    ['id' => 'UF_PRESURE', 'name' => 'Давление', 'sort' => 'IS_SPEND', 'default' => true], 
    ['id' => 'UF_TEMPERATURE', 'name' => 'Температура', 'sort' => 'IS_SPEND', 'default' => true], 
    ['id' => 'UF_WIND_SPEED', 'name' => 'Скорость ветра', 'sort' => 'IS_SPEND', 'default' => true], 

];
$APPLICATION->IncludeComponent(
    'bitrix:main.ui.grid',
    '',
    [
        'GRID_ID' => 'MY_GRID_ID',
        'COLUMNS' => $columns,
        'ROWS' => $rows,
        'AJAX_MODE' => 'Y',
        'AJAX_OPTION_JUMP' => 'N',
        'AJAX_OPTION_HISTORY' => 'N',
    ]
);
?> </div>