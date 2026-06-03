<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Nombre del cliente (white-label)
    |--------------------------------------------------------------------------
    | Se muestra en vistas, emails, PDFs y título del navegador.
    | Configurable desde APP_NAME en .env.
    */
    'name' => env('APP_NAME', 'Cambio J'),

    /*
    |--------------------------------------------------------------------------
    | Logo del cliente
    |--------------------------------------------------------------------------
    | Ruta relativa a public/ (ej: "images/logo.png").
    | Si está vacío, el navegador muestra el nombre en texto.
    */
    'logo' => env('APP_LOGO', ''),

    /*
    |--------------------------------------------------------------------------
    | Favicon del cliente
    |--------------------------------------------------------------------------
    | Ruta relativa a public/ (ej: "favicon-vipmoney.ico").
    | Si está vacío, usa favicon.ico por defecto.
    */
    'favicon' => env('APP_FAVICON', 'favicon.ico'),

];
