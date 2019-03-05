<?php
return [

    //是否伪装已经发送SMS了
    'pretend' => env('SMS_PRETEND', true),

    // 默认的短信服务商
    'default' => env('SMS_DRIVER', 'accessYou'),

    //找回密码时临时密码的生命周期
    'lifetime' => env('SMS_AUTH_LIFE_TIME',30),

    /**
     * 是否使用队列
     */
    'queue' => env('SMS_QUEUE', false),

    'services' => [

        'accessYou' => [
            'driver' => '\MMHK\SMS\Gateways\AccessYou',
            'config' => [
                'accountno' => '',
                'pwd'       => '',
                'from'      => '',
                'size'      => 'l',
            ]
        ],

        'xGate' => [
            'driver' => '\MMHK\SMS\Gateways\XGate',
            'config' => [
                'UserID'          => '',
                'UserPassword'    => '',
                // TEXT WAPPUSHSI UDH OperatorLogo RingingTone
                'MessageType'     => '',
                // UTF8 / ENG / BIG5 / GB
                'MessageLanguage' => '',
                // Allows you to set your own “Sender ID”
                // Up to 11 alphanumeric characters
                // can be used (“Space” ok, but no special characters)
                // e.g. 85291234567
                // -ore.g.
                // abcCompany
                'Senderid'        => 'Motors', // 貌似这个功能需要激活
            ]
        ],

        'WaveCell' => [
            'driver' => '\MMHK\SMS\Gateways\WaveCell',
            'config' => [
                'api_key'    => '',
                'account_id' => '',
                'sender_id'   => 'Motors', // 貌似这个功能需要激活
            ],
        ],
    ],

    //需要开发测试环境接收短信的白名单
    'whitelist' => [

    ],
];