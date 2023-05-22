/*import {Type} from 'main.core'; */

$("#address").suggestions({
    token: "d1706db9072b13e706c9807662646053856996bc",
    type: "ADDRESS",
    /* Вызывается, когда пользователь выбирает одну из подсказок */
    onSelect: function(suggestion) {
        BX.ajax.runComponentAction('mcart:weather.forecast', 'getCityWeather', {
            mode: 'ajax', //это означает, что мы хотим вызывать действие из class.php
            data: {
                city_info: JSON.stringify(suggestion) //данные будут автоматически замаплены на параметры метода 
            },
            analyticsLabel: {
                viewMode: 'grid',
                filterState: 'closed'	
            }	
        }).then(function (response) {
            console.log(response);
            /**
            {
                "status": "success", 
                "data": "Hi Hero!", 
                "errors": []
            }
            **/			
        }, function (response) {
            //сюда будут приходить все ответы, у которых status !== 'success'
            console.log(response);
            /**
            {
                "status": "error", 
                "errors": [...]
            }
            **/				
        });(suggestion);
    }
});