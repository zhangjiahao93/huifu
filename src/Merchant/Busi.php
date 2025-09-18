<?php
namespace Zhangjiahao93\Huifu\Merchant;

trait Busi
{
    // 微信实名认证接口
    public function V2MerchantBusiRealnameQuery($allInput)
    {
        $json = $this->createBody(array_merge([
            'huifu_id' => $this->sys_id,
        ], $allInput));
        $res = $this->client->post("/v2/merchant/busi/realname/query", [
            "json" => $json,
        ]);

        return json_decode($res->getBody()->getContents(), true);
    }

    //微信商户配置
    public function V2MerchantBusiConfig($allInput)
    {
        // $json = $this->createBody([
        //     'req_seq_id' => "",
        //     'req_date' => date("Ymd"),
        //     'huifu_id' => $this->sys_id,
        //     'fee_type' => '02',
        //     'wx_applet_app_id' => $allInput['wx_applet_app_id'],
        //     'wx_applet_secret' => $allInput['wx_applet_secret'],
        // ]);
        $json = $this->createBody(array_merge([
            'huifu_id' => $this->sys_id,
        ], $allInput));
        // var_dump(json_encode($json));
        $res = $this->client->post("/v2/merchant/busi/config", [
            "json" => $json,
        ]);
        return json_decode($res->getBody()->getContents(), true);
    }

    /**
     * 交易结算对账文件配置 -- 支持定期生成
     * https://paas.huifu.com/open/doc/api/#/jyjs/api_jyjs_wjpz
     */
    public function V2MerchantBusiBillConfig($allInput=[])
    {
        $json = $this->createBody(array_merge([
            'req_date' => date("Ymd"),
            'req_seq_id' => "",
            'huifu_id' => $this->sys_id,
            'recon_send_flag'=>"Y",
            'file_type'=>"1,2,3,4",
        ], $allInput));

        return $this->post("/v2/merchant/busi/bill/config", $json);
    }



    /**
     * 交易结算对账单配置查询
     * https://paas.huifu.com/open/doc/api/#/jyjs/api_jyjs_wjpzcx
     * @return array
     */
    public function V2MerchantBusiBillQuery($allInput=[])
    {
        $json = $this->createBody(array_merge([
            'req_date' => date("Ymd"),
            'req_seq_id' => "",
            'huifu_id' => $this->sys_id,
        ], $allInput));

        return $this->post("/v2/merchant/busi/bill/query", $json);
    }
}
