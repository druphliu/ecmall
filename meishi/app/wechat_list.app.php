<?php

/* 定义like语句转换为in语句的条件 */
define('MAX_ID_NUM_OF_IN', 10000); // IN语句的最大ID数
define('MAX_HIT_RATE', 0.05);      // 最大命中率（满足条件的记录数除以总记录数）
define('MAX_STAT_PRICE', 10000);   // 最大统计价格
define('PRICE_INTERVAL_NUM', 5);   // 价格区间个数
define('MIN_STAT_STEP', 50);       // 价格区间最小间隔
define('NUM_PER_PAGE', 16);        // 每页显示数量
define('ENABLE_SEARCH_CACHE', true); // 启用商品搜索缓存
define('SEARCH_CACHE_TTL', 3600);  // 商品搜索缓存时间

class Wechat_listApp extends MallbaseApp
{
    /* 搜索商品 */
    function index()
    {
        if (empty($_GET['openid']))
        {
            exit("参数不正确");
        }
        $openid = $_GET['openid'];
        //根据openid获取绑定信息
        $member_wechat =& m('memberwechat');
        $wechat_info = $member_wechat->get_info($openid);
        print_r($wechat_info);
        $this->display('wechat/list.html');
    }

}

?>
