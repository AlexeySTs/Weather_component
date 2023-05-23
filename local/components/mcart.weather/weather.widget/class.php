<?

use Bitrix\Highloadblock\HighloadblockTable;
use Bitrix\Main\Loader;
use Mcart\Weather\Tables\CitiesTable;
use Mcart\Weather\Helper;
use Mcart\Weather\Weather;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/**
 * @global CUser $USER
 * @global CMain $APPLICATION
 * @global CIntranetToolbar $INTRANET_TOOLBAR
 */


if (!Loader::includeModule('highloadblock')) {
	ShowError('Error not include higtblock module');
	return;
}

if (!Loader::includeModule('mcart.weather')) {
	ShowError('Error not include mcart.weather module');
	return;
};

class WeatherWidgetComponent extends CBitrixComponent
{
	private function translateDirection()
	{
		$arrWind = [
			'С' => [338, 22],
			'СВ' => [23, 67],
			'В' => [68, 112],
			'ЮВ' => [113, 157],
			'Ю' => [158, 202],
			'ЮЗ' => [203, 247],
			'З' => [248, 292],
			'СЗ' => [293, 337],
		];
		$windDir = $this->arResult['UF_DIRECTION_WIND'];
		foreach ($arrWind as $key => $elem) {
			if (($windDir >= $elem[0] and $windDir <= $elem[1])) {
				return $key;
			}
		}
		return 'C';
	}

	public function executeComponent()
	{
		$city = CitiesTable::getList([
			'select' => ['ID', 'NAME'],
			'filter' => ['NAME' => COption::GetOptionString("mcart.weather", "city_name_default")]
		])->fetch();
		
		if (empty($city)) {
			$params = [
				'NAME' => \COption::GetOptionString("mcart.weather", "city_name_default"),
				'CITY_FIAS_ID' => \COption::GetOptionString("mcart.weather", "city_fias_id_default"),
				'LONGITUDE' => \COption::GetOptionString("mcart.weather", "city_long_default"),
				'LATITUDE' => \COption::GetOptionString("mcart.weather", "city_lat_default"),
			];
			Weather::getWeatherCity($params);
		}

		$hlblock  = HighloadBlockTable::getList([
			"filter" => [
				"TABLE_NAME" => 'weather_mcart',
			],
			"select" => ["ID", "TABLE_NAME", "NAME"],
			"limit" => 1,
			"order" => [
				"NAME" => "ASC",
			],
		])->fetch();

		$entity   = HighloadBlockTable::compileEntity($hlblock); //генерация класса для работы с данным ХЛ

		$entityClass = $entity->getDataClass();

		$this->arResult = $entityClass::getList([
			"select" => ['UF_TEMPERATURE', 'UF_CLOUDINESS', 'UF_PRECIPITATION', 'UF_HUMIDITY', 'UF_PRESURE', 'UF_WIND_SPEED', 'UF_DIRECTION_WIND'],
			"filter" => [
				"UF_CITY_ID" => $city['ID'],
				"<UF_START_PERIOD" => date('d.m.Y H:i:s'),
				">UF_END_PERIOD" => date('d.m.Y H:i:s')
			],
			"limit" => 1,

		])->fetch();

		$this->arResult['CITY_NAME'] = $city['NAME'];
		$this->arResult['LINK_FORECAST'] = $this->arParams['LINK_FORECAST'];
		if (!empty($this->arResult)) {

			$this->arResult['UF_CLOUDINESS'] = Helper::getUserEnum([
				'FIELD_NAME' => 'UF_CLOUDINESS',
				'ENTITY_ID' => "HLBLOCK_" . $hlblock['ID'],
				'ID' => 1,
				'RETURN' => $this->arResult['UF_CLOUDINESS']
			]);

			if (!empty($this->arResult['UF_PRECIPITATION'])) {

				$this->arResult['UF_PRECIPITATION'] = Helper::getUserEnum([
					'FIELD_NAME' => 'UF_PRECIPITATION',
					'ENTITY_ID' => "HLBLOCK_" . $hlblock['ID'],
					'ID' => 1,
					'RETURN' => $this->arResult['UF_PRECIPITATION']
				]);
			}

			if (!empty($this->arResult['UF_PRECIPITATION'])) {

				$this->arResult['UF_PRECIPITATION'] = Helper::getUserEnum([
					'FIELD_NAME' => 'UF_PRECIPITATION',
					'ENTITY_ID' => "HLBLOCK_" . $hlblock['ID'],
					'ID' => 1,
					'RETURN' => $this->arResult['UF_PRECIPITATION']
				]);
			}

			$this->arResult['UF_DIRECTION_WIND'] = $this->translateDirection();
		}

		$this->IncludeComponentTemplate();
	}
}
