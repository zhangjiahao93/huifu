<?php
namespace Zhangjiahao93\Huifu\Trade;

trait Settlement
{
    // 取现
    public function V2tradeSettlementEnchashment($allInput)
    {
        $json = $this->createBody([
            'huifu_id'            => $allInput['huifu_id'],
            'req_seq_id'          => "",
            'req_date'            => date("Ymd"),
            'cash_amt'            => $allInput['cash_amt'],
            'into_acct_date_type' => $allInput['into_acct_date_type'],
            'token_no'            => $allInput['token_no'],
        ]);
        $res = $this->client->post("/v2/trade/settlement/enchashment", [
            "json" => $json,
        ]);

        return json_decode($res->getBody()->getContents(), true);
    }
}
