<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();?>
<input id="address" name="address" type="text" />
<?
use Bitrix\Main\Page\Asset;
Asset::getInstance()->addCss($templateFolder. "/css/suggestions.min.css");
Asset::getInstance()->addJs($templateFolder. "/js/jquery.min.js");
Asset::getInstance()->addJs($templateFolder. "/js/jquery.suggestions.min.js");
Asset::getInstance()->addJs($templateFolder. "/js/script.js");

//p($arResult);
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
$rows = [];
foreach($arResult['CITY'] as $elem) {
	$rows[] = [
		'data' => $elem,
	];
};
	/*$rows = [
		[
		'data'  => [ 
			"ID" => 0,
			"NAME" => "Название 1",
			"AMOUNT" => 1000,
			"DATE" => "DATE 1",
			"PAYER_INN" => "PAYER_INN 1",
			"IS_SPEND" => "IS_SPEND 1",
			],
		],
		[
			'data' => [ 
				"ID" => 2,
				"NAME" => "Название 1",
				"AMOUNT" => 111,
				"DATE" => "DATE 1",
				"PAYER_INN" => "PAYER_INN 1",
				"IS_SPEND" => "IS_SPEND 1",
	
					],
							
			],
	];*/
		

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
<div class="weather">
	
    <span class="temperature">
		<?if($arResult['UF_TEMPERATURE'] > 0):?>+<?endif?><?=$arResult['UF_TEMPERATURE']?><span class ="grad">&degC</span></span>
	</span>
	<img class="img_weather" src="<?=$templateFolder?>/images/<?=$arResult['UF_CLOUDINESS']?>.svg" alt=""><br>
    <span class="cloudiness"><?=$arResult['UF_CLOUDINESS']?></span><br>
    <span class="humiduty"><?=Getmessage('PRESURE')?>: <?=$arResult['UF_PRESURE']?> <?=Getmessage('PRESURE_CHAR')?> .,</span><br>
    <span class="pressure">
		<?=Getmessage('HUMIDITY')?>: <?=$arResult['UF_HUMIDITY']?>%, 
		<?=Getmessage('WIND')?>: <?=$arResult['UF_WIND_SPEED']?> <?=Getmessage('WIND_CHAR')?>,
		↙ <?=$arResult['UF_DIRECTION_WIND']?>
	</span><br>
	<?if(!empty($arResult['UF_PRECIPITATION'])):?>
		<span class="humiduty"><?=Getmessage('PRECIPITATION')?>: <?=$arResult['UF_PRECIPITATION']?></span><br>
	<?endif?>
</div>
<?
?>