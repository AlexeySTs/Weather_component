<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("ntcn 3");
?><?$APPLICATION->IncludeComponent(
	"bitrix:main.ui.grid",
	"tilegrid",
	Array(
		"AJAX_MODE" => "Y",
		"AJAX_OPTION_HISTORY" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"COLUMNS" => $columns,
		"GRID_ID" => "MY_GRID_ID",
		"ROWS" => $rows
	)
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>