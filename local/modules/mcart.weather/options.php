<?
use Bitrix\Main\Localization\Loc;
use    Bitrix\Main\HttpApplication;
use Bitrix\Main\Loader;
use Bitrix\Main\Config\Option;

Loc::loadMessages(__FILE__);

$request = HttpApplication::getInstance()->getContext()->getRequest();

$module_id = htmlspecialcharsbx($request["mid"] != "" ? $request["mid"] : $request["id"]);

Loader::includeModule($module_id);
$arAllOptions[]  =  array('param_name',  "Параметр:",  '',  array('text',  20));  
$arAllOptions[]  =  array('param_name',  "Параметр:",  'значение',  array('text',  
20),  '',  'текст');  
$arAllOptions[]  =  array('param_name',  "Параметр  (только  чтение):",  '',  
array('text',  20),  'Y');  
$arAllOptions[]  =  array('param_name',  "Параметр  (пароль):",  '111111',  
array('password',  20));
?>