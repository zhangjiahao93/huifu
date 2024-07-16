<?php
namespace Zhangjiahao93\Huifu\Trade;

use Exception;

trait Settlement
{
    // 取现
    public function V2tradeSettlementEnchashment($allInput)
    {
        $validator = validator()->make(
            $allInput,
            [
                'token_no' => 'required', //取现卡序列号：绑定取现卡后可获取取现卡序列号
                'huifu_id' => 'required',
                'cash_amt' => 'required', //单位元
                'into_acct_date_type' => 'required', //到账日期类型: D0当天 T1：次工作日到账；D1：次自然日到账；DM：当日到账；到账资金不包括当天的交易资金；
            ],
            [],
        );
        if ($validator->fails()) {
            throw new Exception($validator->errors()->first());
        }
        //
        $json = $this->createBody([
            'huifu_id' => $allInput['huifu_id'],
            'req_seq_id' => genReqSeqID(),
            'req_date' => date("Ymd"),
            'cash_amt' => $allInput['cash_amt'],
            'into_acct_date_type' => $allInput['into_acct_date_type'],
            'token_no' => $allInput['token_no'],
        ]);
        $res = $this->client->post("/v2/trade/settlement/enchashment", [
            "json" => $json,
        ]);

        return json_decode($res->getBody()->getContents(), true);
    }
}
