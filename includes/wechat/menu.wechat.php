<?php

/**
 * Created by PhpStorm.
 * User: druphliu
 * Date: 14-1-26
 * Time: 下午2:31
 */
class MenuWeChat
{
    private $url;

    function __construct($access_token)
    {
        $this->url = sprintf("https://api.weixin.qq.com/cgi-bin/menu/create?access_token=%s", $access_token);
    }

    function create_menu()
    {
        $menu = $button_help = $button_about = $button_order = $sub_button = array();
        $button_help = array('type' => 'click', 'name' => '帮助', 'key' => 'help');
        $sub_button_order = array(
            array('type' => 'click', 'name' => '所有订单', 'key' => 'order_list'),
            array('type' => 'click', 'name' => '查询订单', 'key' => 'order_view'),
            array('type' => 'click', 'name' => '取消订单', 'key' => 'order_cancel'),
            array('type'=>'click', 'name'=>'我要催单', 'key'=>'order_urge')
        );
        $button_order = array('name' => '订单管理', 'sub_button' => $sub_button_order);
        $button_order_food = array('type' => 'click', 'name'=>'我要订餐', 'key'=>'order_food');
        $menu['button'] = array($button_order_food, $button_order, $button_help);
        $menu_json = preg_replace("#\\\u([0-9a-f]{4}+)#ie", "iconv('UCS-2', 'UTF-8', pack('H4', '\\1'))", ecm_json_encode($menu));
        import('curl.lib');
        $curl = new curl();
        $curl->setOpt(CURLOPT_RETURNTRANSFER, TRUE);
        $curl->setOpt(CURLOPT_SSL_VERIFYPEER, FALSE);
        $curl->post($this->url, $menu_json);
        $result = ecm_json_decode($curl->response);
        if ($result->errcode == 0) {
            $status = 0;
        } else {
            $status = $result->errcode;
        }
        return $status;
    }
} 