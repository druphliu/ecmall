<?php

/**
 * Created by PhpStorm.
 * User: druphliu
 * Date: 14-2-22
 * Time: 下午3:18
 */
class Response
{
    function is_bind($openid)
    {
        $_wechat_mod = & m('memberwechat');
        $wechat = $_wechat_mod->get("openid= '$openid'");
        $result = $wechat ? 1 : 0;
        return $result;
    }

    function bind_response($request)
    {
        $bing_url = site_url()."/index.php?app=wechat&act=oauth&openid=".$request->from_user_name;
        $content = "还未绑定账户，点此进行绑定:\n<a href='$bing_url'>绑定</a>";
        $response = new WeChatTextResponse($content);
        $xml = $response->_to_xml($request);
        return $xml;
    }

    static public function help($request)
    {
        $content = "您可以直接使用底部菜单选择相应功能。或者尝试以下命令：\ncxdd：查询所有订单\nddcx+订单号：查询您的订单状态\nddqx+订单号：取消您的订单\ncd+订单号：催单\ndc+餐饮名：快捷订餐\n";
        $response = new WeChatTextResponse($content);
        $xml = $response->_to_xml($request);
        return $xml;
    }

    static public function jump($request)
    {
        $cache_file = sprintf(ROOT_PATH . "/temp/wechat/%s.php", $request->from_user_name);
        if (file_exists($cache_file)) {
            @unlink($cache_file);
            $content = "已经退出当前操作!";
        } else {
            $content = "当前无任何操作，你可通过点击菜单或者命令来进行相关操作";
        }
        $response = new WeChatTextResponse($content);
        $xml = $response->_to_xml($request);
        return $xml;
    }
}

class OrderResponse extends Response
{
    public static $KEY_STATUS = "STATUS";
    public static $KEY_SUB_STATUS = "SUB_STATUS";
    public static $status_order_list = 1;
    public static $status_order_food = 2;
    public static $status_order_view = 3;
    public static $status_order_urge = 4;
    public static $status_order_cancel = 5;

    public $openid;
    public $cache_file;
    public $wechat_info;

    function __construct($openid)
    {
        $this->openid = $openid;
        $this->cache_file = sprintf(ROOT_PATH . "/temp/wechat/%s.php", $openid);
        $wechat_member =& m("memberwechat");
        $this->wechat_info = $wechat_member->get_info($openid);
    }

    public function get_address()
    {
        $model_address =& m('address');
        $address = $model_address->get(array('conditions' => 'user_id = ' . $this->wechat_info['user_id']));
        return $address;
    }

    public function update_status($status)
    {
        $content = "<?php\r\nreturn " . var_export($status, true) . "; \r\n?>";
        $handle = fopen($this->cache_file, "w+");
        fwrite($handle, $content);
        fclose($handle);
    }

    public function delete_status()
    {
        @unlink($this->cache_file);
    }

    public function  get_status()
    {
        $status = file_exists($this->cache_file) ? include($this->cache_file) : '';
        return $status;
    }

    /**
     * @param $param array()
     * @return mixed
     */
    public function get_order_info($param)
    {
        $condition = $comm = '';
        if (is_array($param)) {
            foreach ($param as $key => $p) {
                $condition .= $comm . $key . "='" . $p . "'";
                $comm = " AND ";
            }
        } else {
            $condition = $param;
        }
        $model_order =& m('order');
        $order_info = $model_order->get(array(
            'fields' => "*, order.add_time as order_add_time",
            'conditions' => $condition . " AND buyer_id=" . $this->wechat_info['user_id'],
            'join' => 'belongs_to_store',
        ));
        if (!$order_info) {
            return;
        }
        /* 调用相应的订单类型，获取整个订单详情数据 */
        $order_type =& ot($order_info['extension']);
        $order_detail = $order_type->get_order_detail($order_info['order_id'], $order_info);
        $order_info['goods_list'] = $order_detail['data']['goods_list'];
        return $order_info;
    }

    /**
     * @param $order_info array()
     * @param $status
     * @return string
     */
    function update_order_status($order_info, $status)
    {
        $model_order =& m('order');
        switch ($status) {
            case ORDER_CANCELED:
                //未确认的才能取消
                if ($order_info['status'] == ORDER_PENDING) {
                    $model_order->edit($order_info['order_id'], array('status' => ORDER_CANCELED));
                    $response = "订单已取消";
                } else {
                    $response = "卖家已确认无法取消";
                }
                break;
            case ORDER_FINISHED:
                if ($order_info['status'] == ORDER_SHIPPED) {
                    $model_order->edit($order_info['order_id'], array('status' => ORDER_FINISHED));
                    $response = "订单已完成，登录网站可进行评价";
                } else {
                    $response = "未完成的订单，无法确认";
                }

                break;
        }
        return $response;
    }

    function get_order_list()
    {
        $model_order =& m("order");
        $order_list = $model_order->findAll(array(
            'conditions' => "buyer_id=" . $this->wechat_info['user_id'] . " AND status in (11,20,30)",
            'fields' => 'this.*',
            'count' => true,
            'order' => 'add_time DESC',
            'include' => array(
                'has_ordergoods', //取出商品
            ),
        ));
        return $order_list;
    }

    function search_food($region_id, $keyword)
    {
        $goods_mod =& m('goods');
        $conditions = " g.if_show = 1 AND g.closed = 0 AND s.state = 1"; // 上架且没有被禁售，店铺是开启状态,
        $conditions .= " AND s.region_id = '" . $region_id . "' AND g.goods_name like '%$keyword%'";
        $goods_list = $goods_mod->get_list(array(
            'conditions' => $conditions,
            'limit' => "0, 10",
        ));
        return $goods_list;
    }

    function get_goods_info($goods_id)
    {
        $cache_server =& cache_server();
        $key = 'page_of_goods_' . $goods_id;
        $data = $cache_server->get($key);
        if ($data === false) {
            $data = array('id' => $goods_id);
            /* 商品信息 */
            $goods_mod =& m("goods");
            $goods = $goods_mod->get_info($goods_id);
            if (!$goods || $goods['if_show'] == 0 || $goods['closed'] == 1 || $goods['state'] != 1) {
                // 'goods_not_exist';
                return false;
            }
            $data['goods'] = $goods;
            $cache_server->set($key, $data, 1800);
        }
        return $data;
    }
}

class OrderList extends OrderResponse
{

    public $STEP_SELECT_ORDER = "select_order";
    public $STEP_ORDER_HANDLE = "order_handle";
    public $KEY_ORDER_SN = "ORDER_SN";
    public static $HANDLE_CANCEL = 1;
    public static $HANDLE_CONFORM = 2;
    public static $HANDLE_URGE = 3;

    function __construct($openid)
    {
        Lang::load(lang_file('common'));
        parent::__construct($openid);
    }

    function text_handle($content)
    {
        $order_list_str = $goods = $br = '';
        $status = $this->get_status();
        if ($status[OrderResponse::$KEY_SUB_STATUS]) {
            switch ($status[OrderResponse::$KEY_SUB_STATUS]) {
                case $this->STEP_SELECT_ORDER:
                    $order_info = $this->get_order_info(array('order_sn' => $content));
                    $new_status[OrderResponse::$KEY_SUB_STATUS] = $this->STEP_ORDER_HANDLE;
                    $new_status[$this->KEY_ORDER_SN] = $order_info['order_sn'];
                    $new_status[OrderResponse::$KEY_STATUS] = $status[OrderResponse::$KEY_STATUS];
                    $this->update_status($new_status);
                    foreach ($order_info['goods_list'] as $d) {
                        $goods .= $br . $d['goods_name'] . " X " . $d['quantity'];
                        $br = "\n    ";
                    }
                    $response = "订单号:" . $order_info['order_sn'] . "\n订单状态:" . order_status($order_info['status']) .
                        "\n数量:" . $goods . "\n总价:" . $order_info['order_amount'];
                    $response .= "\n回复以下数字对应操作:" . self::$HANDLE_CANCEL . ":取消订单," . self::$HANDLE_CONFORM . ":确认订单," .
                        self::$HANDLE_URGE . ":催单";
                    break;
                case $this->STEP_ORDER_HANDLE:
                    $order_info = $this->get_order_info(array('order_sn' => $status[$this->KEY_ORDER_SN]));
                    switch ($content) {
                        case self::$HANDLE_CANCEL:
                            $response = $this->update_order_status($order_info, ORDER_CANCELED);
                            break;
                        case self::$HANDLE_CONFORM:
                            $response = $this->update_order_status($order_info, ORDER_FINISHED);
                            break;
                        case self::$HANDLE_URGE:
                            //TODO to urge seller
                            $response = "已经通知卖家快点配送";
                            break;
                        default:
                            $response = "错误的回复,退出操作输入#号";
                            break;
                    }
                    $this->delete_status();
                    break;
            }
        } else {
            //首次
            $order_list = $this->get_order_list();
            $status[OrderResponse::$KEY_STATUS] = OrderResponse::$status_order_list;
            if ($order_list) {
                foreach ($order_list as $ord) {
                    $order_list_str .= "订单号:" . $ord['order_sn'] . "总价:" . $ord['order_amount'] . "状态" . order_status($ord['status']) . "\n";
                }
                $status[OrderResponse::$KEY_SUB_STATUS] = $this->STEP_SELECT_ORDER;
                $response = "回复订单号查询详情:\n" . $order_list_str;
            } else {
                $response = "暂无正在进行的订单";
            }
            $this->update_status($status);
        }
        return $response;
    }

    function click_handle()
    {
        return $this->text_handle(null);
    }
}

class OrderFood extends OrderResponse
{
    public $STEP_SELECT_FOOD = "select_food";
    public $STEP_SELECT_SPECIFICATION = "select_specification";
    public static $KEY_ORDER_FOOD_STATUS = "ORDER_FOOD_STATUS";
    public $TAG_GOODS_ID = "goods_id";
    public $TAG_SPECIFICATION = "specification";
    public $TAG_GOODS_INFO = "goods_info";

    function __construct($openid)
    {
        parent::__construct($openid);
    }

    function text_handle($content)
    {
        $text = $region_id = $goods_specification = null;
        $good_ids = array();
        $i = 1;
        $address = $this->get_address();
        $bind_url = site_url()."/index.php?app=wechat&act=set_address&openid=$this->openid";
        if (!$address) {
            $response = "账户信息还未完善，请点此完善：\n <a href='$bind_url'>完善</a>";
            return $response;
        }
        $status = $this->get_status();
        if ($status[OrderResponse::$KEY_SUB_STATUS]) {
            switch ($status[OrderResponse::$KEY_SUB_STATUS]) {
                case $this->STEP_SELECT_FOOD:
                    $goods_id = $status[$this->TAG_GOODS_ID][$content];
                    if ($goods_id) {
                        //选择商品规格
                        $goods_info = $this->get_goods_info($goods_id);
                        foreach ($goods_info['goods']['_specs'] as $specs) {
                            $specs_name = $goods_info['goods']['spec_name_1'] ? $goods_info['goods']['spec_name_1'] . ":" . $specs['spec_1'] : "";
                            $specs_name .= $goods_info['goods']['spec_name_2'] ? "," . $goods_info['goods']['spec_name_2'] . ":" . $specs['spec_2'] : "";
                            $goods_specification .= $i . ":" . $goods_info['goods']['goods_name'] . $specs_name . "\n";
                            $specification[$i] = $specs;
                            $i++;
                        }
                        $response = "请选择如下规格:" . $goods_specification;
                        $status[OrderResponse::$KEY_SUB_STATUS] = $this->STEP_SELECT_SPECIFICATION;
                        $status[$this->TAG_GOODS_INFO] = $goods_info['goods'];
                        $status[$this->TAG_SPECIFICATION] = $specification;
                        $this->update_status($status);
                    } else {
                        $response = "选择的商品不存在,请重新选择。返回请按#号键";
                    }
                    break;
                case $this->STEP_SELECT_SPECIFICATION:
                    if ($status[$this->TAG_SPECIFICATION][$content]) {
                        //生成订单
                        $order_type =& ot("normal");
                        $goods_info = $this->_make_order_info($content);
                        $address['address_options'] = $address['addr_id'];
                        /* 将这些信息传递给订单类型处理类生成订单(你根据我提供的信息生成一张订单) */
                        $_SESSION['user_info'] = $this->wechat_info;
                        $visitor =& env('visitor', new UserVisitor());
                        $order_id = $order_type->submit_order(array(
                            'goods_info' => $goods_info, //商品信息（包括列表，总价，总量，所属店铺，类型）,可靠的!
                            'post' => $address, //用户填写的订单信息
                        ));
                        if ($order_id) {
                            $model_order =& m('order');
                            $order_info = $model_order->get($order_id);
                            $response = "订餐成功，订单号为:" . $order_info['order_sn'];
                            $this->delete_status();
                        } else {
                            $response = "订餐失败";
                        }
                    } else {
                        $response = "错误的选择，请重新选择:";
                    }
                    break;
            }
        } else {
            //首次
            $status[OrderResponse::$KEY_STATUS] = OrderResponse::$status_order_food;
            $region_id = $address['region_id'];
            $goods_list = $this->search_food($region_id, $content);
            if ($goods_list) {
                foreach ($goods_list as $good) {
                    $good_ids[$i] = $good['goods_id'];
                    $text .= $i . ":" . $good["goods_name"] . "\n";
                    $i++;
                }
                $status[OrderResponse::$KEY_SUB_STATUS] = $this->STEP_SELECT_FOOD;
                $status[$this->TAG_GOODS_ID] = $good_ids;
                $response = "选择以下序列号:\n" . $text;
            } else {
                $response = "未查找到相关商品";
            }
            $this->update_status($status);
        }
        return $response;
    }

    function click_handle()
    {
        $status[OrderResponse::$KEY_STATUS] = OrderResponse::$status_order_food;
        $this->update_status($status);
        $response = "请输入餐饮名字：";
        return $response;
    }

    function _make_order_info($spec_id)
    {
        $return = array(
            'items' => array(), //商品列表
            'quantity' => 1, //商品总量
            'amount' => 0, //商品总价
            'store_id' => 0, //所属店铺
            'store_name' => '', //店铺名称
            'type' => 'material', //商品类型
            'otype' => 'normal', //订单类型
            'allow_coupon' => true, //是否允许使用优惠券
        );
        $status = $this->get_status();
        $goods_info = $status[$this->TAG_GOODS_INFO];
        $return['amount'] = $status['specification'][$spec_id]['price'];
        $return['store_id'] = $goods_info['store_id'];
        $store_model =& m('store');
        $store_info = $store_model->get($goods_info['store_id']);
        $return['store_name'] = $store_info['store_name'];
        $cart_items = array(
            'user_id' => $this->wechat_info['user_id'],
            'store_id' => $goods_info['store_id'],
            'spec_id' => $status['combination'][$spec_id]['spec_id'],
            'goods_id' => $goods_info['goods_id'],
            'goods_name' => addslashes($goods_info['goods_name']),
//            'specification' => addslashes(trim($specification)),
            'price' => $status['combination'][$spec_id]['price'],
            'quantity' => 1,
            'goods_image' => $goods_info['default_image'] ? $goods_info['default_image'] : Conf::get('default_goods_image'),
        );
        $return['items'][] = $cart_items;
        return $return;
    }
}

class OrderView extends OrderResponse
{
    public $ORDER_VIEW_STATUS = "order_view";
    public $TAG_ORDER_INFO = "order_info";

    function __construct($openid)
    {
        parent::__construct($openid);
        Lang::load(lang_file('common'));
    }

    function text_handle($content)
    {
        $goods = $br = '';
        $status = $this->get_status();
        if ($status[OrderResponse::$KEY_SUB_STATUS] == $this->ORDER_VIEW_STATUS) {
            $order_info = $status[$this->TAG_ORDER_INFO];
            switch ($content) {
                case OrderList::$HANDLE_CANCEL:
                    $response = $this->update_order_status($order_info, ORDER_CANCELED);
                    break;
                case OrderList::$HANDLE_CONFORM:
                    $response = $this->update_order_status($order_info, ORDER_FINISHED);
                    break;
                case OrderList::$HANDLE_URGE:
                    //TODO to urge seller
                    $response = "已经通知卖家快点配送";
                    $this->delete_status();
                    break;
                default:
                    $response = "错误的回复,退出操作输入#号";
                    break;
            }
        } else {
            $order_info = $this->get_order_info(array('order_sn' => $content));
            if (!$order_info) {
                return "订单不存在";
            }
            foreach ($order_info['goods_list'] as $d) {
                $goods .= $br . $d['goods_name'] . " X " . $d['quantity'];
                $br = "\n    ";
            }
            $status[$this->TAG_ORDER_INFO] = $order_info;
            $status[OrderResponse::$KEY_STATUS] = OrderResponse::$status_order_view;
            $status[OrderResponse::$KEY_SUB_STATUS] = $this->ORDER_VIEW_STATUS;
            $this->update_status($status);
            $response = "订单号:" . $order_info['order_sn'] . "\n订单状态:" . order_status($order_info['status']) .
                "\n数量:" . $goods . "\n总价:" . $order_info['order_amount'];
            $response .= "\n回复以下数字对应操作:" . OrderList::$HANDLE_CANCEL . ":取消订单," . OrderList::$HANDLE_CONFORM . ":确认订单," .
                OrderList::$HANDLE_URGE . ":催单";
        }
        return $response;
    }

    function click_handle()
    {
        $status[OrderResponse::$KEY_STATUS] = OrderResponse::$status_order_view;
        $this->update_status($status);
        $response = "请输入订单号:";
        return $response;
    }
}

class OrderCancel extends OrderResponse
{
    function __construct($openid)
    {
        parent::__construct($openid);
    }

    function text_handle($content)
    {
        $order_sn = $content;
        $order_info = $this->get_order_info(array('order_sn' => $order_sn));
        $response = $this->update_order_status($order_info, ORDER_CANCELED);
        return $response;
    }

    function click_handle()
    {
        $status[OrderResponse::$KEY_STATUS] = OrderResponse::$status_order_cancel;
        $this->update_status($status);
        $response = "请输入要取消的订单号：";
        return $response;
    }
}

class OrderUrge extends OrderResponse
{
    function __construct($openid)
    {
        parent::__construct($openid);
    }

    function text_handle($content)
    {
//        $order_sn = $content;
        $this->delete_status();
        $response = "已经通知卖家快点配送";
        return $response;
    }

    function click_handle()
    {
        $status[OrderResponse::$KEY_STATUS] = OrderResponse::$status_order_urge;
        $this->update_status($status);
        $response = "请输入订单号：";
        return $response;
    }
}