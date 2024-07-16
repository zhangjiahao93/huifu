<?php
namespace Zhangjiahao93\Huifu\Trade;

trait Payment
{
    // 交易确认接口 --- 用于延迟分账
    // https://paas.huifu.com/partners/api/#/smzf/api_jyqr
    public function V2TradePaymentDelaytransConfirm($allInput)
    {
        $json = $this->createBody([
            'req_date' => date("Ymd"),
            'req_seq_id' => genReqSeqID(),
            'huifu_id' => $this->sys_id,
            'org_req_date' => $allInput['org_req_date'],
            'org_req_seq_id' => $allInput['org_req_seq_id'],
            'acct_split_bunch' => $allInput['acct_split_bunch'],
            'ord_amt' => $allInput['ord_amt'],
        ]);

        return $this->post("/v2/trade/payment/delaytrans/confirm", $json);
    }

    // 聚合正扫 -
    // https://paas.huifu.com/partners/api/#/smzf/api_jhzs
    public function V2TradePaymentJspay($allInput)
    {
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

    public function ajspay()
    {
        $data = [
            'req_date' => date("Ymd"),
            'huifu_id' => $this->sys_id,
            'trade_type' => 'A_NATIVE',
            'time_expire' => date("YmdHis", strtotime("+30 minutes")), //支付有效期30分钟
            'req_seq_id' => genReqSeqID(),
            'trans_amt' => "0.02",
            'goods_desc' => "调试0.02",
            'remark' => "",
            "delay_acct_flag" => "Y",
        ];
        $json = $this->createBody($data);
        $res = $this->client->post("/v2/trade/payment/jspay", [
            "json" => $json,
        ]);
        return json_decode($res->getBody()->getContents(), true);
    }

    // 交易确认退款
    // https://paas.huifu.com/partners/api/#/smzf/api_jyqrtk
    public function V2TradePaymentDelaytransConfirmrefund()
    {
        $data = [
            'req_date' => date("Ymd"),
            'huifu_id' => $this->sys_id,
            'req_seq_id' => genReqSeqID(),

        ];
        $json = $this->createBody($data);
        $res = $this->client->post("/v2/trade/payment/delaytrans/confirmrefund", [
            "json" => $json,
        ]);
        return json_decode($res->getBody()->getContents(), true);
    }
}
