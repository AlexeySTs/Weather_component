<?php
namespace Mcart\Weather;

use Bitrix\Highloadblock\HighloadblockTable;
use Bitrix\Main\Loader;
use Mcart\Weather\Tables\CitiesTable;
use Mcart\Weather\Helper;
use Bitrix\Main\Type;



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

class Weather 
{
    static private function translateDirection($direction) 
	{
		p2f('translateDirection - start');
		$arrWind = [
			'С' => [338,22],
			 'СВ' => [23,67],
			 'В' => [68,112],
			 'ЮВ' => [113,157],
			 'Ю' => [158,202],
			 'ЮЗ' => [203,247],
			 'З' => [248,292],
			 'СЗ' => [293,337],
		];
		foreach ($arrWind as $key=>$elem) {
			if (($direction >= $elem[0] AND $direction <= $elem[1])) {
				return $key;
			}
		}
		p2f('translateDirection - endStandart');
		return 'C';

	}

	// Преобразование слов из API в номер для списка пользовательского поля.
	static private function translatePrecipitation($prec, $hl_block_id)  
	{
		p2f('translatePrecipitation - start');
		$arInfo = [
			'Rain' => 'Дождь',
			'Show' => 'Снег',
			'Hail' => 'Град',
		];
		$list = Helper::getUserEnum([
			'FIELD_NAME' => 'UF_PRECIPITATION',
			'ENTITY_ID' => "HLBLOCK_".$hl_block_id,
		]);
		p2f($arInfo);
		foreach($arInfo as $key=>$elem) {
			if($prec == $key){
				return $list["$elem"];
			}
		}	
		p2f('translatePrecipitation - end');
		return 0;
	}
	
	// Преобразование процентов из API в номер для списка пользовательского поля.
	static private function translateCloud($procent,  $hl_block_id)  
	{
		p2f('translateCloud - start');
		$arrCloud = [
			'Ясно' => [0,33],
			'Переменная облачность' => [34,67],
			'Облачно' => [68,100],	 
		];
		$res = '';
		foreach ($arrCloud as $key=>$elem) {
			if (($procent >= $elem[0] AND $procent <= $elem[1])) {
				$res = $key;
			}
		}
		$result = Helper::getUserEnum([
			'FIELD_NAME' => 'UF_CLOUDINESS',
			'ENTITY_ID' => "HLBLOCK_".$hl_block_id,
		]);
		p2f($result);
		foreach($result as $key=>$elem) {
			if($res == $key){
				return($elem);
			}
		}
		p2f('translateCloud - end');
	}

	// Добавление данных в таблицу HL блока с ID города
	static private function fillHLBlockWeatherByCity($params, $city_id)  
	{
		p2f('fillHLBlockWeatherByCity - start');
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

		$entity   = HighloadBlockTable::compileEntity($hlblock); //генерация класса для работы с данным 
		$entityClass = $entity->getDataClass();


		$lat = $params['LATITUDE']; // Кординаты города
		$lot = $params['LONGITUDE']; // Кординаты города
		$app_id = \COption::GetOptionString("mcart.weather", "api_openweathermap"); //'ae3ef61aecebb076a23df15e87a9dd24'; API ключ
		$url = 'https://api.openweathermap.org/data/2.5/forecast?lat='. $lat .'&lon=' . $lot . '&units=metric&lang=ru&appid='. $app_id;
		$curl = curl_init();

		curl_setopt ($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 3);
		p2f('fillHLBlockWeatherByCity - start CURL');
		p2f($url);
		$res = curl_exec($curl);
		p2f('fillHLBlockWeatherByCity - end CURL');

		if($res === false) {
			CitiesTable::delete($city_id);
			ShowError(curl_error($curl));
			return;
		}

		$info = json_decode($res, true); // Результат запроса в массиве.

		p2f('fillHLBlockWeatherByCity - getinfo');
		foreach($info['list'] as $elem) {
			
			$data = array(
				'UF_TEMPERATURE' => round($elem['main']['temp']),
				'UF_CLOUDINESS' => self::translateCloud($elem['clouds']['all'], $hlblock['ID']),
				'UF_PRECIPITATION'=> self::translatePrecipitation($elem['weather']['0']['main'], $hlblock['ID']),
				'UF_HUMIDITY'=> $elem['main']['humidity'],
				'UF_PRESURE'=> $elem['main']['pressure'], 
				'UF_WIND_SPEED'=> round($elem['wind']['speed']), 
				'UF_DIRECTION_WIND'=> $elem['wind']['deg'],
				'UF_START_PERIOD' => date('d.m.Y H:i:s',$elem['dt']-10800),
				'UF_END_PERIOD' => date('d.m.Y H:i:s',$elem['dt']),
				'UF_CITY_ID' => $city_id,
			 );
		
			 $result = $entityClass::add($data);
		}
		p2f('fillHLBlockWeatherByCity - end add info');
	}
	//Заполнение данных по параметрам города 
	static public function getWeatherCity($params) 
	{
		if ($weatherCity = CitiesTable::getList([
			'select' => ['NAME', 'ID', 'LONGITUDE', 'LATITUDE','CITY_FIAS_ID', 'LAST_USE'],
			'filter' => ['CITY_FIAS_ID' => $params['CITY_FIAS_ID']],
			'limit' => 1,
		
		])->fetch()) {
			p2f('getWeatherCity - CashCity');
			CitiesTable::update($weatherCity['ID'], [
				'LAST_USE' => new Type\DateTime(),
			]);
			return self::getWeatherByID($weatherCity['ID']);
		} else {
			$result = CitiesTable::add([
				'NAME' => $params['NAME'],
				'LONGITUDE' => $params['LONGITUDE'],
				'LATITUDE' => $params['LATITUDE'],
				'CITY_FIAS_ID' => $params['CITY_FIAS_ID'],
				'LAST_USE' => new Type\DateTime(),
			]);
			if ($result->isSuccess())
			{
				$city_id = $result->getId();
				p2f('getWeatherCity - addCity');
				self::fillHLBlockWeatherByCity($params, $city_id);
				p2f('getWeatherCity - end add info');
                return self::getWeatherByID($city_id);
			} else {
				ShowError('Не удалось создать объект в таблице City Mcart');
				die();
			}
		}
	}

    static private function getWeatherByID($id) 
    {
		p2f('getWeatherByID - start');
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
                "UF_CITY_ID" => $id,
            ],
            "order" => [
                "UF_START_PERIOD" => "ASC",
            ]
        ]);
        p2f('getWeatherByID - while start');
        p2f($id);
        while($query = $listWeather->fetch()){
			p2f('getWeatherByID - IN while start');
            $query['UF_DIRECTION_WIND'] = self::translateDirection($query['UF_DIRECTION_WIND']);
            $query['UF_CLOUDINESS'] = Helper::getUserEnum([
				'FIELD_NAME' => 'UF_CLOUDINESS',
				'ENTITY_ID' => "HLBLOCK_" . $hlblock['ID'],
				'ID' => 1,
				'RETURN' => $query['UF_CLOUDINESS']
			]);
			if ($query['UF_PRECIPITATION'] == 0) {
				$query['UF_PRECIPITATION'] = '-';
			} else {
				$query['UF_PRECIPITATION'] = Helper::getUserEnum([
					'FIELD_NAME' => 'UF_PRECIPITATION',
					'ENTITY_ID' => "HLBLOCK_" . $hlblock['ID'],
					'ID' => 1,
					'RETURN' => $query['UF_PRECIPITATION']
				]);
			}
            $result[] = $query;
			p2f('getWeatherByID - IN while end');
        };
        //p2f($result);
		p2f('getWeatherByID - end');
        return $result;
        

    }
}