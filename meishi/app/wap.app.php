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
class WapAPP extends MallbaseApp
{
    function index()
    {
        $region_id = intval($_GET['region_id']);
        if($region_id){
            $this->set_area($region_id);
            header('HTTP/1.1 301 Moved Permanently');
            header('Location: '.url('app=wap'));
        }
        $this->display('wap/index.html');
    }
    function goodsList(){
        // 查询参数
        $param = $this->_get_query_param();
        if (empty($param))
        {
            header('Location: '.url("app=wap"));
            exit;
        }
        if (isset($param['cate_id']) && $param['layer'] === false)
        {
            $this->show_warning('no_such_category');
            return;
        }


        /* 按分类、品牌、地区、价格区间统计商品数量 */
        $stats = $this->_get_group_by_info($param, ENABLE_SEARCH_CACHE);
        $categories = $this->_list_gcategory($param['cate_id'],$param['layer']);
        $cate_id = $param['cate_id'];

        $all_category = array_merge(array(array('cate_id'=>$param['layer']>1?$categories[$param['cate_id']]['parent_id']:$param['cate_id'],'cate_name'=>'全部')),$categories);
        $count = count($all_category);
        for($i=0;$i<ceil($count/9);$i++){
            $category_arr[] = array_slice($all_category,$i*9, 9);
        }
        $this->assign('category_arr', $category_arr);

        $brands = $this->_get_brands($param);
        $this->assign('brands', $brands);

        $this->assign('app', APP);
        /* 分页信息 */
        $page = $this->_get_page(NUM_PER_PAGE);
        $page['item_count'] = $stats['total_count'];
        $this->_format_page($page);
        $this->assign('page_info', $page);
        $this->assign('param', $param);
        /* 商品列表 */
        $goods_image_mod = m('goodsimage');
        $sgrade_mod =& m('sgrade');
        $sgrades    = $sgrade_mod->get_options();
        $conditions = $this->_get_goods_conditions($param);
        $goods_mod  =& m('goods');
        $goods_list = $goods_mod->get_list(array(
            'conditions' => $conditions,
            'order'      => isset($_GET['order']) && isset($orders[$_GET['order']]) ? $_GET['order'] : '',
            'limit'      => $page['limit'],
        ));
        foreach ($goods_list as $key => $goods)
        {
            $step = intval(Conf::get('upgrade_required'));
            $step < 1 && $step = 5;
            $store_mod =& m('store');
            $goods_list[$key]['images'] = $goods_image_mod->find(array(
                'conditions'  =>'goods_id='.$goods['goods_id']));
            $goods_list[$key]['credit_image'] = $this->_view->res_base . '/images/' . $store_mod->compute_credit($goods['credit_value'], $step);
            empty($goods['default_image']) && $goods_list[$key]['default_image'] = Conf::get('default_goods_image');
            $goods_list[$key]['grade_name'] = $sgrades[$goods['sgrade']];
        }
        $this->assign('goods_list', $goods_list);

        $this->assign('cate_id',$cate_id);

        $this->assign('title', '切换区域');
        $this->display('wap/goods.list.html');
    }
    function orderList(){
        $this->assign('title', '切换区域');
        $this->display('wap/order.list.html');
    }
    function ucenter(){
        $this->assign('title', '切换区域');
        $this->display('wap/ucenter.html');
    }
    function view(){
        /* 参数 id */
        $id = empty($_GET['id']) ? 0 : intval($_GET['id']);
        if (!$id)
        {
            $this->show_warning('Hacking Attempt');
            return;
        }

        /* 可缓存数据 */
        $data = $this->_get_common_info($id);
        if ($data === false)
        {
            return;
        }
        else
        {
            $this->_assign_common_info($data);
        }

        /* 更新浏览次数 */
        $this->_update_views($id);

        //是否开启验证码
        if (Conf::get('captcha_status.goodsqa'))
        {
            $this->assign('captcha', 1);
        }

        $this->assign('guest_comment_enable', Conf::get('guest_comment'));

        $this->display('wap/goodsDetail.html');
    }
    function area(){
        $region_mod =& m('region');
        import('tree.lib');
        $tree = new Tree();
        $tree->setTree($region_mod->get_list(), 'region_id', 'parent_id', 'region_name');
        $region_list = $tree->getArrayList();
        $this->assign('areas', $region_list);
        $this->assign('hot_area',array(13=>'四川大学.成华校区',14=>'四川大学.温江校区'));
        $this->assign('title', '切换区域');
        $this->display('wap/area.html');
    }
    /**
     * 取得查询参数（有值才返回）
     *
     * @return  array(
     *              'keyword'   => array('aa', 'bb'),
     *              'cate_id'   => 2,
     *              'layer'     => 2, // 分类层级
     *              'brand'     => 'ibm',
     *              'region_id' => 23,
     *              'price'     => array('min' => 10, 'max' => 100),
     *          )
     */
    function _get_query_param()
    {
        static $res = null;
        if ($res === null)
        {
            $res = array();

            // keyword
            $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
            if ($keyword != '')
            {
                //$keyword = preg_split("/[\s," . Lang::get('comma') . Lang::get('whitespace') . "]+/", $keyword);
                $tmp = str_replace(array(Lang::get('comma'),Lang::get('whitespace'),' '),',', $keyword);
                $keyword = explode(',',$tmp);
                sort($keyword);
                $res['keyword'] = $keyword;
            }

            // cate_id
            if (isset($_GET['cate_id']) && intval($_GET['cate_id']) > 0)
            {
                $res['cate_id'] = $cate_id = intval($_GET['cate_id']);
                $gcategory_mod  =& bm('gcategory');
                $res['layer']   = $gcategory_mod->get_layer($cate_id, true);//获取分类层级
            }

            // brand
            if (isset($_GET['brand']))
            {
                $brand = trim($_GET['brand']);
                $res['brand'] = $brand;
            }
            //order
            if(isset($_GET['order'])){
                $order = trim($_GET['order']);
                $res['order'] = $order;
            }
            // price
            if (isset($_GET['price']))
            {
                $arr = explode('-', $_GET['price']);
                $min = abs(floatval($arr[0]));
                $max = abs(floatval($arr[1]));
                if ($min * $max > 0 && $min > $max)
                {
                    list($min, $max) = array($max, $min);
                }

                $res['price'] = array(
                    'min' => $min,
                    'max' => $max
                );
            }
        }

        return $res;
    }
    /**
     * 根据查询条件取得分组统计信息
     *
     * @param   array   $param  查询参数（参加函数_get_query_param的返回值说明）
     * @param   bool    $cached 是否缓存
     * @return  array(
     *              'total_count' => 10,
     *              'by_category' => array(id => array('cate_id' => 1, 'cate_name' => 'haha', 'count' => 10))
     *              'by_brand'    => array(array('brand' => brand, 'count' => count))
     *              'by_region'   => array(array('region_id' => region_id, 'region_name' => region_name, 'count' => count))
     *              'by_price'    => array(array('min' => 10, 'max' => 50, 'count' => 10))
     *          )
     */
    function _get_group_by_info($param, $cached)
    {
        $data = false;

        if ($cached)
        {
            $cache_server =& cache_server();
            $key = 'group_by_info_' . var_export($param, true);
            $data = $cache_server->get($key);
        }

        if ($data === false||empty($data))
        {
            $data = array(
                'total_count' => 0,
                'by_category' => array(),
                'by_brand'    => array(),
                'by_region'   => array(),
                'by_price'    => array()
            );

            $goods_mod =& m('goods');
            $store_mod =& m('store');
            $table = " {$goods_mod->table} g LEFT JOIN {$store_mod->table} s ON g.store_id = s.store_id";
            $conditions = $this->_get_goods_conditions($param);
            $sql = "SELECT COUNT(*) FROM {$table} WHERE" . $conditions;
            $total_count = $goods_mod->getOne($sql);
            if ($total_count > 0)
            {
                $data['total_count'] = $total_count;
                /* 按分类统计 */
                $cate_id = isset($param['cate_id']) ? $param['cate_id'] : 0;
                $sql = "";
                if ($cate_id > 0)
                {
                    $layer = $param['layer'];
                    if ($layer < 4)
                    {
                        $sql = "SELECT g.cate_id_" . ($layer + 1) . " AS id, COUNT(*) AS count FROM {$table} WHERE" . $conditions . " AND g.cate_id_" . ($layer + 1) . " > 0 GROUP BY g.cate_id_" . ($layer + 1) . " ORDER BY count DESC";
                    }
                }
                else
                {
                    $sql = "SELECT g.cate_id_1 AS id, COUNT(*) AS count FROM {$table} WHERE" . $conditions . " AND g.cate_id_1 > 0 GROUP BY g.cate_id_1 ORDER BY count DESC";
                }

                if ($sql)
                {
                    $category_mod =& bm('gcategory');
                    $children = $category_mod->get_children($cate_id, true);
                    $res = $goods_mod->db->query($sql);
                    while ($row = $goods_mod->db->fetchRow($res))
                    {
                        $data['by_category'][$row['id']] = array(
                            'cate_id'   => $row['id'],
                            'cate_name' => $children[$row['id']]['cate_name'],
                            'count'     => $row['count']
                        );
                    }
                }

                /* 按品牌统计 */
                $sql = "SELECT g.brand, COUNT(*) AS count FROM {$table} WHERE" . $conditions . " AND g.brand > '' GROUP BY g.brand ORDER BY count DESC";
                $by_brands = $goods_mod->db->getAllWithIndex($sql, 'brand');

                /* 滤去未通过商城审核的品牌 */
                if ($by_brands)
                {
                    $m_brand = &m('brand');
                    $brand_conditions = db_create_in(addslashes_deep(array_keys($by_brands)), 'brand_name');
                    $brands_verified = $m_brand->getCol("SELECT brand_name FROM {$m_brand->table} WHERE " . $brand_conditions . ' AND if_show=1');
                    foreach ($by_brands as $k => $v)
                    {
                        if (!in_array($k, $brands_verified))
                        {
                            unset($by_brands[$k]);
                        }
                    }
                }
                $data['by_brand'] = $by_brands;


                /* 按地区统计 */
                $sql = "SELECT s.region_id, s.region_name, COUNT(*) AS count FROM {$table} WHERE" . $conditions . " AND s.region_id > 0 GROUP BY s.region_id ORDER BY count DESC";
                $data['by_region'] = $goods_mod->getAll($sql);

                /* 按价格统计 */
                if ($total_count > NUM_PER_PAGE)
                {
                    $sql = "SELECT MIN(g.price) AS min, MAX(g.price) AS max FROM {$table} WHERE" . $conditions;
                    $row = $goods_mod->getRow($sql);
                    $min = $row['min'];
                    $max = min($row['max'], MAX_STAT_PRICE);
                    $step = max(ceil(($max - $min) / PRICE_INTERVAL_NUM), MIN_STAT_STEP);
                    $sql = "SELECT FLOOR((g.price - '$min') / '$step') AS i, count(*) AS count FROM {$table} WHERE " . $conditions . " GROUP BY i ORDER BY i";
                    $res = $goods_mod->db->query($sql);
                    while ($row = $goods_mod->db->fetchRow($res))
                    {
                        $data['by_price'][] = array(
                            'count' => $row['count'],
                            'min'   => $min + $row['i'] * $step,
                            'max'   => $min + ($row['i'] + 1) * $step,
                        );
                    }
                }
            }

            if ($cached)
            {
                $cache_server->set($key, $data, SEARCH_CACHE_TTL);
            }
        }

        return $data;
    }
    /**
     * 取得查询条件语句
     *
     * @param   array   $param  查询参数（参加函数_get_query_param的返回值说明）
     * @return  string  where语句
     */
    function _get_goods_conditions($param)
    {
        /* 组成查询条件 */
        $conditions = " g.if_show = 1 AND g.closed = 0 AND s.state = 1"; // 上架且没有被禁售，店铺是开启状态,
        if (isset($param['keyword']))
        {
            $conditions .= $this->_get_conditions_by_keyword($param['keyword'], ENABLE_SEARCH_CACHE);
        }
        if (isset($param['cate_id']))
        {
            $conditions .= " AND g.cate_id_{$param['layer']} = '" . $param['cate_id'] . "'";
        }
        if (isset($param['brand']))
        {
            $conditions .= " AND g.brand = '" . $param['brand'] . "'";
        }
        $conditions .= " AND FIND_IN_SET('" .$this->area  . "',s.seller_area)";
        if (isset($param['price']))
        {
            $min = $param['price']['min'];
            $max = $param['price']['max'];
            $min > 0 && $conditions .= " AND g.price >= '$min'";
            $max > 0 && $conditions .= " AND g.price <= '$max'";
        }

        return $conditions;
    }
    function _list_gcategory($cate_id,$layer=0){
        $gcategory_mod =& m('gcategory');
        if($layer>1){
            $category = $gcategory_mod->get_parent($cate_id);
            $cate_id = $category['parent_id'];
        }
        $scategories = $gcategory_mod->get_list($cate_id);
        return $scategories;
    }
    private function _get_brands($param){
        $brand_model = m('brand');
        $brands = $brand_model->find(array('store_id'=>$this->agent_id));
        return $brands;
    }
    /**
     * 取得公共信息
     *
     * @param   int     $id
     * @return  false   失败
     *          array   成功
     */
    function _get_common_info($id)
    {
        $cache_server =& cache_server();
        $key = 'page_of_goods_' . $id;
        $data = $cache_server->get($key);
        $cached = true;
        if ($data === false)
        {
            $cached = false;
            $data = array('id' => $id);

            /* 商品信息 */
            $goods = $this->_goods_mod->get_info($id);
            if (!$goods || $goods['if_show'] == 0 || $goods['closed'] == 1 || $goods['state'] != 1)
            {
                $this->show_warning('goods_not_exist');
                return false;
            }
            $goods['tags'] = $goods['tags'] ? explode(',', trim($goods['tags'], ',')) : array();

            /* 品牌 */
            if($goods['brand']){
                $brand_mod =& m('brand');
                $brand_info = $brand_mod->get(array('brand_name'=>$goods['brand']));
                $goods['brand_info'] = $brand_info;
            }
            $data['goods'] = $goods;

            /* 店铺信息 */
            if (!$goods['store_id'])
            {
                $this->show_warning('store of goods is empty');
                return false;
            }
            $this->set_store($goods['store_id']);
            $data['store_data'] = $this->get_store_data();

            /* 当前位置 */
            $data['cur_local'] = $this->_get_curlocal($goods['cate_id']);
            $data['goods']['related_info'] = $this->_get_related_objects($data['goods']['tags']);
            /* 分享链接 */
            $data['share'] = $this->_get_share($goods);

            $cache_server->set($key, $data, 1800);
        }
        if ($cached)
        {
            $this->set_store($data['goods']['store_id']);
        }

        return $data;
    }
    /* 赋值公共信息 */
    function _assign_common_info($data)
    {
        /* 商品信息 */
        $goods = $data['goods'];
        $this->assign('goods', $goods);
        $this->assign('sales_info', sprintf(LANG::get('sales'), $goods['sales'] ? $goods['sales'] : 0));
        $this->assign('comments', sprintf(LANG::get('comments'), $goods['comments'] ? $goods['comments'] : 0));

        /* 店铺信息 */
        $this->assign('store', $data['store_data']);

        /* 浏览历史 */
        $this->assign('goods_history', $this->_get_goods_history($data['id']));

        /* 默认图片 */
        $this->assign('default_image', Conf::get('default_goods_image'));

        /* 当前位置 */
        $this->_curlocal($data['cur_local']);

        /* 配置seo信息 */
        $this->_config_seo($this->_get_seo_info($data['goods']));

        /* 商品分享 */
        $this->assign('share', $data['share']);

        $this->import_resource(array(
            'script' => 'jquery.jqzoom.js',
            'style' => 'res:jqzoom.css'
        ));
    }
    /* 更新浏览次数 */
    function _update_views($id)
    {
        $goodsstat_mod =& m('goodsstatistics');
        $goodsstat_mod->edit($id, "views = views + 1");
    }
}

?>
