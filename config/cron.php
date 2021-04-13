<?php

return [

/*
|--------------------------------------------------------------------------
| Configuración tarea cálculo de existencias
|--------------------------------------------------------------------------|
|
*/

'time' => env('CRON_TIME', '00:00'),

'woo_url' => env('WOO_URL', false),
'woo_key' => env('WOO_KEY', null),
'woo_sec' => env('WOO_SEC', null),

];
