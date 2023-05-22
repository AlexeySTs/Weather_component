<?php

namespace Mcart\Weather\Tables;

use \Bitrix\Main\Entity;
use \Bitrix\Main\Type;

class CitiesTable extends Entity\DataManager
{
    public static function getTableName()
    {
        return 'mcart_cities';
    }

    public static function getMap()
    {
        return [
            new Entity\IntegerField('ID', [
                'primary' => true,
                'autocomplete' => true,
            ]),
            new Entity\StringField('NAME'),
            new Entity\StringField('LONGITUDE'),
            new Entity\StringField('LATITUDE'),
            new Entity\StringField('CITY_FIAS_ID'),
            new Entity\DatetimeField('LAST_USE',array(
                'required' => true)),
        ];
    }
}