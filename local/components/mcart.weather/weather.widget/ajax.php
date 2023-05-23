<?php
#components/bitrix/example/ajax.php

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

class WeatherWidgetAjaxController extends \Bitrix\Main\Engine\Controller
{
    #в параметр $person будут автоматически подставлены данные из REQUEST
    public function testAction($query)
    {   
        $info = json_decode($query, true);
        $res = $info['value'];
        p2f($info['value']);
        return $info['value'];
    }

    public function listUsersAction(array $filter)
    {
        $users = [];
        //выборка пользователей по фильтру
        //наполнения массива данными для ответа
        
        return $users;
    }
}