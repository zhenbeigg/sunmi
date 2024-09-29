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
        $header['Sunmi-Timestamp'] = time();
        $header['Sunmi-Nonce'] = get_rand_str(6);
        $header['Sunmi-Appid'] = env('SUNMI_APPID');
        /* 生成请求body */
        $param['body'] = eyc_array_key($param, 'request_id,encrypt_factor');
        $param = eyc_array_insert($param, $header, 'Sunmi-Timestamp,Sunmi-Nonce');
        /*获取签名 */
        $header['Sunmi-Sign'] = $this->Service->get_sign($param);
        $r = $this->GuzzleHttp->post($this->url . '/v2/eid/eid/idcard/decode', $param['body'], $header);
        if ($r['code'] == 1) {
            return $r['data'];
        } else {
            error($r['code'], $r['msg']);
        }
    }
}
