<?php

/**
 * Created by PhpStorm.
 * User: druphliu
 * Date: 14-2-13
 * Time: 下午10:15
 */
class WechatApp extends WechatbaseApp
{
    function oauth()
    {
        if (IS_POST && $_POST['user_name'] && $_POST['password']) {
            $username = trim($_POST['user_name']);
            $password = $_POST['password'];
            $oauth = new OAuth();
            $result = $oauth->bind($username, $password);
            if ($result == 1) {
                $model_address =& m('address');
                $addresses = $model_address->find(array(
                    'conditions' => 'user_id = ' . $this->visitor->get('user_id'),
                ));
                $result = "绑定成功！";
                if (!$addresses) {
                    $result .= "你还未设置你的配送地址，设置地址后即可快速订餐！<a href='/index.php?app=oauth&act=set_address'>点此设置</a>";
                }

            } else {
                var_dump($oauth->openid);
                echo "未知错误";
            }
        } else {
            $this->display('member.wechat.bind.html');
        }
    }

    function set_address()
    {
        $model_address =& m('address');
        $openid = $_GET['openid'];
        if (!$openid) {
            show_message("参数不正确！");
            return;
        }
        $member_wechat =& m('memberwechat');
        $wechat_info = $member_wechat->get_info($openid);
        if (IS_POST) {
            if (!$wechat_info) {
                show_message("账户未绑定");
                return;
            }
            $data = array(
                'user_id' => $wechat_info['user_id'],
                'consignee' => $_POST['consignee'],
                'region_id' => $_POST['region_id'],
                'region_name' => $_POST['region_name'],
                'address' => $_POST['address'],
                'zipcode' => $_POST['zipcode'],
                'phone_tel' => $_POST['phone_tel'],
                'phone_mob' => $_POST['phone_mob'],
            );
            if (!($address_id = $model_address->add($data))) {
                show_message($model_address->get_error());
                return;
            }
            show_message("设置成功");
        } else {
            $my_address = $model_address->get(array('conditions' => 'user_id = ' . $wechat_info['user_id'])); ;
            if($my_address){
                return;
            }
            $this->assign('openid', $openid);
            $this->assign('act', 'set_address');
            $this->_get_regions();
            $this->import_resource(array('script' => 'mlselection.js,inline_edit.js,jquery.plugins/jquery.validate.js'));
            $this->display('member.wechat.set_address.html');
        }
    }

    function _get_regions()
    {
        $model_region =& m('region');
        $regions = $model_region->get_list(0);
        if ($regions) {
            $tmp = array();
            foreach ($regions as $key => $value) {
                $tmp[$key] = $value['region_name'];
            }
            $regions = $tmp;
        }
        $this->assign('regions', $regions);
    }
}