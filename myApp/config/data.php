<?php

return [

    // HTTP status codes
    'http' => [
        'ok'      => 200,
        'created' => 201,
        'no_content' => 204,
        'not_found'  => 404,
        'validation_error' => 422,
        
    ],

    // Pagination defaults
    'pagination' => [
        'per_page_default' => 10,
        'per_page_max'     => 100,
    ],

 
];
