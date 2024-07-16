<?php
namespace Zhangjiahao93\Huifu\Merchant;

trait Settle
{
    // 子账户开通
    // https://paas.huifu.com/partners/api/#/zhgl/api_zhgl_zhpz_kyc
    public function V2MerchantSettleConfig($allInput)
    {
        $json = $this->createBody(array_merge([
            'req_seq_id' => genReqSeqID(),
            'req_date' => date("Ymd"),
            'huifu_id' => $this->sys_id,
        ], $allInput));
        $res = $this->client->post("/v2/merchant/settle/config", [
            "json" => $json,
        ]);

        return json_decode($res->getBody()->getContents(), true);
    }

    // 修改子账户
    // https://paas.huifu.com/partners/api/#/zhgl/api_zhgl_xgzh_kyc
    public function V2MerchantSettleModify($allInput)
    {
        $json = $this->createBody(array_merge([
            'req_seq_id' => genReqSeqID(),
            'req_date' => date("Ymd"),
            'huifu_id' => $this->sys_id,
        ], $allInput));
        $res = $this->client->post("/v2/merchant/settle/modify", [
            "json" => $json,
        ]);

        return json_decode($res->getBody()->getContents(), true);
    }
}
