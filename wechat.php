<?php
/**
 * Created by PhpStorm.
 * User: druphliu
 * Date: 14-1-22
 * Time: 下午2:52
 */
//error_reporting(0);
define('ROOT_PATH', dirname(__FILE__));
/* 定义配置信息 */
include(ROOT_PATH . '/eccore/ecmall.php');
ecm_define(ROOT_PATH . '/data/config.inc.php');
/* 加载初始化文件 */
require(ROOT_PATH . '/eccore/controller/app.base.php'); //基础控制器类
require(ROOT_PATH . '/eccore/model/model.base.php'); //模型基础类
include(ROOT_PATH . '/includes/global.lib.php');
include(ROOT_PATH . '/includes/libraries/time.lib.php');
include(ROOT_PATH . '/includes/ecapp.base.php');
include(ROOT_PATH . '/app/frontend.base.php');


include(ROOT_PATH . '/includes/wechat/token.wechat.php');
include(ROOT_PATH . '/includes/wechat/protocols.wechat.php');
include(ROOT_PATH . '/includes/wechat/menu.wechat.php');
include(ROOT_PATH . '/includes/wechat/handler.wechat.php');
include(ROOT_PATH . '/includes/wechat/check.wechat.php');
include(ROOT_PATH . '/includes/wechat/oauth.wechat.php');
include(ROOT_PATH . '/includes/wechat/response.wechat.php');
include(ROOT_PATH. '/includes/wechat/order.response.wechat.php');
include(ROOT_PATH. '/includes/wechat/service.wechat.php');
/* 载入配置项 */
$setting =& af('settings');
Conf::load($setting->getAll());
if ($_GET && $_GET['action']) {
    switch ($_GET['action']) {
        case "menu":
            $token = new TokenWechat(APPID, APPSECRET);
            $access_token = $token->get_token();
            $menu = new MenuWeChat($access_token);
            $result = $menu->create_menu();
            echo $result;
            break;
        default:
            //test case
            $post = new WeChatTextService('o5hUjuPNWekp7T7LBzA__0rybdGs','test');
            $post->handle_post();
            break;
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $post_string = $GLOBALS["HTTP_RAW_POST_DATA"];
    $builder = new WeChatRequestBuilder();
    $request = $builder->builder($post_string);
    switch ($request->msg_type) {
        case 'event':
            $response = EventHandler::on_request($request);
            break;
        case 'text':
            $response = TextHandler::on_request($request);
            break;
    }
} else {
    $check = new checkWechat();
    $response = $check->valid();
}
echo $response;
?>