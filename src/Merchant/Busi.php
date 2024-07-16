<?php
namespace Zhangjiahao93\Huifu\Merchant;

trait Busi
{
    // 微信实名认证接口
    public function V2MerchantBusiRealnameQuery()
    {
        $json = $this->createBody([
            'req_seq_id' => genReqSeqID(),
            'req_date' => date("Ymd"),
            'huifu_id' => $this->sys_id,
        ]);
        $res = $this->client->post("/v2/merchant/busi/realname/query", [
            "json" => $json,
        ]);

        return json_decode($res->getBody()->getContents(), true);
    }

    //微信商户配置
    public function V2MerchantBusiConfig($allInput)
    {
        $json = $this->createBody([
            'req_seq_id' => genReqSeqID(),
            'req_date' => date("Ymd"),
            'huifu_id' => $this->sys_id,
            'fee_type' => '02',
            'wx_applet_app_id' => $allInput['wx_applet_app_id'],
            'wx_applet_secret' => $allInput['wx_applet_secret'],
        ]);
        // var_dump(json_encode($json));
        $res = $this->client->post("/v2/merchant/busi/config", [
            "json" => $json,
        ]);
        return json_decode($res->getBody()->getContents(), true);
    }
}
