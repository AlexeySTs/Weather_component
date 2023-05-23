<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("MCART_WEATHER_FORECAST_COMPONENT_NAME"),
	"DESCRIPTION" => GetMessage("MCART_WEATHER_FORECAST_COMPONENT_DESCRIPTION"),
	"ICON" => "/images/cat_list.gif",
	"CACHE_PATH" => "Y",
	"SORT" => 30,
	"PATH" => array(
		"ID" => "content",
		"CHILD" => array(
			"ID" => "mcart_weather",
			"NAME" => GetMessage("MCART_WEATHER_FORECAST_COMPONENT_NAME"),
			
		),
	),
);

?>