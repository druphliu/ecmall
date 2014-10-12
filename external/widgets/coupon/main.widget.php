<?php

/**
 * 优惠券抢购
 *
 * @return  array
 */
class CouponWidget extends BaseWidget
{
    var $_name = 'coupon';
    var $_ttl  = 86400;
    var $_num  = 10;

    function _get_data()
    {
        $area = ecm_getcookie('area');
        $cache_server =& cache_server();
        $key = $this->_get_cache_id();
        $data = $cache_server->get($key);
        $time = time();
        if($data === false)
        {
            $coupon_mod =& m('coupon');
            $data = $coupon_mod->find(array(
                'conditions' => "FIND_IN_SET($area,use_area) AND end_time>$time AND if_issue=1",
                'order' => 'start_time',
                'limit' => $this->_num,
            ));
            $cache_server->set($key, $data, $this->_ttl);
        }

        return $data;
    }
}

?>