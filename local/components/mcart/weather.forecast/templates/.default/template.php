<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();?>

<?
use Bitrix\Main\Page\Asset;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

Asset::getInstance()->addCss($templateFolder. "/css/suggestions.min.css");
Asset::getInstance()->addJs($templateFolder. "/js/jquery.min.js");
Asset::getInstance()->addJs($templateFolder. "/js/jquery.suggestions.min.js");
Asset::getInstance()->addJs($templateFolder. "/js/script.js");?>

<p><?=Loc::getMessage("SELECT_CITY")?></p>
<input id="address" name="address" type="text" />
<?php
	$columns = [ 
		['id' => 'UF_START_PERIOD', 'name' => Loc::getMessage("START_PERIOD"), 'sort' => 'DATE', 'default' => true], 
        ['id' => 'UF_END_PERIOD', 'name' => Loc::getMessage("END_PERIOD"), 'sort' => 'DATE', 'default' => true], 
        ['id' => 'UF_CLOUDINESS', 'name' => Loc::getMessage("CLOUDINESS"), 'sort' => 'ID', 'default' => true], 
        ['id' => 'UF_DIRECTION_WIND', 'name' => Loc::getMessage("WIND"), 'sort' => 'DATE', 'default' => true], 
        ['id' => 'UF_HUMIDITY', 'name' => Loc::getMessage("PRESURE"), 'sort' => 'PAYER_INN', 'default' => true], 
        ['id' => 'UF_PRECIPITATION', 'name' => Loc::getMessage("PRECIPITATION"), 'sort' => 'PAYER_NAME', 'default' => true], 
        ['id' => 'UF_PRESURE', 'name' => Loc::getMessage("HUMIDITY"), 'sort' => 'IS_SPEND', 'default' => true], 
        ['id' => 'UF_TEMPERATURE', 'name' => Loc::getMessage("TEMPERATURE"), 'sort' => 'IS_SPEND', 'default' => true], 
        ['id' => 'UF_WIND_SPEED', 'name' => Loc::getMessage("WIND_SPEED_CHAR"), 'sort' => 'IS_SPEND', 'default' => true], 
    ];
$rows = [];
foreach($arResult['CITY'] as $elem) {
	$rows[] = [
		'data' => $elem,
	];
}?>
<div class="weather_foreast">
	<h1><?=$arResult['CITY_NAME']?></h1>
	<?	
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
	?>	
</div>
<?
?>