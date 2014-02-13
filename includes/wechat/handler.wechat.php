<?php

/**
 * Created by PhpStorm.
 * User: druphliu
 * Date: 14-2-9
 * Time: 下午9:57
 */
class TextHandler
{
    static function on_request($request)
    {
        switch ($request->text_type) {
            case WeChatCommonRequest::$type_text:
                $handle = TextResponse::response($request);
                break;
            case WeChatCommonRequest::$type_image:
                //TODO image request response
                break;
            case WeChatCommonRequest::$type_voice:
                //TODO voice request response
                break;
            case WeChatCommonRequest::$type_video:
                //TODO video request response
                break;
            case WeChatCommonRequest::$type_location:
                //TODO location request response
                break;
            case WeChatCommonRequest::$type_link:
                //TODO link request response
                break;
        }
        return $handle;
    }

}

class EventHandler
{
    public function on_request($request)
    {
        switch ($request->event_type) {
            case WeChatEventRequest::$type_subscribe:
                $handle = self::handle_subscribe($request);
                break;
            case WeChatEventRequest::$type_menu:
                switch ($request->event_key) {
                    case "help":
                        $handle = Response::help($request);
                        break;
                    case "order_food":
                        $handle = ClickResponse::order_food($request);
                        break;
                    case "order_list":
                        $handle = ClickResponse::order_list($request);
                        break;
                    case "order_cancel":
                        $handle = ClickResponse::order_cancel($request);
                        break;
                    case "order_urge":
                        $handle = ClickResponse::order_urge($request);
                        break;
                    case "order_view":
                        $handle = ClickResponse::order_view($request);
                        break;
                }
                break;
            case WeChatEventRequest::$type_location:
                //TODO location response
                break;
            case WeChatEventRequest::$type_scan:
                //TODO scan response
                break;
        }
        return $handle;
    }

    private function handle_subscribe($request)
    {
        if ($request->is_subscribe) {
            $content = '你好，欢迎关注！';
            $data[0] = array('title' => '', 'description' => $content, 'url' => 'http://www.zhaigow.com.cn', 'pic_url' => 'http://img3.cache.netease.com/photo/0001/2014-02-09/9KKRH71500AO0001.jpg');
            $data[1] = array('title' => 'test', 'description' => '描述描述描述描述描述', 'url' => 'http://www.zhaigow.com.cn', 'pic_url' => '');
            $response = new WeChatArticleResponse($data);
            $xml = $response->_to_xml($request);
            return $xml;
        } else {
            $_wechat_mod = & m('memberwechat');
            $wechat = $_wechat_mod->get("openid= '$request->from_user_name'");
            if ($wechat) {
                $_wechat_mod->drop("openid= '$request->from_user_name'");
            }
        }
    }
}