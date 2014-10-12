<?php
/**
 * Created by PhpStorm.
 * User: druphliu
 * Date: 14-2-9
 * Time: 下午2:53
 */
class TokenWechat
{
    private $appid, $appsecret, $file, $url;
    private $expires_in = 7200;

    function __construct($appid, $appsecret)
    {
        $this->appid = $appid;
        $this->appsecret = $appsecret;
        $this->url = sprintf("https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s", $this->appid, $this->appsecret);
        $this->file = ROOT_PATH . '/data/wechat.token.php';
    }

    function get_token()
    {
        $file_time = file_exists($this->file) ? filemtime($this->file) : 0;
        if ($file_time && (time() - $file_time) < $this->expires_in) {
            $token = file_get_contents($this->file);
            if (!$token) {
                $this->update_token();
            }
        } else {
            $token = $this->update_token();
        }
        return $token;
    }

    function update_token()
    {
        import("curl.lib");
        $curl = new curl();
        $curl->setOpt(CURLOPT_RETURNTRANSFER, TRUE);
        $curl->setOpt(CURLOPT_SSL_VERIFYPEER, FALSE);
        $curl->get($this->url);
        $response = $curl->response;
        $json = ecm_json_decode($response);
        file_put_contents($this->file, $json->access_token);
        return $json->access_token;
    }
}