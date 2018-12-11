<?php

return [
    //支付宝配置
    'alipay' => [
        //商户APP_ID
        'app_id'         => '2016092300578558',
        //支付宝公钥
        'ali_public_key' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA0dOupuazdYGsBUNq/SOFJTTmkWrw+cnV/S6F4ftZodX5/Ppibp5D5NXiN6kz9KW43nOWBcWjFcEfCL74p4mSSCihVbf5dqAqnVrpjD49pF0A8FEKmlIvwI5agK4xxjSVfOJbrgMyBX/oeOgsCuauWcSW9bRuvnaka6RXA5LRdg18Mho3diSEbavlFyIHt/2iWlM813ob7R+gjpIn2hf3rzJXZh6gwpd5MzvF19omDpiHz0qtEYcx6wiZ8KZFj0OpSs1xzwmI210Rln6Nlfo4IXSyBl+4/vKD3VYIBHuymrsP5SN4mvQqmY9pXBqwC2nILVOqCilz+r7sJvuIy0nefwIDAQAB',
        //商户私钥
        'private_key'    => 'MIIEpQIBAAKCAQEA4ubrJ8YSeVCgMtgAO2zIpSA+s775nZmqh4KWmSmfU7XSwZADesnxFK5Rbx9/WMq04OCcrRTg00bHwAw/HY9+KM/uUtArpmG/9ahucm+XXBrAB5NenaXRxAvlRtlX4IZZWo5NDOEwbwj1o4qsXoKfd1CQAE1r/y6+z1Bh1reuwkLkVVnErRPqwJjqjFzs1RxsZNHl6R6MJ8aMvsxi1f3/C+6eP8z+zdEgY1fM9gOtNDh0EqzyZojv1KMoCYbcQosTj/PUCk8Kk3rKlriSeL1i4B+JLMstMbn7UcvX+6JDF1Nm29Bcl49N/RCHnAFydc9ann9iq10xWIPQKH6gUPsToQIDAQABAoIBAQCYlgF9dc3mlzmj923wiG9F3DFOXxctzLDJACCWT0891AAu+tHcOQFOjwtkVyIka+zHbUTvgCLQ8cBSfenTzzNf7rSuLB+ue/3DDcl4W2LGJZcNgSUXjvNJi1ZcFrzp2G8uXpOiHa2cfcHygMb5p43lht/P1EDUEnNupveklVGIgH7fewxK9dNrv9x6RlJ4/XG6coeKoa+LMt/odFBOMcunAFrHRc7ngTk3hZAhoB/oj9lWAEc8SVXxenfhkLotLg8j4G4CKktGw3dmfTdfnG/HxkWQdla9aMUhJpYnB3PHGm/gWr093TCqj6oI9j5fhR0Xy6tJ1dSyA7NjJVmTfJOBAoGBAPw0UvVWbwf/zNoQ5VmjkG8Vo9d4QmwIGQp37r1S2n2Yzk6bIytLyaBpUfdnm/hzr9mc+hOVOkjn6kAAugZiXsaBcIp6CujLuhJnQo9HGs3g6ad0vfR45VHIk8cvYh+dSrEQGo8nIbPC4UUfvBuktrbC3JRyMZY660H2i2STDIZtAoGBAOZRHH09Etd/lNt+qD9R+6PDa/EufpHmInRREIawP4/9cUAf5ZixeUyfpZkasdzCHW8R338//nq3R7LxYELbzbXtSipeE9GRZBHz9AzI9r3dk/OGKtPExrTo7KZZq77VE/YvppMR8RKtbUUJoRLYU0H3AIQ2SGEHnRP8vQXJPBGFAoGBAI/n/0oUNqiW0jm/mE6MoPZxn5pr+WKRQFCT3HrU0u0FivqZVPj4dqT8U5Pa38lloXqHMr6H0MkW47f/ciYAbVnRM/kf6CerT5H+r6D3jpjY9Bnj6Bud/COtUn0+UTv+0Ua1DKKQTo+27Dk4AWq4krU3/QsLGbZCCkkKN8F8kpIFAoGBAOFUfeDxfZukUIimVAkPNniz2sltyZsIYrEWFmsPyEAhn0kza7eF+rzCfItcGKN4rr9Z5yViAKEi61zg1mLTFWFmNGda/1zLN/JwkFZpzy1WuUdJ/mCNZjLcp5rgpCzh3tO/LuM4bHjvf7ZJg3MifeJNvA2d5hY4Eq/ZVz1v8frZAoGAeV1riWxDoGOawt5AMsGLjUN9Oj4fws/lL42mymK0BWczkORyottWtGQXj+XMUOhPbqon0by+jAcBBswtxwyTQRMlNDnvvXiDwNwDt5+vNls4EuYp1IwfZIqNtPSIstN5LdBRazZwQf4Aru005hCvAzWscK+zBQyrINAG1RYVKlA=',
        //日志
        'log'            => [
            'file' => storage_path('logs/alipay.log'),
        ],
    ],

    'wechat' => [
        'app_id'      => '',
        'mch_id'      => '',
        'key'         => '',
        'cert_client' => '',
        'cert_key'    => '',
        'log'         => [
            'file' => storage_path('logs/wechat_pay.log'),
        ],
    ],
];