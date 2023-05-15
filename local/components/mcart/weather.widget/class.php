<?
use Bitrix\Highloadblock\HighloadblockTable;
use Bitrix\Main\Loader;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/**
 * @global CUser $USER
 * @global CMain $APPLICATION
 * @global CIntranetToolbar $INTRANET_TOOLBAR
 */


if (!\Bitrix\Main\Loader::includeModule('highloadblock'))
{
	ShowError('Error not include higtblock module');
	return;
}

class WeatherWidgetComponent extends CBitrixComponent
{

	private function translateDirection() 
	{
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
		$windDir = $this->arResult['UF_DIRECTION_WIND'];
		foreach ($arrWind as $key=>$elem) {
			if (($windDir >= $elem[0] AND $windDir <= $elem[1])) {
				return $key;
			}
		}
		return 'C';

	}
	

	public function executeComponent ()
	{
		$hlblock  = HighloadBlockTable::getList([
			"filter" => [
				"ID" => $this->arParams['HLBLOCK_ID'],
			],
			"select" => ["ID","TABLE_NAME", "NAME"],
			"limit" => 1,
			"order" => [
				"NAME" => "ASC",
			],
		])->fetch();

		$entity   = HighloadBlockTable::compileEntity( $hlblock ); //генерация класса для работы с данным ХЛ
		
		$entityClass = $entity->getDataClass();
		
		$this->arResult = $entityClass::getList([
			"select" => ['UF_TEMPERATURE','UF_CLOUDINESS','UF_PRECIPITATION','UF_HUMIDITY', 'UF_PRESURE', 'UF_WIND_SPEED', 'UF_DIRECTION_WIND'],
			"filter" => [
				"<UF_START_PERIOD" => date('d.m.Y H:i:s'), 
				">UF_END_PERIOD" => date('d.m.Y H:i:s')
			],
			"limit" => 1,
			"order" => [
				"ID" => "DESC",
			]
		])->fetch();
		
		if(!empty($this->arResult)) {
			 
			$this->arResult['UF_CLOUDINESS'] = getUserEnum([
				'FIELD_NAME' => 'UF_CLOUDINESS',
				'ENTITY_ID' => "HLBLOCK_" . $hlblock['ID'],
				'ID' => 1,
				'RETURN' => $this->arResult['UF_CLOUDINESS']
			]);
			
			if(!empty($this->arResult['UF_PRECIPITATION'])) {

				$this->arResult['UF_PRECIPITATION'] = getUserEnum([
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