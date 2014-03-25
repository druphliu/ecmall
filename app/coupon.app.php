<?php

class CouponApp extends StoreadminbaseApp
{
    var $_coupon_mod;
    var $_store_id;
    var $_store_mod;
    var $_couponsn_mod;
    function __construct()
    {
        $this->CouponApp();
    }
    function CouponApp()
    {
        parent::__construct();
        $this->_store_id  = intval($this->visitor->get('manage_store'));
        $this->_store_mod =& m('store');
        $this->_coupon_mod =& m('coupon');
        $this->_couponsn_mod =& m('couponsn');
    }
    function index()
    {
        $page = $this->_get_page(10);
        $coupon = $this->_coupon_mod->find(array(
            'conditions' => 'store_id = '.$this->_store_id,
            'limit' => $page['limit'],
            'count' => true,
        ));
        $this->_curlocal(LANG::get('member_center'), 'index.php?app=member',
                         LANG::get('coupon'), 'index.php?app=coupon',
                         LANG::get('coupons_list'));
        $page['item_count'] = $this->_coupon_mod->getCount();
        $this->_format_page($page);
        $this->assign('page_info', $page);

        $this->_curitem('coupon');
        $this->_curmenu('coupons_list');
        $this->_config_seo('title', Lang::get('member_center') . ' - ' . Lang::get('coupon'));
        $this->assign('coupons', $coupon);
        $this->import_resource(array(
            'script' => array(
                array(
                    'path' => 'dialog/dialog.js',
                    'attr' => 'id="dialog_js"',
                ),
                array(
                    'path' => 'jquery.ui/jquery.ui.js',
                    'attr' => '',
                ),
                array(
                    'path' => 'jquery.ui/i18n/' . i18n_code() . '.js',
                    'attr' => '',
                ),
                array(
                    'path' => 'jquery.plugins/jquery.validate.js',
                    'attr' => '',
                ),
                array(
                    'path' => 'chosen_v1.1.0/chosen.jquery.js',
                    'attr' => 'id="chosen_js"',
                ),
            ),
            'style' =>  'jquery.ui/themes/ui-lightness/jquery.ui.css,chosen_v1.1.0/chosen.min.css',
        ));
        $this->assign('time', gmtime());
        $this->display('coupon.index.html');
    }

    function add()
    {
        if (!IS_POST)
        {
            $store_id = $this->_store_id;
            $store_info = $this->_store_mod->get($store_id);
            $area_array = explode(',',$store_info['seller_area']);
            $region_mod =& m('region');
            $regions = $region_mod->get_list();
            foreach($area_array as $a){
                $region = $regions[$a];
                $option[]="<option value='$a'>".$regions[$region['parent_id']]['region_name'].".".$region['region_name']."</option>";
            }
            header("Content-Type:text/html;charset=" . CHARSET);
            $this->assign('option', $option);
            $this->assign('today', gmtime());
            $this->display('coupon.form.html');
        }
        else
        {

            $coupon_value = floatval(trim($_POST['coupon_value']));
            $min_amount = floatval(trim($_POST['min_amount']));
            if (empty($coupon_value) || $coupon_value < 0 )
            {
                $this->pop_warning('coupon_value_not');
                exit;
            }
            if ($min_amount < 0)
            {
                $this->pop_warning("min_amount_gt_zero");
                exit;
            }
            $start_time = gmstr2time(trim($_POST['start_time']));
            $end_time = gmstr2time_end(trim($_POST['end_time'])) - 1 ;
            if ($end_time < $start_time)
            {
                $this->pop_warning('end_gt_start');
                exit;
            }
            $use_area = $_POST['use_area'];
            if(!$this->_exit_area($use_area)){
                return;
            }
            $coupon = array(
                'coupon_name' => trim($_POST['coupon_name']),
                'coupon_value' => $coupon_value,
                'store_id' => $this->_store_id,
                'start_time' => $start_time,
                'end_time' => $end_time,
                'min_amount' => $min_amount,
                'if_issue'  => trim($_POST['if_issue']) == 1 ? 1 : 0,
                'use_area'=>implode(',',$use_area),
                'use_area_name'=>$this->_get_area_name($use_area)
            );
            $this->_coupon_mod->add($coupon);
            if ($this->_coupon_mod->has_error())
            {
                $this->pop_warning($this->_coupon_mod->get_error());
                exit;
            }
            $this->pop_warning('ok', 'coupon_add');
        }
    }

    function edit()
    {
        $coupon_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if (empty($coupon_id))
        {
            echo Lang::get("no_coupon");
        }
        if (!IS_POST)
        {
            header("Content-Type:text/html;charset=" . CHARSET);
            $coupon = $this->_coupon_mod->get_info($coupon_id);
            $store_id = $this->_store_id;
            $store_info = $this->_store_mod->get($store_id);
            $area_array = explode(',',$store_info['seller_area']);
            $region_mod =& m('region');
            $regions = $region_mod->get_list();
            foreach($area_array as $a){
                $selected = '';
                foreach(explode(',', $coupon['use_area']) as $u){
                    if($a==$u){
                        $selected = 'selected="selected"';
                    }
                }
                $region = $regions[$a];
                $option[]="<option value='$a' $selected>".$regions[$region['parent_id']]['region_name'].".".$region['region_name']."</option>";
            }
            $this->assign('option', $option);
            $this->assign('coupon', $coupon);
            $this->display('coupon.form.html');
        }
        else
        {
            $coupon_value = floatval(trim($_POST['coupon_value']));
            $min_amount = floatval(trim($_POST['min_amount']));
            if (empty($coupon_value) || $coupon_value < 0 )
            {
                $this->pop_warning('coupon_value_not');
                exit;
            }
            if ($min_amount < 0)
            {
                $this->pop_warning("min_amount_gt_zero");
                exit;
            }
            $start_time = gmstr2time(trim($_POST['start_time']));
            $end_time = gmstr2time_end(trim($_POST['end_time']))-1;
            //echo gmstr2time_end(trim($_POST['end_time'])) . '-------' .$end_time;exit;
            if ($end_time < $start_time)
            {
                $this->pop_warning('end_gt_start');
                exit;
            }
            $use_area = $_POST['use_area'];
            if(!$this->_exit_area($use_area)){
                exit;
            }
            $coupon = array(
                'coupon_name' => trim($_POST['coupon_name']),
                'coupon_value' => $coupon_value,
                'store_id' => $this->_store_id,
                'start_time' => $start_time,
                'end_time' => $end_time,
                'min_amount' => $min_amount,
                'if_issue'  => trim($_POST['if_issue']) == 1 ? 1 : 0,
                'use_area_name'=>$this->_get_area_name($use_area),
                'use_area'=>implode(',',$use_area)
            );
            $this->_coupon_mod->edit($coupon_id, $coupon);
            if ($this->_coupon_mod->has_error())
            {
                $this->pop_warning($this->_coupon_mod->get_error());
                exit;
            }
            $this->pop_warning('ok','coupon_edit');
        }
    }

    function issue()
    {
        $coupon_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if (empty($coupon_id))
        {
            $this->show_warning("no_coupon");
            exit;
        }
        $this->_coupon_mod->edit($coupon_id, array('if_issue' => 1));
        if ($this->_coupon_mod->has_error())
        {
            $this->show_message($this->_coupon_mod->get_error());
            exit;
        }
        $this->show_message('issue_success',
            'back_list', 'index.php?app=coupon');
    }

    function drop()
    {
        $coupon_id = isset($_GET['id']) ? trim($_GET['id']) : '';
        if (empty($coupon_id))
        {
            $this->show_warning('no_coupon');
            exit;
        }
        $time = gmtime();
        $coupon_ids = explode(',', $coupon_id);//vdump($this->_coupon_mod->find("((if_issue = 1 AND end_time > {$time})) AND coupon_id ".db_create_in($coupon_ids)));
        $this->_coupon_mod->drop("(if_issue = 0 OR (if_issue = 1 AND end_time < {$time})) AND coupon_id ".db_create_in($coupon_ids));
        if ($this->_coupon_mod->has_error())
        {
            $this->show_warning($this->_coupon_mod->get_error());
        }
        $this->show_message('drop_ok',
            'back_list', 'index.php?app=coupon');
    }

    function make()
    {
        $coupon_id = isset($_GET['id']) ? trim($_GET['id']) : '';
        if (empty($coupon_id))
        {
            echo Lang::get('no_coupon');
            exit;
        }
        if (!IS_POST)
        {
            header("Content-Type:text/html;charset=" . CHARSET);
            $this->assign('id', $coupon_id);
            $this->display('coupon_make.html');
        }
        else
        {
            $amount = intval(trim($_POST['amount']));
            if (empty($amount))
            {
                $this->pop_warning('involid_data');
                exit;
            }
            $this->generate($amount, $coupon_id);
            $this->pop_warning("ok","coupon_make");
        }
    }

    function export()
    {
        $coupon_id = isset($_GET['id']) ? trim($_GET['id']) : '';
        if (empty($coupon_id))
        {
            echo Lang::get('no_coupon');
            exit;
        }
        $info = $this->_coupon_mod->get_info($coupon_id);
        $coupon_name = ecm_iconv(CHARSET, 'gbk', $info['coupon_name']);
        header('Content-type: application/txt');
        header('Content-Disposition: attachment; filename="coupon_' .date('Ymd'). '_' .$coupon_name.'.txt"');
        $sn_array = $this->_couponsn_mod->findAll(array('coupon_id'=>$coupon_id));
        $crlf = get_crlf();
        foreach ($sn_array as $val)
        {
            echo $val['coupon_sn'] . $crlf;
        }
    }

    function extend()
    {
        $coupon_id = isset($_GET['id']) ? trim($_GET['id']) : '';
        if (empty($coupon_id))
        {
            echo Lang::get('no_coupon');
            exit;
        }
        if (!IS_POST)
        {
            header("Content-Type:text/html;charset=" . CHARSET);
            $this->assign('id', $coupon_id);
            $this->assign('send_model', Lang::get('send_model'));
            $this->display("coupon_extend.html");
        }
        else
        {
            if (empty($_POST['user_name']))
            {
                $this->pop_warning("involid_data");
                exit;
            }
            $user_name = str_replace(array("\r","\r\n"), "\n", trim($_POST['user_name']));
            $user_name = explode("\n", $user_name);
            $user_mod =&m ('member');
            $users = $user_mod->find(db_create_in($user_name, 'user_name'));
            if (empty($users))
            {
                $this->pop_warning('involid_data');
                exit;
            }
            if (count($users) > 30)
            {
                $this->pop_warning("amount_gt");
                exit;
            }
            else
            {
                $users = $this->assign_user($coupon_id, $users);
                $store = $this->_store_mod->get_info($this->_store_id);
                $coupon = $this->_coupon_mod->get_info($coupon_id);
                $coupon['store_name'] = $store['store_name'];
                $coupon['store_id'] = $this->_store_id;
                $this->_message_to_user($users, $coupon);
                $this->_mail_to_user($users, $coupon);
                $this->pop_warning("ok","coupon_extend");
            }
        }
    }

    function sn_list()
    {
        $coupon_id = isset($_GET['id']) ? trim($_GET['id']) : '';
        if (empty($coupon_id)) {
            echo Lang::get('no_coupon');
            exit;
        }
        $page = $this->_get_page(10);
        $coupon_sn = $this->_couponsn_mod->find(array(
            'conditions' => 'coupon_id = '.$coupon_id,
            'limit' => $page['limit'],
            'count' => true,
            'join'=>'bind_user',
            'order'=>'user_id desc'
        ));

        $this->_curlocal(LANG::get('member_center'), 'index.php?app=member',
            LANG::get('coupon'), 'index.php?app=coupon',
            LANG::get('coupon_sn'));
        $page['item_count'] = $this->_couponsn_mod->getCount();
        $this->_format_page($page);
        $this->assign('page_info', $page);

        $this->import_resource(array(
            'script' => array(
                array(
                    'path' => 'dialog/dialog.js',
                    'attr' => 'id="dialog_js"',
                ),
                array(
                    'path' => 'jquery.ui/jquery.ui.js',
                    'attr' => '',
                ),
            ),
            'style' =>  'jquery.ui/themes/ui-lightness/jquery.ui.css',
        ));

        $this->_curitem('coupon_sn');
        $this->_curmenu('coupon_sn');
        $this->_config_seo('title', Lang::get('member_center') . ' - ' . Lang::get('coupon').'-'.Lang::get('coupon_sn'));
        $this->assign('coupon_sn', $coupon_sn);
        $this->assign('coupon_id',$coupon_id);
        $this->display("coupon.sn.html");
    }

    function drop_sn(){
        $coupon_sns = isset($_GET['coupon_sn']) ? trim($_GET['coupon_sn']) : '';
        if (empty($coupon_sns)) {
            echo Lang::get('no_coupon_sn');
            exit;
        }
        $coupon_sn_ids = explode(',', $coupon_sns);

        $rows = $this->_couponsn_mod->drop("is_activity = 0 AND coupon_sn ".db_create_in($coupon_sn_ids));
        if ($this->_coupon_mod->has_error())
        {
            $this->show_warning($this->_coupon_mod->get_error());
        }
        if($rows){
            $this->show_message('drop_ok');
        }else{
            $this->show_warning('drop_error');
        }


    }

    function _message_to_user($users, $coupon)
    {
        $ms =& ms();
        foreach ($users as $key => $val)
        {
            $content = get_msg('touser_send_coupon', array(
            'price' => $coupon['coupon_value'],
            'start_time' =>  local_date('Y-m-d',$coupon['start_time']),
            'end_time' => local_date("Y-m-d", $coupon['end_time']),
            'coupon_sn' => $val['coupon']['coupon_sn'],
            'min_amount' => $coupon['min_amount'],
            'url' => SITE_URL . '/' . url('app=store&id=' . $coupon['store_id']),
            'store_name' => $coupon['store_name'],
            ));
            $msg_id = $ms->pm->send(MSG_SYSTEM, $val['user_id'], '',$content);
        }
    }

    function _mail_to_user($users, $coupon)
    {
        foreach ($users as $val)
        {
            $mail = get_mail('touser_send_coupon', array('user' => $val, 'coupon' => $coupon));
            if (!$mail)
            {
                continue;
            }
            $this->_mailto($val['email'], addslashes($mail['subject']), addslashes($mail['message']));
        }
    }

    function assign_user($id, $users)
    {
        $_user_mod =& m('member');
        $count = count($users);
        $users = array_values($users);
        $arr = $this->generate($count, $id);
        $i = 0;
        foreach ($users as $key => $user)
        {
                $users[$key]['coupon'] = $arr[$i];
                $_user_mod->createRelation('bind_couponsn', $user['user_id'], array($arr[$i]['coupon_sn'] => array('coupon_sn' =>$arr[$i]['coupon_sn'])));
                $i = $i + 1;
        }
        return $users;
    }

    function generate($num, $id)
    {
        if ($num > 1000)
        {
            $num = 1000;
        }
        if ($num < 1)
        {
            $num = 1;
        }
        $add_data = array();
        $str = '';
        $pix = 0;
        if (file_exists(ROOT_PATH . '/data/generate.txt'))
        {
            $s = file_get_contents(ROOT_PATH . '/data/generate.txt');
            $pix = intval($s);
        }
        $max = $pix + $num;
        file_put_contents(ROOT_PATH . '/data/generate.txt', $max);
        $couponsn = '';
        $tmp = '';
        $cpm = '';
        $str = '';
        for ($i = $pix + 1; $i <= $max; $i++ )
        {
            $cpm = sprintf("%08d", $i);
            $tmp = mt_rand(1000, 9999);
            $couponsn = $cpm . $tmp;
            $str .= "('$couponsn', {$id}, 0),";
            $add_data[] = array(
                'coupon_sn' => $couponsn,
                'coupon_id' => $id
                );
        }
        $string = substr($str,0, strrpos($str, ','));
        $this->_couponsn_mod->db->query("INSERT INTO {$this->_couponsn_mod->table} (coupon_sn, coupon_id, is_activity) VALUES {$string}", 'SILENT');
        return $add_data;
    }

    function _sql_insert($data)
    {
        $str = '';
        foreach ($data as $val)
        {
            $str .= "('{$val['coupon_sn']}', {$val['coupon_id']}, {$val['remain_times']}),";
        }
        $string = substr($str,0, strrpos($str, ','));
        $res = $this->_couponsn_mod->db->query("INSERT INTO {$this->_couponsn_mod->table} (coupon_sn, coupon_id, remain_times) VALUES {$string}", 'SILENT');
        $error = $this->_couponsn_mod->db->errno();
        return array('res' => $res, 'errno' => $error);
    }

    function _create_random($num, $id, $times)
    {
        $arr = array();
        for ($i = 1; $i <= $num; $i++)
        {
            $arr[$i]['coupon_sn'] =  mt_rand(10000, 99999);
            $arr[$i]['coupon_id'] = $id;
            $arr[$i]['remain_times'] = $times;
        }
        return $arr;
    }

    function _get_member_submenu()
    {
        $menus = array(
            array(
                'name'  => 'coupons_list',
                'url'   => 'index.php?app=coupon',
            ),
        );
        return $menus;
    }

    function _exit_area($area){
        $store_info = $this->_store_mod->get($this->_store_id);
        $store_area = explode(',', $store_info['seller_area']);
        if(!is_array($area)){
            $area_arr = explode(',', $area);
        }else{
            $area_arr = $area;
        }
        foreach($area_arr as $a){
            if(!in_array($a, $store_area)){

                return false;
            }
        }
        return true;
    }

    function _get_area_name($area){
        $area_name = '';
        $region_md =& m('region');
        $regions = $region_md->get_list();
        if(!is_array($area)){
            $area_arr = explode(',', $area);
        }else{
            $area_arr = $area;
        }
        foreach($area_arr as $a){
            $area = $regions[$a];
            $area_name .=$regions[$area['parent_id']]['region_name'].".".$area['region_name']."<br>";
        }
        return $area_name;
    }
}

?>