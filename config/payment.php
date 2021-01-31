<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Currency which have decimals
    |--------------------------------------------------------------------------
    */

    'currency_decimals' => ['AUD', 'CAD', 'CZK', 'DKK', 'EUR', 'HKD', 'MXN', 'NZD', 'NOK', 'PHP', 'PLN', 'GBP', 'RUB', 'SGD', 'SEK', 'CHF', 'THB', 'USD'],

    /*
    |--------------------------------------------------------------------------
    | Currency which supported by paypal subscription'
    |--------------------------------------------------------------------------
    */

    'paypal_currency_subscription' => ['AUD', 'BRL', 'CAD', 'CZK', 'DKK', 'EUR', 'HKD', 'HUF', 'INR', 'ILS', 'JPY', 'MYR', 'MXN', 'TWD', 'NZD', 'NOK', 'PHP', 'PLN', 'GBP', 'RUB', 'SGD', 'SEK', 'CHF', 'THB', 'USD', 'NGN'],

    'gateway' => [
        'paystack' => [
            'name' => 'PayStack',
            'color' => '#03c3f7',
            'secret_key' => 'sk_test_a98b5265de9738a9d7db3928ba9dceb6fd4add01',
            'purchaseLink' => 'frontend.paystack.purchase.authorization',
            'subscriptionLink' => 'frontend.paystack.subscription.authorization',
            'buttonColor' => '#03c3f7',
            'buttonIcon' => '<svg width="24px" height="24px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 28 28"><defs></defs><g clip-path="url(#clip0)"><path d="M22.32 2.663H1.306C.594 2.663 0 3.263 0 3.985v2.37c0 .74.594 1.324 1.307 1.324h21.012c.73 0 1.307-.602 1.324-1.323V4.002c0-.738-.594-1.34-1.323-1.34zm0 13.192H1.306a1.3 1.3 0 00-.924.388 1.33 1.33 0 00-.383.935v2.37c0 .74.594 1.323 1.307 1.323h21.012c.73 0 1.307-.584 1.324-1.322v-2.371c0-.739-.594-1.323-1.323-1.323zm-9.183 6.58H1.307c-.347 0-.68.139-.924.387a1.33 1.33 0 00-.383.935v2.37c0 .74.594 1.323 1.307 1.323H13.12c.73 0 1.307-.6 1.307-1.322v-2.371a1.29 1.29 0 00-1.29-1.323zM23.643 9.258H1.307c-.347 0-.68.14-.924.387a1.33 1.33 0 00-.383.936v2.37c0 .739.594 1.323 1.307 1.323h22.32c.73 0 1.306-.601 1.306-1.323v-2.37a1.301 1.301 0 00-1.29-1.323z" fill="white"></path></g><defs><clipPath id="clip0"><path fill="#fff" d="M0 0h157v28H0z"></path></clipPath></defs></svg>',
        ],
        'flutterwave' => [
            'name' => 'flutterwave',
            'color' => '#03c3f7',
            'environment' => 'staging', //This can be staging or live.
            'public_key' => 'FLWPUBK_TEST-8f3d2d97fd7e3719ddb4dcf493b84a6b-X',
            'secret_key' => 'FLWSECK_TEST-1f4620f9e5aeaeeaf7662cbbeaa8da2b-X',
            'encryption' => 'FLWSECK_TESTc0ce68f908ed',
            'purchaseLink' => 'frontend.flutterwave.purchase.authorization',
            'subscriptionLink' => 'frontend.flutterwave.subscription.authorization',
            'buttonColor' => '#F5A623',
            'buttonIcon' => '<svg width="24px" height="24px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 28 28"><defs></defs><g clip-path="url(#clip0)"><path d="M22.32 2.663H1.306C.594 2.663 0 3.263 0 3.985v2.37c0 .74.594 1.324 1.307 1.324h21.012c.73 0 1.307-.602 1.324-1.323V4.002c0-.738-.594-1.34-1.323-1.34zm0 13.192H1.306a1.3 1.3 0 00-.924.388 1.33 1.33 0 00-.383.935v2.37c0 .74.594 1.323 1.307 1.323h21.012c.73 0 1.307-.584 1.324-1.322v-2.371c0-.739-.594-1.323-1.323-1.323zm-9.183 6.58H1.307c-.347 0-.68.139-.924.387a1.33 1.33 0 00-.383.935v2.37c0 .74.594 1.323 1.307 1.323H13.12c.73 0 1.307-.6 1.307-1.322v-2.371a1.29 1.29 0 00-1.29-1.323zM23.643 9.258H1.307c-.347 0-.68.14-.924.387a1.33 1.33 0 00-.383.936v2.37c0 .739.594 1.323 1.307 1.323h22.32c.73 0 1.306-.601 1.306-1.323v-2.37a1.301 1.301 0 00-1.29-1.323z" fill="white"></path></g><defs><clipPath id="clip0"><path fill="#fff" d="M0 0h157v28H0z"></path></clipPath></defs></svg>',
        ]
    ]
];
