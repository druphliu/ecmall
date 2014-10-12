<?php

/**
 * wechat base.
 * User: druphliu
 * Date: 14-1-22
 * Time: 下午4:24
 */
class checkWechat
{
    public function valid()
    {
        $response = false;
        $echoStr = $_GET["echostr"];

        //valid signature , option
        if ($this->checkSignature()) {
            $response = $echoStr;
        }
        return $response;
    }

    private function checkSignature()
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);

        if ($tmpStr == $signature) {
            return true;
        } else {
            return false;
        }
    }


}