<?php

/**
 * Created by PhpStorm.
 * User: druphliu
 * Date: 14-2-10
 * Time: 下午1:01
 */
class OAuth extends ECBaseApp
{
    private $TAG_BIND_IS_EXIT = -1;
    private $TAG_PORT_ERROR = -2;
    private $TAG_PASSWORD_ERROR = -3;
    private $TAG_UNKNOWW_ERROR = -4;
    private $TAG_OK = 1;
    public $appid = APPID;
    public $redirect_uri;
    private $API_BASE = "snsapi_base";
    private $API_USERINFO = "snsapi_userinfo";
    private $state = "STATE";
    private $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=%s&redirect_uri=%s&response_type=code&scope=%s&state=%s#wechat_redirect";
    public $openid;

    function curl_get($url)
    {
        import("curl.lib");
        $curl = new curl();
        $curl->setOpt(CURLOPT_RETURNTRANSFER, TRUE);
        $curl->setOpt(CURLOPT_SSL_VERIFYPEER, FALSE);
        $curl->get($url);
        $response = ecm_json_decode($curl->response);
        if ($response->errcode) {
            $this->show_warning($response->errcode);
            return;
        } else {
            return ($response);
        }
    }

    function bind($username, $password)
    {
        $this->redirect_uri = "";
//        $url = sprintf($this->url, $this->appid, $this->redirect_uri, $this->API_BASE, $this->state);
//        $response = $this->curl_get($url);
//        $this->openid = $response->openid;
        $this->openid = $_SESSION['openid'];
        $ms =& ms();
        $user_id = $ms->user->auth($username, $password);
        $_wechat_mod = & m('memberwechat');
        $wechat = $_wechat_mod->get("user_id= '$user_id'");
        if ($user_id && !$wechat) {
            $data = array('user_id' => $user_id, 'openid' => $this->openid, 'datetime' => gmtime());
            $_wechat_mod->add($data);
            $result = $_wechat_mod->get_error() ? $this->TAG_PORT_ERROR : $this->TAG_OK;
        } else {
            if ($ms->user->get_error()) {
                $result = $this->TAG_PASSWORD_ERROR;
            } else if ($wechat) {
                $result = $this->TAG_BIND_IS_EXIT;
            } else {
                $result = $this->TAG_UNKNOWW_ERROR;
            }
        }
        return $result;
    }
}