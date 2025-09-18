<?php
namespace Zhangjiahao93\Huifu\Merchant;

trait Basicdata
{
    // 商户详细信息查询
    public function V2MerchantBasicdataQuery()
    {
        $json = $this->createBody([
            'huifu_id'   => $this->sys_id,
            'req_seq_id' => "",
            'req_date'   => date("Ymd"),
        ]);


        $res = $this->client->post("/v2/merchant/basicdata/query", [
            "json" => $json,
        ]);

        return json_decode($res->getBody()->getContents(), true);
    }

    // 修改商户信息
    public function V2MerchantBasicdataModify($allInput)
    {
        $json = $this->createBody(array_merge([
            'upper_huifu_id' => $this->sys_id,
            'huifu_id'       => $this->sys_id,
            'req_seq_id'     => "",
            'req_date'       => date("Ymd"),
        ], $allInput));
        $res = $this->client->post("/v2/merchant/basicdata/modify", [
            "json" => $json,
        ]);

        return json_decode($res->getBody()->getContents(), true);
    }

}
