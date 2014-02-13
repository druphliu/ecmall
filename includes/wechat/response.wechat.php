<?php

/**
 * Created by PhpStorm.
 * User: druphliu
 * Date: 14-2-16
 * Time: 下午10:42
 */
class TextResponse
{
    static function response($request)
    {
        $content = $request->content;
        $patterns['help'] = "/^\?$/";
        $patterns['jump'] = "/^\#$/";
        $patterns['order_food'] = "/^dc(.*)/";
        $patterns['order_list'] = "/^ddlb$/";
        $patterns['order_view'] = "/^ddcx(.*)/";
        $patterns['order_cancel'] = "/^ddqx(.*)/";
        $patterns['order_urge'] = "/^cd(.*)/";
        //处理是否处于会话状态中
        $openid = $request->from_user_name;
        $order_response = new OrderResponse($openid);
        if (!$order_response->is_bind($openid)) {
            return $order_response->bind_response($request);
        }
        $status = $order_response->get_status();
        $result = self::content_match($content);
        $dialog = $result['message'] ? $result['message'] : $content;
        $key = $result['key'] ? $result['key'] : null;
        if ($key == "help" && !empty($matches)) {
            return OrderResponse::help($request);
        } elseif (($key == "order_list" && !empty($result)) || ($status && $status[OrderResponse::$KEY_STATUS] == OrderResponse::$status_order_list)) {
            $order_list = new OrderList($openid);
            $response = $order_list->text_handle($dialog);
        } elseif (($key == "order_food" && !empty($result)) || ($status && $status[OrderResponse::$KEY_STATUS] == OrderResponse::$status_order_food)) {
            $order_food = new OrderFood($openid);
            $response = $order_food->text_handle($dialog);
        } elseif (($key == "order_view" && !empty($result)) || ($status && $status[OrderResponse::$KEY_STATUS] == OrderResponse::$status_order_view)) {
            $order_view = new OrderView($openid);
            $response = $order_view->text_handle($dialog);
        } elseif (($key == "cancel" && !empty($result)) || ($status && $status[OrderResponse::$KEY_STATUS] == OrderResponse::$status_order_cancel)) {
            $order_cancel = new OrderCancel($openid);
            $response = $order_cancel->text_handle($dialog);
        } elseif (($key == "order_urge" && !empty($result)) || ($status && $status[OrderResponse::$KEY_STATUS] == OrderResponse::$status_order_urge)) {
            $order_urge = new OrderUrge($openid);
            $response = $order_urge->text_handle($dialog);
        } elseif ($key == "help" && !empty($matches)) {
            return OrderResponse::jump($request);
        } else {
            //TODO 处理其他消息,包括客户乱发的消息就显示help信息

        }
        $response_obj = new WeChatTextResponse($response);
        $xml = $response_obj->_to_xml($request);
        return $xml;
    }

    function content_match($content)
    {
        $result = array();
        $patterns['help'] = "/^\?$/";
        $patterns['jump'] = "/^\#$/";
        $patterns['order_food'] = "/^dc(.*)/";
        $patterns['order_list'] = "/^ddlb$/";
        $patterns['order_view'] = "/^ddcx(.*)/";
        $patterns['order_cancel'] = "/^ddqx(.*)/";
        $patterns['order_urge'] = "/^cd(.*)/";
        foreach ($patterns as $key => $pattern) {
            preg_match($pattern, $content, $matches);
            if (!empty($matches)) {
                $result['message'] = $matches[1] ? $matches[1] : null;
                $result['key'] = $key;
            }
        }
        return $result;
    }
}

class ClickResponse
{
    static function order_food($request)
    {
        $order_food = new OrderFood($request->from_user_name);
        $status = array(OrderResponse::$KEY_STATUS => OrderResponse::$status_order_food);
        $order_food->update_status($status);
        $response = $order_food->click_handle();
        $response_obj = new WeChatTextResponse($response);
        $xml = $response_obj->_to_xml($request);
        return $xml;
    }

    static function order_list($request)
    {
        $order_list = new OrderList($request->from_user_name);
        $status[OrderResponse::$KEY_STATUS] = OrderResponse::$status_order_list;
        $order_list->update_status($status);
        $response = $order_list->click_handle();
        $response_obj = new WeChatTextResponse($response);
        $xml = $response_obj->_to_xml($request);
        return $xml;
    }

    static function order_cancel($request)
    {
        $order_cancel = new OrderCancel($request->from_user_name);
        $status[OrderResponse::$KEY_STATUS] = OrderResponse::$status_order_cancel;
        $order_cancel->update_status($status);
        $response = $order_cancel->click_handle();
        $response_obj = new WeChatTextResponse($response);
        $xml = $response_obj->_to_xml($request);
        return $xml;
    }

    static function order_urge($request)
    {
        $order_urge = new OrderUrge($request->from_user_name);
        $status[OrderResponse::$KEY_STATUS] = OrderResponse::$status_order_urge;
        $order_urge->update_status($status);
        $response = $order_urge->click_handle();
        $response_obj = new WeChatTextResponse($response);
        $xml = $response_obj->_to_xml($request);
        return $xml;
    }

    static function order_view($request)
    {
        $order_view = new OrderView($request->from_user_name);
        $status[OrderResponse::$KEY_STATUS] = OrderResponse::$status_order_view;
        $order_view->update_status($status);
        $response = $order_view->click_handle();
        $response_obj = new WeChatTextResponse($response);
        $xml = $response_obj->_to_xml($request);
        return $xml;
    }
}





