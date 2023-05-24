<?
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

use Bitrix\Main\Loader;
use Mcart\Weather\Weather;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

if (!Loader::includeModule('mcart.weather')) {
    ShowError('Error not include mcart.weather module');
    return;
}

if (!Loader::includeModule('highloadblock')) {
    ShowError('Error not include highloadblock module');
    return;
}

$APPLICATION->ShowAjaxHead();
?>
<div class="white-block">
    <?
    $request = \Bitrix\Main\Context::getCurrent()->getRequest();

    if (!(!empty($request['city']) && !empty($request['city_fias_id']) && !empty($request['geo_lon']) && !empty($request['geo_lat']))) {
        echo 'Недостаточно данных';
        die();
    }
    p2f($request['city']);
    echo '<h1>' . $request['city'] . '</h1>';

    $weather_city =  Weather::getWeatherForAjax([
        'NAME' => $request['city'],
        'CITY_FIAS_ID' => $request['city_fias_id'],
        'LONGITUDE' => $request['geo_lon'],
        'LATITUDE' => $request['geo_lat'],
    ]);

    p2f($weather_city);

    $APPLICATION->IncludeComponent(
        'bitrix:main.ui.grid',
        '',
        [
            'GRID_ID' => 'MY_GRID_ID',
            'COLUMNS' => $weather_city['COLUMS'],
            'ROWS' => $weather_city['ROWS'],
            'AJAX_MODE' => 'Y',
            'AJAX_OPTION_JUMP' => 'N',
            'AJAX_OPTION_HISTORY' => 'N',
        ]
    ); ?>
</div>