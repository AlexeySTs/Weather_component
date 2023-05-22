<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Тест 2");
?><?$APPLICATION->IncludeComponent(
	"mcart:weather.forecast", 
	".default", 
	array(
		"COMPONENT_TEMPLATE" => ".default",
		"HLBLOCK_ID" => "1",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "36000000"
	),
	false
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>