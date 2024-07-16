<?php
namespace Zhangjiahao93\Huifu\Trade;

use Exception;

trait Acctpayment
{
    // 余额支付
    public function V2TradeAcctpaymentPay($allInput)
    {
        $validator = validator()->make(
            $allInput,
            [
                'out_huifu_id' => 'required', // 出款方
                'ord_amt' => 'required', //单位元
                'acct_split_bunch' => 'required', // 分佣对象
                'risk_check_data' => 'required',
                'fund_type' => 'required',
                'trans_fee_take_flag' => 'required',
            ],
            [],
        );
        if ($validator->fails()) {
            throw new Exception($validator->errors()->first());
        }
        //
        $json = $this->createBody(array_merge([
            'req_seq_id' => genReqSeqID(),
            'req_date' => date("Ymd"),
        ], $allInput));
        return $this->post("/v2/trade/acctpayment/pay", $json);
    }

    // 余额支付查询
    // https://paas.huifu.com/partners/api/#/yuer/api_acctpaycx
    public function V2TradeAcctpaymentPayQuery($allInput)
    {
        $validator = validator()->make(
            $allInput,
            [
                'huifu_id' => 'required',
                'org_req_date' => 'required',
                'org_req_seq_id' => 'required',
            ],
            [],
        );
        if ($validator->fails()) {
            throw new Exception($validator->errors()->first());
        }
        //
        $json = $this->createBody($allInput);
        $res = $this->client->post("/v2/trade/acctpayment/pay/query", [
            "json" => $json,
        ]);

        return json_decode($res->getBody()->getContents(), true);
    }

    // 余额支付退款
    // https://paas.huifu.com/partners/api/#/yuer/api_acctpaytk
    public function V2TradeAcctpaymentRefund($allInput)
    {
        $validator = validator()->make(
            $allInput,
            [
                'huifu_id' => 'required', // 商户号
                'req_date' => 'required', // 请求日期
                'req_seq_id' => 'required', //请求流水号
                'org_req_date' => 'required',
                'org_req_seq_id' => 'required',
                'ord_amt' => 'required', //退款金额
            ],
            [],
        );
        if ($validator->fails()) {
            throw new Exception($validator->errors()->first());
        }
        //
        $json = $this->createBody([
            'req_seq_id' => genReqSeqID(),
            'req_date' => date("Ymd"),
            'huifu_id' => $allInput['huifu_id'],
            'org_req_date' => $allInput['org_req_date'],
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
        $validator = validator()->make(
            $allInput,
            [
                'huifu_id' => 'required', // 商户号
                'org_req_date' => 'required', //退款请求日期
                'org_req_seq_id' => 'required', //退款请求流水号
            ],
            [],
        );
        if ($validator->fails()) {
            throw new Exception($validator->errors()->first());
        }
        //
        $json = $this->createBody([
            'huifu_id' => $allInput['huifu_id'],
            'org_req_date' => $allInput['org_req_date'],
            'org_req_seq_id' => $allInput['org_req_seq_id'],
        ]);
        $res = $this->client->post("/v2/trade/acctpayment/refund/query", [
            "json" => $json,
        ]);

        return json_decode($res->getBody()->getContents(), true);
    }

    // 账户余额信息查询
    // https://paas.huifu.com/partners/api/#/jyjs/api_jyjs_yuexxcx
    public function V2TradeAcctpaymentBalanceQuery($allInput)
    {
        $json = $this->createBody([
            'huifu_id' => $allInput['huifu_id'],
            'req_seq_id' => genReqSeqID(),
            'req_date' => date("Ymd"),
        ]);
        $res = $this->client->post("/v2/trade/acctpayment/balance/query", [
            "json" => $json,
        ]);

        return json_decode($res->getBody()->getContents(), true);
    }
}
