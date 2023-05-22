<?
    use Bitrix\Main\Localization\Loc;
    use Bitrix\Main\ModuleManager;
    use Bitrix\Main\Config\Option;
    use Bitrix\Main\EventManager;
    use Bitrix\Main\Application;
    use Bitrix\Main\IO\Directory;
    use Bitrix\Main\Loader;
    use Bitrix\Main\Entity\Base;
    use Mcart\Weather\Tables\CitiesTable;
    use Bitrix\Highloadblock;
    use Bitrix\Highloadblock\HighloadblockTable;

    Loc::loadMessages(__FILE__);

    class mcart_weather extends CModule
    {
        public function __construct()
        {
            if(file_exists(__DIR__ . "/version.php")) {
                
                include_once(__DIR__."/version.php");

                $this->MODULE_ID            = str_replace("_", ".", get_class($this));
                $this->MODULE_VERSION       = $arModuleVersion["VERSION"];
                $this->MODULE_VERSION_DATE  = $arModuleVersion["VERSION_DATE"];
                $this->MODULE_NAME          = Loc::getMessage("MCART_WEATHER_NAME");
                $this->MODULE_DESCRIPTION   = Loc::getMessage("MCART_WEATHER_DESCRIPTION");
                $this->PARTNER_NAME         = Loc::getMessage("MCART_WEATHER_PARTNER_NAME");
                $this->PARTNER_URI          = Loc::getMessage("MCART_WEATHER_PARTNER_URI");
            }

            return false;

        }

        public function DoInstall()
        {
            global $APPLICATION;

            ModuleManager::registerModule($this->MODULE_ID);

            $this->InstallFiles();
            $this->InstallDB();   
            $this->installHlBlockWeather();   
        }

        public function installDB()
        {
            if (Loader::includeModule($this->MODULE_ID)) {
                CitiesTable::getEntity()->createDbTable();
            }                 
        }

        public function uninstallDB()
        {
           if (Loader::includeModule($this->MODULE_ID)) {

                if (Application::getConnection()->isTableExists(Base::getInstance('Mcart\Weather\Tables\CitiesTable')->getDBTableName())) {
                    $connection = Application::getInstance()->getConnection();
                    $connection->dropTable(CitiesTable::getTableName());
                }
           }
        }

        public function DoUninstall()
        {
            global $APPLICATION;
            $this->uninstallDB();
            $this->uninstallHlBlockWeather(); 
            UnRegisterModule($this->MODULE_ID);
        }
        
        private function uninstallHlBlockWeather() 
        {
            if(!Loader::IncludeModule('highloadblock')) {
                $APPLICATION->ThrowException(Loc::getMessage("MCART_WEATHER_HL_BLOCK_MODULE_ERROR"));
            }

            $hlblock = HighloadBlockTable::getList([
    
                "select" => ["ID","TABLE_NAME", "NAME"],
                "filter" => ["TABLE_NAME" => "weather_mcart"],
                "order" => [
                    "NAME" => "ASC",
                ],
            ])->fetch();
            
            $userFields = CUserTypeEntity::GetList(
                ['ID'=>'ASC'],
                ['ENTITY_ID' => 'HLBLOCK_'. $hlblock['ID']]
            );
            
            while($item = $userFields->Fetch()) {
                $arRes[] = $item;
            }
            
            $UserTypeEntity = new CUserTypeEntity();
            foreach($arRes as $elem) {
                $UserTypeEntity->delete($elem['ID']);
            }
            Highloadblock\HighloadBlockTable::delete($hlblock['ID']);
        }

        private function installHlBlockWeather() 
        {
            global $APPLICATION;
            global $USER_FIELD_MANAGER;

            if(!Loader::IncludeModule('highloadblock')) {
                $APPLICATION->ThrowException(Loc::getMessage("MCART_WEATHER_HL_BLOCK_MODULE_ERROR"));
            }
        
            $arLangs = Array(
                'ru' => 'Погода Mcart',
                'en' => 'Weather Mcart'
            );
            
            $result = Highloadblock\HighloadBlockTable::add(array(
                'NAME' => 'WeatherMcart',
                'TABLE_NAME' => 'weather_mcart', 
            ));
            p2f($result->getErrorMessages());
            if (!$result->isSuccess()) {
                p2f($APPLICATION->ThrowException($result->getErrorMessages()));
            } 
            $hl_id = $result->getId();

            foreach($arLangs as $lang_key => $lang_val){
                Highloadblock\HighloadBlockLangTable::add(array(
                    'ID' => $hl_id,
                    'LID' => $lang_key,
                    'NAME' => $lang_val
                ));
            }

            $UFObject = 'HLBLOCK_'.$hl_id;

            // Формирование списка полей для ХЛ блока
            $arCartFields = Array(
                'UF_TEMPERATURE'=>Array(
                    'ENTITY_ID' => $UFObject,
                    'FIELD_NAME' => 'UF_TEMPERATURE',
                    'USER_TYPE_ID' => 'integer',
                    'MANDATORY' => 'N',
                    "EDIT_FORM_LABEL" => Array('ru'=>'Температура', 'en'=>'Temperature'), 
                    "LIST_COLUMN_LABEL" => Array('ru'=>'Температура', 'en'=>'Temperature'),
                    "LIST_FILTER_LABEL" => Array('ru'=>'Температура', 'en'=>'Temperature'), 
                    "ERROR_MESSAGE" => Array('ru'=>'', 'en'=>''), 
                    "HELP_MESSAGE" => Array('ru'=>'', 'en'=>''),
                ),
                'UF_CLOUDINESS'=>Array(
                    'ENTITY_ID' => $UFObject,
                    'FIELD_NAME' => 'UF_CLOUDINESS',
                    'USER_TYPE_ID' => 'enumeration',
                    'MANDATORY' => 'Y',
                    "EDIT_FORM_LABEL" => Array('ru'=>'Облачность', 'en'=>'Clouediness'), 
                    "LIST_COLUMN_LABEL" => Array('ru'=>'Облачность', 'en'=>'Clouediness'),
                    "LIST_FILTER_LABEL" => Array('ru'=>'Облачность', 'en'=>'Clouediness'), 
                    "ERROR_MESSAGE" => Array('ru'=>'', 'en'=>''), 
                    "HELP_MESSAGE" => Array('ru'=>'', 'en'=>''),
                ),
                'UF_PRECIPITATION'=>Array(
                    'ENTITY_ID' => $UFObject,
                    'FIELD_NAME' => 'UF_PRECIPITATION',
                    'USER_TYPE_ID' => 'enumeration',
                    'MANDATORY' => 'N',
                    "EDIT_FORM_LABEL" => Array('ru'=>'Осадки', 'en'=>'Precipitation'), 
                    "LIST_COLUMN_LABEL" => Array('ru'=>'Осадки', 'en'=>'Precipitation'),
                    "LIST_FILTER_LABEL" => Array('ru'=>'Осадки', 'en'=>'Precipitation'), 
                    "ERROR_MESSAGE" => Array('ru'=>'', 'en'=>''), 
                    "HELP_MESSAGE" => Array('ru'=>'', 'en'=>''),
                ),
                'UF_HUMIDITY'=>Array(
                    'ENTITY_ID' => $UFObject,
                    'FIELD_NAME' => 'UF_HUMIDITY',
                    'USER_TYPE_ID' => 'integer',
                    'MANDATORY' => 'N',
                    "EDIT_FORM_LABEL" => Array('ru'=>'Влажность', 'en'=>'Humidity'), 
                    "LIST_COLUMN_LABEL" => Array('ru'=>'Влажность', 'en'=>'Humidity'),
                    "LIST_FILTER_LABEL" => Array('ru'=>'Влажность', 'en'=>'Humidity'), 
                    "ERROR_MESSAGE" => Array('ru'=>'', 'en'=>''), 
                    "HELP_MESSAGE" => Array('ru'=>'', 'en'=>''),
                ),
                'UF_PRESURE'=>Array(
                    'ENTITY_ID' => $UFObject,
                    'FIELD_NAME' => 'UF_PRESURE',
                    'USER_TYPE_ID' => 'integer',
                    'MANDATORY' => 'N',
                    "EDIT_FORM_LABEL" => Array('ru'=>'Давление', 'en'=>'Preasure'), 
                    "LIST_COLUMN_LABEL" => Array('ru'=>'Давление', 'en'=>'Preasure'),
                    "LIST_FILTER_LABEL" => Array('ru'=>'Давление', 'en'=>'Preasure'), 
                    "ERROR_MESSAGE" => Array('ru'=>'', 'en'=>''), 
                    "HELP_MESSAGE" => Array('ru'=>'', 'en'=>''),
                ),
                'UF_WIND_SPEED'=>Array(
                    'ENTITY_ID' => $UFObject,
                    'FIELD_NAME' => 'UF_WIND_SPEED',
                    'USER_TYPE_ID' => 'integer',
                    'MANDATORY' => 'N',
                    "EDIT_FORM_LABEL" => Array('ru'=>'Скорость ветра', 'en'=>'Wind speed'), 
                    "LIST_COLUMN_LABEL" => Array('ru'=>'Скорость ветра', 'en'=>'Wind speed'),
                    "LIST_FILTER_LABEL" => Array('ru'=>'Скорость ветра', 'en'=>'Wind speed'), 
                    "ERROR_MESSAGE" => Array('ru'=>'', 'en'=>''), 
                    "HELP_MESSAGE" => Array('ru'=>'', 'en'=>''),
                ),
                'UF_DIRECTION_WIND'=>Array(
                    'ENTITY_ID' => $UFObject,
                    'FIELD_NAME' => 'UF_DIRECTION_WIND',
                    'USER_TYPE_ID' => 'integer',
                    'MANDATORY' => 'N',
                    "EDIT_FORM_LABEL" => Array('ru'=>'Направление ветра', 'en'=>'Direction speed'), 
                    "LIST_COLUMN_LABEL" => Array('ru'=>'Направление ветра', 'en'=>'Direction speed'),
                    "LIST_FILTER_LABEL" => Array('ru'=>'Направление ветра', 'en'=>'Direction speed'), 
                    "ERROR_MESSAGE" => Array('ru'=>'', 'en'=>''), 
                    "HELP_MESSAGE" => Array('ru'=>'', 'en'=>''),
                ),
                'UF_START_PERIOD'=>Array(
                    'ENTITY_ID' => $UFObject,
                    'FIELD_NAME' => 'UF_START_PERIOD',
                    'USER_TYPE_ID' => 'datetime',
                    'MANDATORY' => 'N',
                    "EDIT_FORM_LABEL" => Array('ru'=>'Начало периода', 'en'=>'Start period'), 
                    "LIST_COLUMN_LABEL" => Array('ru'=>'Начало периода', 'en'=>'Start period'),
                    "LIST_FILTER_LABEL" => Array('ru'=>'Начало периода', 'en'=>'Start period'), 
                    "ERROR_MESSAGE" => Array('ru'=>'', 'en'=>''), 
                    "HELP_MESSAGE" => Array('ru'=>'', 'en'=>''),
                ),
                'UF_END_PERIOD'=>Array(
                    'ENTITY_ID' => $UFObject,
                    'FIELD_NAME' => 'UF_END_PERIOD',
                    'USER_TYPE_ID' => 'datetime',
                    'MANDATORY' => 'N',
                    "EDIT_FORM_LABEL" => Array('ru'=>'Конец периода', 'en'=>'End period'), 
                    "LIST_COLUMN_LABEL" => Array('ru'=>'Конец периода', 'en'=>'End period'),
                    "LIST_FILTER_LABEL" => Array('ru'=>'Конец периода', 'en'=>'End period'), 
                    "ERROR_MESSAGE" => Array('ru'=>'', 'en'=>''), 
                    "HELP_MESSAGE" => Array('ru'=>'', 'en'=>''),
                ),
                'UF_CITY_ID'=>Array(
                    'ENTITY_ID' => $UFObject,
                    'FIELD_NAME' => 'UF_CITY_ID',
                    'USER_TYPE_ID' => 'integer',
                    'MANDATORY' => 'N',
                    "EDIT_FORM_LABEL" => Array('ru'=>'ID города из таблицы городов', 'en'=>'ID cities'), 
                    "LIST_COLUMN_LABEL" => Array('ru'=>'ID города из таблицы городов', 'en'=>'ID cities'),
                    "LIST_FILTER_LABEL" => Array('ru'=>'ID города из таблицы городов', 'en'=>'ID cities'), 
                    "ERROR_MESSAGE" => Array('ru'=>'', 'en'=>''), 
                    "HELP_MESSAGE" => Array('ru'=>'', 'en'=>''),
                ),

                
            );

                
                // Перебор массива со списком полей и добавление в пользовательские поля
                $arSavedFieldsRes = Array();
                foreach($arCartFields as $arCartField){
                    $obUserField  = new CUserTypeEntity();
                    $filed_Id = $obUserField->Add($arCartField);
                    $arSavedFieldsRes[] = $filed_Id;
                };
                
                // Параметры для пользовательских полей типа "список" по ИД ХЛ блока
                $arFields = $USER_FIELD_MANAGER->GetUserFields("HLBLOCK_" . $hl_id);
                
                // Заполнение пользовательского поля типа "список" для списка облачность
                if(array_key_exists("UF_CLOUDINESS", $arFields)) 
                {              
                    $user_filed_Id = $arFields["UF_CLOUDINESS"]["ID"];
                    $obEnum = new CUserFieldEnum();
                    $obEnum->SetEnumValues($user_filed_Id, array(
                        "n0" => array(
                            "VALUE" => "Ясно",
                            "DEF" => "Y",
                        ),
                        "n1" => array(
                            "VALUE" => "Переменная облачность",
                        ),
                        "n2" => array(
                            "VALUE" => "Облачно",
                        ),
                        
                    ));
                };

                // Заполнение пользовательского поля типа "список" для списка осадки
                if(array_key_exists("UF_PRECIPITATION", $arFields))
                {              
                    $user_filed_Id = $arFields["UF_PRECIPITATION"]["ID"];
                    $obEnum = new CUserFieldEnum();
                    $obEnum->SetEnumValues($user_filed_Id, array(
                        "n0" => array(
                            "VALUE" => "Дождь",
                        ),
                        "n1" => array(
                            "VALUE" => "Снег",
                        ),
                        "n2" => array(
                            "VALUE" => "Град",
                        ),
                        
                    ));
                };
            }
       
    }