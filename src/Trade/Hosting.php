<?php
namespace Zhangjiahao93\Huifu\Trade;


trait Hosting
{
    // H5、PC预下单接口
    // https://paas.huifu.com/open/doc/api/#/cpjs/api_cpjs_hosting
    public function V2TradeHostingPaymentPreorder($allInput)
    {
        $json = $this->createBody(array_merge([
            'huifu_id'      => $this->sys_id,
            // // 'req_seq_id' => genId(),
            // 'req_date' => date("Ymd"),
            // 'trans_amt' => "0.02",
            // 'goods_desc' => "调试0.02",
            // 'pre_order_type'=>"1",
            // 'hosting_data'=> json_encode([
            //     'project_title' => '汇付支付',
            //     'project_id' => 'PROJECTID2025082962545945',
            // ]),
            // 'notify_url'=> "https://gongcun.quicknew.top/api/wechat/transfer/notify",
        ], $allInput));
        // var_dump($json);
        $res = $this->client->post("/v2/trade/hosting/payment/preorder", [
            "json" => $json,
        ]);
        return json_decode($res->getBody()->getContents(), true);
    }

   
}
