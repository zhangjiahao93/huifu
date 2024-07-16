<?php
namespace Zhangjiahao93\Huifu\Trade;

use Exception;

trait Split
{
    // 取现
    public function V2tradeTransSplitQuery($allInput)
    {
        $validator = validator()->make(
            $allInput,
            [
                'huifu_id' => 'required',
                'hf_seq_id' => 'required', //分账交易汇付全局流水号
                'ord_type' => 'required',
            ],
            [],
        );
        if ($validator->fails()) {
            throw new Exception($validator->errors()->first());
        }
        //
        $json = $this->createBody([
            'huifu_id' => $allInput['huifu_id'],
            'hf_seq_id' => $allInput['hf_seq_id'],
            'ord_type' => $allInput['ord_type'],
            'req_seq_id' => genReqSeqID(),
            'req_date' => date("Ymd"),
        ]);

        return $this->post("/v2/trade/trans/split/query", $json);
    }
}
