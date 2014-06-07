<?php

/* 会员 member */
class MemberWechatModel extends BaseModel
{
    var $table  = 'member_wechat';
    var $prikey = 'user_id';
    var $_name  = 'member';

    var $_autov = array(
        'openid' => array(
            'required'  => true,
            'filter'    => 'trim',
        )
    );

    function get_info($openid)
    {
        $member_mod =& m('member');
        $fields = "m.user_name,m.email,m.real_name,w.openid,w.user_id";
        $tables = "{$this->table} w left join {$member_mod->table} m on w.user_id = m.user_id";
        $conditions = " where w.openid = '$openid'";
        $sql = "SELECT {$fields} FROM {$tables}{$conditions}";
        $info =$this->db->getRow($sql);
        return $info;
    }

}

?>