<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();?>
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