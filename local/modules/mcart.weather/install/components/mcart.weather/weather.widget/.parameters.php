<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/**
 * @var string $componentPath
 * @var string $componentName
 * @var array $arCurrentValues
 * @var array $templateProperties
 * @global CUserTypeManager $USER_FIELD_MANAGER
 */

use Bitrix\Main\Loader;

global $USER_FIELD_MANAGER;

if (!Loader::includeModule('highloadblock'))
	return;

$arComponentParameters = array(
	'PARAMETERS' => array (
		'LINK_FORECAST' => array(
			'PARENT' => 'BASE',
			'NAME' => GetMessage('MCART_LINK_FORECAST'),
			'TYPE' => 'STRING',
		),
		'CACHE_TIME' => array('DEFAULT' => 36000000),
	)
);

