<?php
namespace Zhangjiahao93\Huifu;

use Exception;
use function Hyperf\Support\env;
use GuzzleHttp\Client;
use Zhangjiahao93\Huifu\Merchant\Basicdata;
use Zhangjiahao93\Huifu\Merchant\Busi;
use Zhangjiahao93\Huifu\Merchant\Settle;
use Zhangjiahao93\Huifu\Trade\Acctpayment;
use Zhangjiahao93\Huifu\Trade\Payment;
use Zhangjiahao93\Huifu\Trade\Settlement;

class Huifu
{
    use Basicdata, Busi, Acctpayment, Settlement, Settle, Payment;
    public $client;
    private $sys_id;
    private $product_id;
    private $private_key;
    private $public_key;

    public function __construct($config = [])
    {
        $this->client = new Client([
            'base_uri' => "https://api.huifu.com",
            'headers' => [],
            'timeout' => 10,
        ]);
        foreach ($config as $key => $value) {
            $this->$key = $value;
        }
        if (empty($config)) {
            $this->sys_id = env('HUIFU_SYS_ID');
            $this->product_id = env("HUIFU_PRODUCT_ID");
            $this->private_key = env("HUIFU_PRIVATE_KEY");
            $this->public_key = env("HUIFU_PUBLIC_KEY");
        }
    }

    protected function createBody($post_data)
    {
        $post_data = $this->filterEmptyData($post_data);
        $body = array();
        $body['sys_id'] = $this->sys_id;
        $body['product_id'] = $this->product_id;
        ksort($post_data); // 根据key排序
        $body['data'] = $post_data;
        #  执行签名
        $sign = BsPayTools::sha_with_rsa_sign(
            json_encode($post_data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), // 数据里有中文和斜杠都不转码
            $this->private_key);
        $body['sign'] = $sign;

        // logger()->info("createBody====>", $body);
        return $body;
    }

    private function filterEmptyData($req_params)
    {
        return array_filter($req_params, function ($v) {
            if (!empty($v) || $v == '0') {
                return true;
            }
            return false;
        });
    }

    // 聚合正扫 -- 微信小程序
    // https://paas.huifu.com/partners/api/#/smzf/api_jhzs
    public function wxMiniJspay($allInput)
    {
        $validator = validator()->make(
            $allInput,
            [
                'trans_amt' => 'required|min:0.01',
                'goods_desc' => 'sometimes', // 商品描述
                'remark' => 'sometimes', // 交易备注
                'notify_url' => 'required',
                'wx_data' => 'required',

                'wx_data.sub_appid' => 'required', //微信分配的子商户公众账号id，微信js/小程序支付必传
                //   'wx_data.openid' => 'required',//用户标识
                'wx_data.sub_openid' => 'required', //sub_openid    子商户用户标识
                //   'wx_data.attach' => 'required',// 携带订单自定义数据。原样返回
                'wx_data.body' => 'required', //商品描述
                //   'wx_data.detail' => 'required|array',
                //   'wx_data.detail.*.goods_id' => 'required',//商品编码
                //   'wx_data.detail.*.goods_name' => 'required',//商品名称
                //   'wx_data.detail.*.price' => 'required',//商品金额（元）
                //   'wx_data.detail.*.quantity' => 'required',//商品数量
            ],
            [],
        );
        if ($validator->fails()) {
            throw new Exception($validator->errors()->first());
        }
        /*
         * trans_amt 交易金额 元
         * trade_type 交易类型
         * goods_desc 商品描述
         * remark 备注
         * notify_url 异步通知地址
         **/
        $json = $this->createBody([
            'req_date' => date("Ymd"),
            'huifu_id' => $this->sys_id,
            'trade_type' => 'T_MINIAPP', //微信小程序
            'time_expire' => date("YmdHis", strtotime("+30 minutes")), //支付有效期30分钟
            'req_seq_id' => genReqSeqID(),
            'trans_amt' => $allInput['trans_amt'],
            'goods_desc' => $allInput['goods_desc'] ?? "",
            'remark' => $allInput['remark'] ?? "",
            "notify_url" => $allInput['notify_url'] ?? "",
            'wx_data' => json_encode($allInput['wx_data'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
        ]);
        $res = $this->client->post("/v2/trade/payment/jspay", [
            "json" => $json,
        ]);

        return json_decode($res->getBody()->getContents(), true);
    }

    // 查询支付结果
    public function paymentScanpayQuery($allInput)
    {
        $json = $this->createBody([
            'huifu_id' => $this->sys_id,
            'org_req_date' => $allInput['org_req_date'],
            'org_req_seq_id' => $allInput['org_req_seq_id'], //汇付订单号
        ]);
        $res = $this->client->post("/v2/trade/payment/scanpay/query", [
            "json" => $json,
        ]);

        return json_decode($res->getBody()->getContents(), true);
    }

    // 退款
    public function refund($allInput)
    {
        $validator = validator()->make(
            $allInput,
            [
                'req_seq_id' => 'required', //本地流水单
                'org_hf_seq_id' => 'required', //全局流水单
                'ord_amt' => 'required|min:0.01',
                'org_req_date' => 'required', //本地流水单日
            ],
            [],
        );
        if ($validator->fails()) {
            throw new Exception($validator->errors()->first());
        }
        $json = $this->createBody([
            'req_date' => date("Ymd"),
            'req_seq_id' => genReqSeqID(),
            'org_hf_seq_id' => $allInput['org_hf_seq_id'],
            'huifu_id' => $this->sys_id,
            'ord_amt' => $allInput['ord_amt'],
            'org_req_date' => $allInput['org_req_date'],
        ]);
        $res = $this->client->post("/v2/trade/payment/scanpay/refund", [
            "json" => $json,
        ]);

        return json_decode($res->getBody()->getContents(), true);
    }

    // 退款查询
    public function refundQuery($allInput)
    {
        $validator = validator()->make(
            $allInput,
            [
                'org_hf_seq_id' => 'required', //全局退款流水
                'org_req_date' => 'required', // 申请退款日
            ],
            [],
        );
        if ($validator->fails()) {
            throw new Exception($validator->errors()->first());
        }
        //
        $json = $this->createBody([
            'huifu_id' => $this->sys_id,
            'org_hf_seq_id' => $allInput['org_hf_seq_id'],
            'org_req_date' => $allInput['org_req_date'],
        ]);
        $res = $this->client->post("/v2/trade/payment/scanpay/refundquery", [
            "json" => $json,
        ]);

        return json_decode($res->getBody()->getContents(), true);
    }

    public function post($url, $data)
    {
        $response = $this->client->post($url, [
            "json" => $data,
        ]);
        $result = json_decode($response->getBody()->getContents(), true);

        return $result;
    }

}
