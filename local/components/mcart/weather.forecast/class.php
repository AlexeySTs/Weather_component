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
            'NAME' => 'Иркутск',
            'CITY_FIAS_ID' => '8eeed222-72e7-47c3-ab3a-9a553c31cf72',
            'LONGITUDE' => '104.28066',
            'LATITUDE' => '52.286387',
        ];

		$weather_city =  Weather::getWeatherCity($params);
		
		foreach ($weather_city as $key=>$elem) {
			if ($elem['UF_PRECIPITATION'] == 0) {
				$weather_city[$key]['UF_PRECIPITATION'] = '-';
			} else {
				$weather_city[$key]['UF_PRECIPITATION'] = Helper::getUserEnum([
					'FIELD_NAME' => 'UF_PRECIPITATION',
					'ENTITY_ID' => "HLBLOCK_" . $hlblock['ID'],
					'ID' => 1,
					'RETURN' => $elem['UF_PRECIPITATION']
				]);
			}
		}

		foreach ($weather_city as $key=>$elem) {			
			$weather_city[$key]['UF_CLOUDINESS'] = Helper::getUserEnum([
					'FIELD_NAME' => 'UF_CLOUDINESS',
					'ENTITY_ID' => "HLBLOCK_" . $hlblock['ID'],
					'ID' => 1,
					'RETURN' => $elem['UF_CLOUDINESS']
			]);
		}

		$this->arResult['CITY'] = $weather_city;
		
		$this->IncludeComponentTemplate();
	}

	
}