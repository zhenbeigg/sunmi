<?php
/*
 * @author: 布尔
 * @name:  身份证
 * @Date: 2020-04-20 10:29:00
 */

namespace Eykj\Sunmi;

use Eykj\Base\GuzzleHttp;
use Eykj\Sunmi\Service;
use function Hyperf\Support\env;

class Idcard
{
    protected ?GuzzleHttp $GuzzleHttp;

    protected ?Service $Service;

    // 通过设置参数为 nullable，表明该参数为一个可选参数
    public function __construct(?GuzzleHttp $GuzzleHttp, ?Service $Service)
    {
        $this->GuzzleHttp = $GuzzleHttp;
        $this->Service = $Service;
    }

    /* 请求域名 */
    private $url = "https://openapi.sunmi.com";

    /**
     * @author: 布尔
     * @name:身份证云识别
     * @param {array} $param 
     * @return {array}
     */
    public function decode(array $param): array
    {
        $options['headers']['Sunmi-Timestamp'] = time();
        $options['headers']['Sunmi-Nonce'] = get_rand_str(6);
        $options['headers']['Sunmi-Appid'] = env('SUNMI_APPID');
        /* 生成请求body */
        $param['body'] = eyc_array_key($param, 'request_id|card_no,request_id');
        $param['body']['encrypt_factor'] = 'Yy' . get_rand_str(6);
        $param = eyc_array_insert($param, $options['headers'], 'Sunmi-Timestamp,Sunmi-Nonce');
        /*获取签名 */
        $header['Sunmi-Sign'] = $this->Service->get_sign($param);
        $r = $this->GuzzleHttp->post($this->url . '/v2/eid/eid/idcard/decode', $param['body'], $options);
        if ($r['code'] == 1) {
            //1.Base64 解码 2. DES解密需要的密钥和向量 3. 截取AppKey前8位作为密钥 4. 加密因子作为初始向量 5.进行DES解密（假定使用的是DES-CBC模式）
            return y_json_decode(openssl_decrypt(base64_decode($r['data']['info']), 'DES-CBC', substr(env('SUNMI_KEY'), 0, 8), OPENSSL_RAW_DATA, $param['body']['encrypt_factor']));
        } else {
            alog($header, 2);
            alog($param['body'], 2);
            alog($r, 2);
            error($r['code'], '识别失败，请重试');
        }
    }
}
