<?php
namespace Zhangjiahao93\Huifu;

use Zhangjiahao93\Huifu\Merchant\Basicdata;
use Zhangjiahao93\Huifu\Merchant\Busi;
use Zhangjiahao93\Huifu\Merchant\Settle;
use Zhangjiahao93\Huifu\Trade\Acctpayment;
use Zhangjiahao93\Huifu\Trade\Check;
use Zhangjiahao93\Huifu\Trade\Hosting;
use Zhangjiahao93\Huifu\Trade\Payment;
use Zhangjiahao93\Huifu\Trade\Settlement;
class Huifu
{
    use Basicdata, Busi, Acctpayment, Settlement, Settle, Payment, Check, Hosting;
    public $client;
    private $sys_id;
    private $product_id;
    private $private_key;
    private $public_key;

    public function __construct($config = [])
    {
        // $this->client = new Client([
        //     'base_uri' => "https://api.huifu.com",
        //     'headers'  => [],
        //     'timeout'  => 10,
        // ]);
        foreach ($config as $key => $value) {
            $this->$key = $value;
        }
    }

    protected function createBody($post_data)
    {
        $post_data          = $this->filterEmptyData($post_data);
        $body               = [];
        $body['sys_id']     = $this->sys_id;
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
            if (! empty($v) || $v == '0') {
                return true;
            }
            return false;
        });
    }

    // 聚合正扫 -- 微信小程序
    // https://paas.huifu.com/partners/api/#/smzf/api_jhzs
    public function wxMiniJspay($allInput)
    {
        /*
         * trans_amt 交易金额 元
         * trade_type 交易类型
         * goods_desc 商品描述
         * remark 备注
         * notify_url 异步通知地址
         **/
        $acct_split_bunch = "";
        if (! empty($allInput['acct_split_bunch'])) {
            $acct_split_bunch = json_encode($allInput['acct_split_bunch'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }
        // $json = $this->createBody([
        //     'req_date'         => $allInput['req_date'],
        //     'huifu_id'         => $this->sys_id,

        //     'trade_type'       => 'T_MINIAPP',                              //微信小程序
        //     'time_expire'      => date("YmdHis", strtotime("+30 minutes")), //支付有效期30分钟
        //     'req_seq_id'       => $allInput['req_seq_id'],
        //     'trans_amt'        => $allInput['trans_amt'],
        //     'goods_desc'       => $allInput['goods_desc'],
        //     'remark'           => $allInput['remark'],
        //     "notify_url"       => $allInput['notify_url'],
        //     "delay_acct_flag"  => $allInput['delay_acct_flag'],
        //     'is_div'           => $allInput['is_div'], // 是否分账交易
        //     'acct_split_bunch' => $acct_split_bunch,
        //     'wx_data'          => json_encode($allInput['wx_data'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
        // ]);

        $json = $this->createBody(array_merge([
            'huifu_id' => $this->sys_id,
        ], $allInput));

        return $this->post("/v2/trade/payment/jspay", $json);
    }

    // 交易查询接口
    // https://paas.huifu.com/partners/api#/smzf/api_qrpay_cx?id=%e4%ba%a4%e6%98%93%e6%9f%a5%e8%af%a2%e6%8e%a5%e5%8f%a3
    public function paymentScanpayQuery($allInput)
    {

        $json = $this->createBody(array_merge(
            [
                'huifu_id' => $this->sys_id,
                // 'org_req_date' => $allInput['org_req_date'],
                // 'org_req_seq_id' => $allInput['org_req_seq_id'], //汇付订单号
            ],
            $allInput
        )
        );
        $res = $this->client->post("/v2/trade/payment/scanpay/query", [
            "json" => $json,
        ]);

        return json_decode($res->getBody()->getContents(), true);
    }

    // 退款
    // https://paas.huifu.com/open/doc/api/#/smzf/api_qrpay_tk?id=ewm
    public function refund($allInput)
    {
        $json = $this->createBody(array_merge([
            'huifu_id' => $this->sys_id,
            // 'req_date'      => date("Ymd"),
            // 'req_seq_id'    => "",
            // 'org_hf_seq_id' => $allInput['org_hf_seq_id'],
            // 'ord_amt'       => $allInput['ord_amt'],
            // 'org_req_date'  => $allInput['org_req_date'],
        ], $allInput));

        return $this->post("/v2/trade/payment/scanpay/refund", $json);
    }

    // 退款查询
    public function refundQuery($allInput)
    {
        $json = $this->createBody([
            'huifu_id'      => $this->sys_id,
            'org_hf_seq_id' => $allInput['org_hf_seq_id'],
            'org_req_date'  => $allInput['org_req_date'],
        ]);
        $res = $this->client->post("/v2/trade/payment/scanpay/refundquery", $json);

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
