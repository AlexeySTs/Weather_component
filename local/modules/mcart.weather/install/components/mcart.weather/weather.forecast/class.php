<?
use Bitrix\Highloadblock\HighloadblockTable;
use Bitrix\Main\Loader;
use Mcart\Weather\Tables\CitiesTable;
use Mcart\Weather\Helper;
use Bitrix\Main\Type;
use Mcart\Weather\Weather;


if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/**
 * @global CUser $USER
 * @global CMain $APPLICATION
 * @global CIntranetToolbar $INTRANET_TOOLBAR
 */


if (!Loader::includeModule('highloadblock'))
{
	ShowError('Error not include highloadblock module');
	return;
}

if (!Loader::includeModule('mcart.weather'))
{
	ShowError('Error not include mcart.weather module');
	return;
}

class WeatherForecastComponent extends CBitrixComponent
{
	public function executeComponent ()
	{
		$request = \Bitrix\Main\Context::getCurrent()->getRequest();
		
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
            'NAME' => \COption::GetOptionString("mcart.weather", "city_name_default"),
            'CITY_FIAS_ID' => \COption::GetOptionString("mcart.weather", "city_fias_id_default"),
            'LONGITUDE' => \COption::GetOptionString("mcart.weather", "city_long_default"),
            'LATITUDE' => \COption::GetOptionString("mcart.weather", "city_lat_default"),
        ];

		$weather_city =  Weather::getWeatherCity($params);
		$this->arResult['CITY_NAME'] = $params['NAME'];
		$this->arResult['CITY'] = $weather_city;
		
		$this->IncludeComponentTemplate();
	}

	
}