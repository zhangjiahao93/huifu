<?php
namespace Zhangjiahao93\Huifu\Trade;

trait Check
{
    // 交易结算对账单查询
    // https://paas.huifu.com/open/doc/api/#/jyjs/api_jyjs_wjcx
    // file_type_query :
    // 1、日对账单，包括日结算对账单、日分账对账单、日出金对账单
    // 2、日交易数据，包括全域资金入账明细单；
    // 3、月结算对账单
    // 4、月交易数据
    // 13、电子账户资金变动明细文件(只支持渠道商生成获取)
    public function V2TradeCheckFilequery($allInput)
    {
        $json = $this->createBody(array_merge([
            'req_date'        => date("Ymd"),
            'req_seq_id'      => "",
            'file_date'       => date("Ymd"),
            'file_type_query' => 3,
            'huifu_id'        => $this->sys_id,
        ], $allInput));

        return $this->post("/v2/trade/check/filequery", $json);
    }

    /**
     * 交易结算对账文件重新生成
     * https://api.huifu.com/v2/trade/check/replay
     *
    file_type:
    1、日结算对账单
    2、日交易数据
    3、月结算对账单
    4、月交易数据
     */
    public function V2TradeCheckReplay($allInput)
    {
        $json = $this->createBody(array_merge([
            'req_date'   => date("Ymd"),
            'req_seq_id' => "",
            'file_type'  => 2,
            'huifu_id'   => $this->sys_id,
        ], $allInput));

        return $this->post("/v2/trade/check/replay", $json);
    }

}
