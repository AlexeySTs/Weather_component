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

class WeatherWidgetAjaxController extends \Bitrix\Main\Engine\Controller
{
    #в параметр $person будут автоматически подставлены данные из REQUEST
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
	
}