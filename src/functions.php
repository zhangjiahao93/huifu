<?php
use Hyperf\Context\ApplicationContext;

if (!function_exists('validator')) {
    function validator()
    {
        return ApplicationContext::getContainer()->get(\Hyperf\Validation\ValidatorFactory::class);
    }
}
if (!function_exists('genReqSeqID')) {
    function genReqSeqID()
    {
        $arr = explode(' ', microtime());
        return date("YmdHis") . ltrim($arr[0], "0.") . '1' . rand(1000, 9999);
    }
}
