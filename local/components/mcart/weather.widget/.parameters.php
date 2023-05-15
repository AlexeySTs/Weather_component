<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/**
 * @var string $componentPath
 * @var string $componentName
 * @var array $arCurrentValues
 * @var array $templateProperties
 * @global CUserTypeManager $USER_FIELD_MANAGER
 */

use Bitrix\Main\Loader,
	Bitrix\Highloadblock\HighloadblockTable;

global $USER_FIELD_MANAGER;

if (!Loader::includeModule('highloadblock'))
	return;

$arComponentParameters = array(
	
	'PARAMETERS' => array(	
		'HLBLOCK_ID' => array(
			'PARENT' => 'BASE',
			'NAME' => GetMessage('MCART_HLBLOCK'),
			'TYPE' => 'STRING',
		),
		'CACHE_TIME' => array('DEFAULT' => 36000000),

	),
);
