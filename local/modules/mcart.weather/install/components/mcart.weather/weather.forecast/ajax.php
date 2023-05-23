<?php
use Mcart\Weather\Weather;
use Bitrix\Main\Loader;
CJSCore::Init(array('ajax'));



if (!Loader::includeModule('mcart.weather'))
{
	ShowError('Error not include mcart.weather module');
	return;
}
#components/bitrix/example/ajax.php

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

global $APPLICATION;

class WeatherWidgetAjaxController extends \Bitrix\Main\Engine\Controller
{
    #в параметр $person будут автоматически подставлены данные из REQUEST
   /*
    public function getCityWeatherAction($city_info)
    {   
        
        $info = json_decode($city_info, true);
        $params = [
            'NAME' => $info['data']['city'],
            'CITY_FIAS_ID' => $info['data']['city_fias_id'],
            'LONGITUDE' => $info['data']['geo_lon'],
            'LATITUDE' => $info['data']['geo_lat'],
        ];
        $result =  Weather::getWeatherCity($params);
        p2f('End Ajax');

        return $result;
    }

    public function getCityWeatherAction($city_info)
    {   
        global $APPLICATION;
        
        $info = json_decode($city_info, true);
        $params = [
            'NAME' => $info['data']['city'],
            'CITY_FIAS_ID' => $info['data']['city_fias_id'],
            'LONGITUDE' => $info['data']['geo_lon'],
            'LATITUDE' => $info['data']['geo_lat'],
        ];
        $result =  Weather::getWeatherCity($params);
        p2f('End Ajax');

        ob_start();

        $columns = [ 
            ['id' => 'UF_START_PERIOD', 'name' => 'Начало периода', 'sort' => 'DATE', 'default' => true], 
            ['id' => 'UF_END_PERIOD', 'name' => 'Конец периода', 'sort' => 'DATE', 'default' => true], 
            ['id' => 'UF_CLOUDINESS', 'name' => 'Облачность', 'sort' => 'ID', 'default' => true], 
            ['id' => 'UF_DIRECTION_WIND', 'name' => 'НАправление ветра', 'sort' => 'DATE', 'default' => true], 
            ['id' => 'UF_HUMIDITY', 'name' => 'Давление', 'sort' => 'PAYER_INN', 'default' => true], 
            ['id' => 'UF_PRECIPITATION', 'name' => 'Осадки', 'sort' => 'PAYER_NAME', 'default' => true], 
            ['id' => 'UF_PRESURE', 'name' => 'Влажность', 'sort' => 'IS_SPEND', 'default' => true], 
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

        $res = ob_get_clean();

        return $res;
    }

*/	
}
