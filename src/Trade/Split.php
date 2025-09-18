<?php
namespace Zhangjiahao93\Huifu\Trade;

trait Split
{
    // 取现
    public function V2tradeTransSplitQuery($allInput)
    {
        $json = $this->createBody(array_merge([
            'huifu_id' => $this->sys_id,
        ], $allInput));
        return $this->post("/v2/trade/trans/split/query", $json);
    }
}
