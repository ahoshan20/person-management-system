<?php

return [

    /*
    |─────────────────────────────────────────────────────
    | API Authentication Key (must be exactly 25 chars)
    |─────────────────────────────────────────────────────
    */
    'auth_key' => env('API_AUTH_KEY', 'DOORSOFT2024APIKEY12345678'),

    /*
    |─────────────────────────────────────────────────────
    | Required Organization Name
    |─────────────────────────────────────────────────────
    */
    'org_name' => env('API_ORG_NAME', 'Door Soft'),

    /*
    |─────────────────────────────────────────────────────
    | Rate Limiting: max requests per minute per IP
    |─────────────────────────────────────────────────────
    */
    'rate_limit' => env('API_RATE_LIMIT', 5),

];