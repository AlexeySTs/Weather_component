<?php

namespace Mcart\Weather;

use Bitrix\Main\Loader;
use Mcart\Weather\Weather;
use Mcart\Weather\Tables\CitiesTable;

class Agent
{
    static public function updateInfoWeather()
    {
        if (!Loader::includeModule('mcart.weather')) {
            ShowError('Error not include mcart.weather module');
            return;
        };

        $list_cities = CitiesTable::getList([
            'select' => ['LAST_USE', 'ID', 'LONGITUDE', 'LATITUDE'],
        ]);

        $date_del = (time() - (60 * 60 * 24 * 5)); // Дата 5 дней назад
        while ($query = $list_cities->fetch()) {
            $last_use = ($query['LAST_USE']->getTimestamp());
            if ($last_use < $date_del) { // Если  прошло больше 5 дней удалить из БД города и ХЛ блока
                CitiesTable::delete($query['ID']);
                Weather::deleteWeatherHLByCity($query['ID']);
            } else { // Иначе очистить ХЛ блок и заполнить заного
                $params = [
                    'LONGITUDE' => $query['LONGITUDE'],
                    'LATITUDE' => $query['LATITUDE'],
                ];
                Weather::deleteWeatherHLByCity($query['ID']);
                Weather::fillHLBlockWeatherByCity($params, $query['ID']);
            };
        };
    }
}
