<?php
    function p ($ar) {
        echo "<pre>";
        print_r($ar);
        echo "</pre>";
    }

    function getUserEnum($arParams){
        if(empty($arParams['FIELD_NAME'])){ // Название поля из админки
           $GLOBALS['APPLICATION']->ThrowException('Не заполнено название поля');
           return false;
       }
       if(empty($arParams['ENTITY_ID'])){ // Объект поля из админки
           $GLOBALS['APPLICATION']->ThrowException('Не заполнено название поля');
           return false;
       }
       $filter=['FIELD_NAME'=>$arParams['FIELD_NAME'],'ENTITY_ID'=>$arParams['ENTITY_ID']];
    
       $rsData = CUserTypeEntity::GetList( array('ID'=>'ASC'), $filter);
       while($arRes = $rsData->Fetch())
       {
           $id=$arRes['ID'];
       }
       $result=[];
       $res = CUserFieldEnum::GetList(['ID'=>'ASC'],["USER_FIELD_ID"=>$id]);
       while($cur=$res->fetch()){
           $result[$cur['VALUE']]=$cur['ID'];
       }
      
       if($arParams['ID']>0)
           $result = array_flip($result);
       if(!empty($arParams['RETURN']))
           return  $result[$arParams['RETURN']];
       else
           return  $result;
    }

    function refreshWeatherInfo() {
        require_once($_SERVER["DOCUMENT_ROOT"].'/local/tools/weather.php');
        return 'refreshWeatherInfo();';
    }
?>