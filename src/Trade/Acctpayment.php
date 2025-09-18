<?php
namespace Zhangjiahao93\Huifu\Trade;

trait Acctpayment
{
    // 余额支付 https://paas.huifu.com/open/doc/api/#/yuer/api_acctpayzf.md
    public function V2TradeAcctpaymentPay($allInput)
    {
        $json = $this->createBody(array_merge([
            'req_seq_id' => "",
            'req_date'   => date("Ymd"),
        ], $allInput));
        //
        return $this->post("/v2/trade/acctpayment/pay", $json);
    }

    // 余额支付查询
    // https://paas.huifu.com/partners/api/#/yuer/api_acctpaycx
    public function V2TradeAcctpaymentPayQuery($allInput)
    {
        $json = $this->createBody(array_merge([
            'huifu_id' => $this->sys_id,
            // 'req_date' => date("Ymd"),
            // 'req_seq_id' => "",
            // "org_req_seq_id" =>  $payment->req_seq_id, // 原交易流水号
            // 'org_req_date' => $allInput['org_req_date'], // 交易日期
            // 'org_hf_seq_id' => $allInput['org_hf_seq_id'], // 全局交易流水号
            // 'acct_split_bunch' => $allInput['acct_split_bunch'], //必填 分账信息
        ], $allInput));
        $res  = $this->client->post("/v2/trade/acctpayment/pay/query", [
            "json" => $json,
        ]);

        return json_decode($res->getBody()->getContents(), true);
    }

    // 余额支付退款
    // https://paas.huifu.com/partners/api/#/yuer/api_acctpaytk
    public function V2TradeAcctpaymentRefund($allInput)
    {
        $json = $this->createBody([
            array_merge([
                'huifu_id'   => $this->sys_id,
            ], $allInput),
        ]);

        $res = $this->client->post("/v2/trade/acctpayment/refund", [
            "json" => $json,
        ]);

        return json_decode($res->getBody()->getContents(), true);
    }

    // 余额支付退款查询
    // https://paas.huifu.com/partners/api/#/yuer/api_acctpaytk
    public function V2TradeAcctpaymentRefundQuery($allInput)
    {
        $json = $this->createBody(array_merge([
            'huifu_id' => $this->sys_id,
        ], $allInput));
        $res = $this->client->post("/v2/trade/acctpayment/refund/query", [
            "json" => $json,
        ]);

        return json_decode($res->getBody()->getContents(), true);
    }

    // 账户余额信息查询
    // https://paas.huifu.com/partners/api/#/jyjs/api_jyjs_yuexxcx
    public function V2TradeAcctpaymentBalanceQuery($allInput)
    {
        $json = $this->createBody(array_merge([
            'huifu_id' => $this->sys_id,
        ], $allInput));
        $res = $this->client->post("/v2/trade/acctpayment/balance/query", [
            "json" => $json,
        ]);

        return json_decode($res->getBody()->getContents(), true);
    }
}
