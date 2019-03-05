## Laravel SMS Provider for MMHK

一个发送 `SMS` 的 service 接口， 现在只集成了 [AccessYou](http://www.accessyou.com/) [xGate](https://www.xgate.com/sms-gateway.html) [WaveCell](https://wavecell.com/) 等项目中常用的第三方`SMS`服务。

### 安装

```bash
composer require mmhk/mm-laravel-sms
```

`composer` 安装好了之后，将provider添加到 `config/app.php` 的 `providers` 数组里面。

```php
\MMHK\SMS\SMSServiceProvider::class,
```

**Laravel 5.5** 自带自动发现功能, 所以并不需要手动添加provider。

复制配置文件到 `config` 目录下

```bash
php artisan vendor:publish --provider="MMHK\SMS\SMSServiceProvider" --tag=config
```

### 配置

配置文件存放在 `config/sms-service.php`， 具体内容如下：

```php
[

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

    /**
     * SMS服务提供商配置
     */
    'services' => [

        'accessYou' => [
            //具体实现类
            'driver' => '\MMHK\SMS\Gateways\AccessYou',
            //实现类的配置
            'config' => [
                'accountno' => '',
                'pwd'       => '',
                'from'      => '',
                'size'      => 'l',
            ]
        ],

        'xGate' => [
            //具体实现类
            'driver' => '\MMHK\SMS\Gateways\XGate',
            //实现类的配置
            'config' => [
                'UserID'          => '',
                'UserPassword'    => '',
                'MessageType'     => '',
                'MessageLanguage' => '',
                'Senderid'        => '',
            ]
        ],

        'WaveCell' => [
            //具体实现类
            'driver' => '\MMHK\SMS\Gateways\WaveCell',
            //实现类的配置
            'config' => [
                'api_key'     => '',
                'account_id'  => '',
                'sender_id'   => '',
            ],
        ],
    ],

    //需要开发测试环境接收短信的白名单
    'whitelist' => [

    ],
];
```

### 使用例子

```php
app('sms')->send(new \MMHK\SMS\Message([
    'to' => 'your mobile number',
    'content' => 'Hello World!',
]));
```