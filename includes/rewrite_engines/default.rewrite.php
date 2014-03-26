<?php

/**
 *    默认Rewrite引擎
 *
 *    @author    Garbin
 *    @usage    none
 */

/*

##### Rewrite Rule #####

RewriteEngine On

#商品详情
RewriteRule ^goods/([0-9]+)/?$ index.php?app=goods&id=$1 [L]
RewriteRule ^goods/([0-9]+)/([^/]+)/?$ index.php?app=goods&id=$1&act=$2 [L]
RewriteRule ^goods/([0-9]+)/([^/]+)/page_([^/]+)/?$ index.php?app=goods&id=$1&act=$2&page=$3 [L]
RewriteRule ^groupbuy/([0-9]+)/?$ index.php?app=groupbuy&id=$1 [L]

#分类
RewriteRule ^category/goods/?$ index.php?app=category [L]
RewriteRule ^category/(.*)/?$ index.php?app=category&act=$1 [L]

#品牌
RewriteRule ^brand/?$ index.php?app=brand [L]

#文章
RewriteRule ^article/([0-9]+).html$ index.php?app=article&act=view&article_id=$1 [L]

#店铺页面
RewriteRule ^store/([0-9]+)/?$ index.php?app=store&id=$1 [L]
RewriteRule ^store/article/([0-9]+).html$ index.php?app=store&act=article&id=$1 [L]
RewriteRule ^store/([0-9]+)/credit/?$ index.php?app=store&id=$1&act=credit [L]
RewriteRule ^store/([0-9]+)/credit/page_([^/]+)/?$ index.php?app=store&id=$1&act=credit&page=$2 [L]
RewriteRule ^store/([0-9]+)/credit/([0-9]+)/?$ index.php?app=store&id=$1&act=credit&eval=$2 [L]
RewriteRule ^store/([0-9]+)/credit/([0-9]+)/page_([^/]+)/?$ index.php?app=store&id=$1&act=credit&eval=$2&page=$3 [L]
RewriteRule ^store/([0-9]+)/goods/?$ index.php?app=store&id=$1&act=search [L]
RewriteRule ^store/([0-9]+)/goods/page_([^/]+)/?$ index.php?app=store&id=$1&act=search&page=$2 [L]
RewriteRule ^store/([0-9]+)/category/([0-9]+)/?$ index.php?app=store&id=$1&act=search&cate_id=$2 [L]
RewriteRule ^store/([0-9]+)/category/([0-9]+)/page_([^/]+)/?$ index.php?app=store&id=$1&act=search&cate_id=$2&page=$3 [L]
RewriteRule ^store/([0-9]+)/groupbuy/?$ index.php?app=store&id=$1&act=groupbuy [L]
RewriteRule ^store/([0-9]+)/groupbuy/page_([^/]+)/?$ index.php?app=store&id=$1&act=groupbuy&page=$2 [L]

*/

class DefaultRewrite extends BaseRewrite
{
    /*静态化的键*/
    var $_rewrite_query = array('act','cate_id','id','page');

    /* Rewrite规则地图，记录参数对应的rule名称 */
    var $_rewrite_maps  = array(
        /* '{app名称}_{参数列表，按升序排序，"_"连接}' => '重写规则名称', */
        'app'=>'app_index',
        'app_cate_id'=>'app_id',
        'app_act'=>'app_cate',
        'app_act_id'=>'app_cate_id',
        'app_act_page'=>'app_cate_page',
        'app_act_cate_id_id_page'=>'app_cate_id_id_page'
//        /*卖家相关*/
//        'my_goods'=>'my_goods_index',
//        'my_goods_act'=>'my_goods_cate',
//        /*会员*/
//        'member'=>'member_index',
//        'member_act'=>'member_cate',
//        /*订单*/
//        'buyer_order'=>'buyer_order_index',
//
//        /*收藏夹*/
//        'my_favorite'=>'favorite_index',
//        'my_favorite_store'=>'favorite_store',
//
//        /*购物车*/
//        'cart'=>'cart_index',
//
//        /* 搜索 */
//        'search_cate_id'=>'goods_search',
//
//        /* 店铺首页 */
//        'store_id'  => 'store_index',
//
//        /* 商品详情 */
//        'goods_id'  => 'goods_detail',
//        'groupbuy_id'   => 'groupbuy_detail',
//
//        /* 商品分类 */
//        'category'  => 'goods_cate',
//
//        /* 品牌列表 */
//        'brand'     => 'brand_list',
//
//        /* 店铺分类 */
//        'category_act' => 'store_cate',
//
//        /* 文章详情 */
//        'article_act_id' => 'article_detail',
//        'article_act_article_id' => 'article_detail',
//
//        /* 店铺文章 */
//        'store_act_id'  => REWRITE_RULE_FN,
//        'store_act_id_page' => REWRITE_RULE_FN,
//        'store_act_eval_id' => 'store_credit_eval',
//        'store_act_eval_id_page'    => 'store_credit_eval_page',
//        'store_act_cate_id_id'  => 'store_goodscate',
//        'store_act_cate_id_id_page' => 'store_goodscate_page',
//        'goods_act_id'      => 'goods_extra_info',
//        'goods_act_id_page' => 'goods_extra_info_page',
    );

    /* Rewrite rules，记录各规则信息 */
    var $_rewrite_rules = array(
        'app_id'=>array(
            'rewrite'=>'%app%/%cate_id%.html'
        ),
        'app_index'=>array(
            'rewrite'=>'%app%.html'
        ),
        'app_cate'=>array(
            'rewrite'=>'%app%/%act%.html'
        ),
        'app_cate_id'=>array(
            'rewrite'=>'%app%/%act%/%id%.html'
        ),
        'app_cate_page'=>array(
            'rewrite'=>'%app%/%act%/page_%page%.html'
        ),
//        'my_goods_index'=>array(
//            'rewrite'=>'my_goods.html'
//        ),
//        'my_goods_cate'=>array(
//            'rewrite'=>'my_goods/%act%.html'
//        ),
//        'member_index'=>array(
//            'rewrite'=>'member.html'
//        ),
//        'member_cate'=>array(
//            'rewrite'=>'member/%act%.html'
//        ),
//        'buyer_order_index'=>array(
//            'rewrite'=>'buyer_order.html'
//        ),
//        'favorite_index'=>array(
//            'rewrite'=>'my_favorite.html'
//        ),
//        'favorite_store'=>array(
//            'rewrite'=>'my_favorite/store.html'
//        ),
//        'cart_index'=>array(
//            'rewrite'=>'cart.html'
//        ),
//        'goods_search'=>array(
//            'rewrite'=>'search/%cate_id%.html'
//        ),
//        'store_index'   => array(
//            'rewrite'   => 'store/%id%.html',
//        ),
//        'goods_detail'  => array(
//            'rewrite'   => 'goods/%id%.html',
//        ),
//        'goods_cate'    => array(
//            'rewrite'   => 'category.html',
//        ),
//        'brand_list'    => array(
//            'rewrite'   => 'brand.html',
//        ),
//        'store_cate'    => array(
//            'rewrite'   => 'category/%act%.html',
//        ),
//        'article_detail'    => array(
//            'rewrite'   => 'article/%article_id%.html',
//        ),
//        'store_article' => array(
//            'rewrite'   => 'store/article/%id%.html',
//        ),
//        'store_credit'  => array(
//            'rewrite'   => 'store/%id%/credit.html',
//        ),
//        'store_credit_page'  => array(
//            'rewrite'   => 'store/%id%/credit/page_%page%.html',
//        ),
//        'store_credit_eval'  => array(
//            'rewrite'   => 'store/%id%/credit/%eval%.html',
//        ),
//        'store_credit_eval_page'    => array(
//            'rewrite'   => 'store/%id%/credit/%eval%/page_%page%.html',
//        ),
//        'store_goodslist'   => array(
//            'rewrite'   => 'store/%id%/goods.html',
//        ),
//        'store_goodslist_page'   => array(
//            'rewrite'   => 'store/%id%/goods/page_%page%.html',
//        ),
//        'store_goodscate'   => array(
//            'rewrite'   => 'store/%id%/category/%cate_id%.html',
//        ),
//        'store_goodscate_page'   => array(
//            'rewrite'   => 'store/%id%/category/%cate_id%/page_%page%.html',
//        ),
//        'goods_extra_info' => array(
//            'rewrite'   => 'goods/%id%/%act%.html',
//        ),
//        'goods_extra_info_page' => array(
//            'rewrite'   => 'goods/%id%/%act%/page_%page%.html',
//        ),
//        'groupbuy_detail'   =>  array(
//            'rewrite'   => 'groupbuy/%id%.html',
//        ),
//        'store_groupbuy'   =>  array(
//            'rewrite'   => 'store/%id%/groupbuy.html',
//        ),
//        'store_groupbuy_page'   =>  array(
//            'rewrite'   => 'store/%id%/groupbuy/page_%page%.html',
//        ),
    );


    function rule_store_act_id($params)
    {
        $rule_name = '';
        switch ($params['act'])
        {
            case 'article':
                $rule_name = 'store_article';
            break;
            case 'credit':
                $rule_name = 'store_credit';
            break;
            case 'search':
                $rule_name = 'store_goodslist';
            break;
            case 'groupbuy':
                $rule_name = 'store_groupbuy';
            break;
        }

        return $rule_name;
    }

    function rule_store_act_id_page($params)
    {
        $rule_name = '';
        switch ($params['act'])
        {
            case 'credit':
                $rule_name = 'store_credit_page';
            break;
            case 'search':
                $rule_name = 'store_goodslist_page';
            break;
            case 'groupbuy':
                $rule_name = 'store_groupbuy_page';
            break;
        }

        return $rule_name;
    }
}

?>
