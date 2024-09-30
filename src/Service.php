<?php
/*
 * @author: 布尔
 * @name:  服务类
 * @Date: 2020-04-20 10:29:00
 */

namespace Eykj\Sunmi;

use function Hyperf\Support\env;

class Service
{
    /**
     * @author: 布尔
     * @name:获取签名
     * @param {array} $param 
     * @return {string}
     */
    public function get_sign(array $param): string
    {
        /* 参考文档：https://developer.sunmi.com/docs/zh-CN/cdixeghjk491/xcdieghjk579 */
        /* 1.拼接字符串 stringA=json-body + Sunmi-Appid + Sunmi-Timestamp + Sunmi-Nonce,2. hmac256,3.bin2hex转16进制*/
        return bin2hex(hash_hmac('sha256', json_encode($param['body'], 320) . env('SUNMI_APPID') . $param['Sunmi-Timestamp'] . $param['Sunmi-Nonce'], env('SUNMI_KEY'), true));
    }
}
