/*import {Type} from 'main.core'; */

$("#address").suggestions({
    token: "d1706db9072b13e706c9807662646053856996bc",
    type: "ADDRESS",
    /* Вызывается, когда пользователь выбирает одну из подсказок */
    onSelect: function (suggestion) {

        $.ajax({
            type: "POST",
            url: '/local/modules/mcart.weather/ajax.php',
            data: {
                'city_fias_id': suggestion.data.city_fias_id,
                'geo_lat': suggestion.data.geo_lat,
                'geo_lon': suggestion.data.geo_lon,
                'city': suggestion.data.city,
            },
            success: function (answer) {
                let forecast = $('.weather_foreast');
                forecast.html(answer);

            },
        });
    }
});