<?php
/**
 * Created by PhpStorm.
 * User: leo
 * Date: 2019/4/11
 * Time: 11:36
 */
return [
    'alipay' => [
        'app_id' => '2019040463754546',
        // 'notify_url' => 'https://www.pingshentong.com/pay/alipay',
        // 'return_url' => 'https://www.pingshentong.com/pay/alipay_return',
        'ali_public_key' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAs/pDh3m7FzBcHn/f0toyX2KcO8feyBVJT1t7x/YkHpyCdK9FOeQh9U0aZhpsGepc20dX39vHoP5cca+ns4R7OgG+ZpRCoqUChJGq0s6gA9/IicufyYU+U/qMkjhY0RCiBW3Dw5s+PY+a51OyziDW10CabriQ/IjqlU9Ldyn6af2B9L1oiphlwAUhRtSc+y0i7E0XNpCWFg+iJBwc0Lxvq6kyjNnfBhRNENGTwnK9avHnSozGdA/CRBbYoSNqs/XKEygdVT7MEj2toQxO7y249jTZeeuvFzp1SLgzDs6iPfkX6r1Xfx6Ig+bds/PPyWYlB7P5p6BJbaZPTIz97ncmcQIDAQAB',
        // 加密方式： **RSA2**
        'private_key' => 'MIIEpAIBAAKCAQEAmqyYmD3wthr+KxKGcmwgELu6+w0p1knd2WeM2fX8RRokrhzDlpmNfl1Y6DOccHwRcn3fYKGXfsWCyT2oGx4VpiAP6Ge8Dz0lB8NfvIHpkT66pzNQ96Cn2FaoxbdsMa1BDHm0WCg2K63ApAiMhw803x41CZi2Avc9OT0PnrBLaeqpF0TXTM8UGglpdvlJ3EJe++OtR1slJH+xtXG48z1feXtnwfBL4MnA3Hb+cJKWK2qlD89zAqpIWlmsNoWSTbgn5BTYJxPQ/+UlWApmlffHwrhaNJOILQQCC5JoecRshBTi3Nme+CdxFJwcyxod49UH0T0Bfd1FLrsmk2TGhVgRMwIDAQABAoIBAEpm/dw152Vh5KOJfLzrwBzsgQwkj9xxmdLrWbi6+AexhLVwRrFZ2dNQSeUgdn8xXx8o51vPP1WO4tO04flxZk6MG7ras37NFdP/Kj0+bMEeYKDvMWJ8mGCbhwHcsqHOynyayqKhbTVjlkgRiK+rI+6z1CVynRMeJQg0lB/sg8pFhGZ9WMxYw9Qxp/Vh1U/u+eTeCsB6q4fuoQDNeHhu+ZCraZYBNFz89LULo6HLjRxM0MhpC+zPuQ1+F8Yk2KNeXWM3+nN0i/boB1hIOURS2IlFgZDcak0iALjdrq/2ZnK5IAU7ZfTEe4KijgIKP+0tR1IIdFb8WwPCYD6MJMXUYJECgYEAy2gJH3vuixjYSrdldWbrvF/VC5L4oBF/jcoAWDS1+zID4v+rQRC7/aJu7W2mjuRSq2IoShvmzBVKEI+q11bEHGdxw7uBC/iLDjuchmuCvnV46PBOlKxENvlKYPm/XSiWTE9AHqnFz9yUVH0p1PO4T9xo+lNR9qDiKqWGoJK5l0kCgYEAwqrbvTG301L8cvVBg7Woq5eZeLt2aC0r96awx+Z2SjT+KcXgfaCgkXS1LLLs0R9/nfijsIglXQc/CbUgEslbQDNe3J7Wmu+wT+dQ9ko1Iw8kP4JXyAmPwu11pV/WXRHo3Rn52ZDYK8+oFbRgiyQnsYnh73mZn5R0XMqlVZdVuJsCgYEAoGk3aKDwCrpZOPNRDDlSh4h8F1icwn+KPGOlyeon6iYTcp12jSWJXAkRuiTTSbU+jPIoG+nNJf1UH6Ntc811c4EQ8u+YYbxszZitZES9Iz65T9rT3ePNGB2ZTskm5WzwutV1F7mDyt2GDOt4ZfyeyjxkfaXf6cQPGNrZRSDxRCECgYEApsCLSK6tI/XDUIosW7Gh0ZMkIRqcHDMNpa+BYUtWasyoJ4pceeC9irvpcme2mpCb1esl9NM7BkxtwFgKKG2ZoA4hJWVeugu2AUxJux/oEqcmysxRe4ZNQpH8o/PLgaAU1EkrECaWNjlEZ4tC5A3NfrR4+JU2dk9Jui7Qn9fp0x8CgYAFBQppUw4c7eKv9lUpVlABCYYoqlt0KjqIIP4dXFP9e8zl4QqtxdVzTcuob4E9AtmugeGNbW4+2+ubl3yg/xgkLJb7ChRvC1utuSn9iaStICnSPm1rbd+xYL8adYrfG7boePZTZzI//YBO0RL2HR+cTP8/lYdRwxGZz0WzYX/pow==',
        'log' => [ // optional
            'file' => storage_path('logs/alipay.log'),
            'level' => 'info', // 建议生产环境等级调整为 info，开发环境为 debug
            'type' => 'single', // optional, 可选 daily.
            'max_file' => 30, // optional, 当 type 为 daily 时有效，默认 30 天
        ],
        'http' => [ // optional
            'timeout' => 5.0,
            'connect_timeout' => 5.0,
            // 更多配置项请参考 [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
        ],
        // 'mode' => 'dev', // optional,设置此参数，将进入沙箱模式
    ],

    'wechat' => [
        // 'appid' => 'wx68ce9558bc0a1381', // APP APPID
        'app_id' => 'wx68ce9558bc0a1381', // 公众号 APPID
        'miniapp_id' => 'wx7942d28b03b972fd', // 小程序 APPID
        'mch_id' => '1532740991',
        'key' => 'lrjbf3DSEMKFeEi6GOR2RpF6svTkC1U5',
//        'notify_url' => 'https://www.pingshentong.com/payment/wechat/notify',
        'cert_client' => resource_path('wechat_pay/apiclient_cert.pem'),
        'cert_key'    => resource_path('wechat_pay/apiclient_key.pem'),
        'log' => [ // optional
            'file' => storage_path('logs/wechat_pay.log'),
            'level' => 'info', // 建议生产环境等级调整为 info，开发环境为 debug
            'type' => 'single', // optional, 可选 daily.
            'max_file' => 30, // optional, 当 type 为 daily 时有效，默认 30 天
        ],
        'http' => [ // optional
            'timeout' => 5.0,
            'connect_timeout' => 5.0,
            // 更多配置项请参考 [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
        ],
//        'mode' => 'dev', // optional, dev/hk;当为 `hk` 时，为香港 gateway。
    ],
];