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

class SearchApp extends MallbaseApp
{
    /* 搜索商品 */
    function index()
    {
        // 查询参数
        $param = $this->_get_query_param();
        if (empty($param))
        {
            header('Location: '.url("app=index"));
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

        $this->import_resource(array(
            'script' => array(
                array(
                    'path' => 'blocksit.min.js',
                    'attr' => 'id="blocksit_js"',
                ),
                array(
                    'path' => 'jquery.lazyload.min.js',
                    'attr' => '',
                )
            )
        ));
        $this->assign('cate_id',$cate_id);

        /* 配置seo信息 */
        $this->_config_seo($this->_get_seo_info('goods', $cate_id));
        $this->display('goods.list.html');
    }

    function groupbuy()
    {
        empty($_GET['state']) &&  $_GET['state'] = 'on';
        $conditions = '1=1';

        // 排序
        $orders = array(
            'group_id desc'          => Lang::get('select_pls'),
            'views desc'     => Lang::get('views'),
        );

        if ($_GET['state'] == 'on')
        {
            $orders['end_time asc'] = Lang::get('lefttime');
            $conditions .= ' AND gb.state ='. GROUP_ON .' AND gb.end_time>' . gmtime();
        }
        elseif ($_GET['state'] == 'end')
        {
            $conditions .= ' AND (gb.state=' . GROUP_ON . ' OR gb.state=' . GROUP_END . ') AND gb.end_time<=' . gmtime();
        }
        else
        {
            $conditions .= $this->_get_query_conditions(array(
                array(      //按团购状态搜索
                    'field' => 'gb.state',
                    'name'  => 'state',
                    'handler' => 'groupbuy_state_translator',
                )
            ));
        }
        $conditions .= $this->_get_query_conditions(array(
            array( //活动名称
                'field' => 'group_name',
                'equal' => 'LIKE',
                'assoc' => 'AND',
                'name'  => 'keyword',
                'type'  => 'string',
            ),
        ));
        $page = $this->_get_page(NUM_PER_PAGE);   //获取分页信息
        $groupbuy_mod = &m('groupbuy');
        $groupbuy_list = $groupbuy_mod->find(array(
            'conditions'    => $conditions,
            'fields'        => 'gb.group_name,gb.spec_price,gb.min_quantity,gb.store_id,gb.state,gb.end_time,g.default_image,default_spec,s.store_name',
            'join'          => 'belong_store, belong_goods',
            'limit'         => $page['limit'],
            'count'         => true,   //允许统计
            'order'         => isset($_GET['order']) && isset($orders[$_GET['order']]) ? $_GET['order'] : 'group_id desc',
        ));
        if ($ids = array_keys($groupbuy_list))
        {
            $quantity = $groupbuy_mod->get_join_quantity($ids);
        }
        foreach ($groupbuy_list as $key => $groupbuy)
        {
            $groupbuy_list[$key]['quantity'] = empty($quantity[$key]['quantity']) ? 0 : $quantity[$key]['quantity'];
            $groupbuy['default_image'] || $groupbuy_list[$key]['default_image'] = Conf::get('default_goods_image');
            $groupbuy['spec_price'] = unserialize($groupbuy['spec_price']);
            $groupbuy_list[$key]['group_price'] = $groupbuy['spec_price'][$groupbuy['default_spec']]['price'];
            $groupbuy['state'] == GROUP_ON && $groupbuy_list[$key]['lefttime'] = lefttime($groupbuy['end_time']);
        }
        $this->assign('state', array(
             'on' => Lang::get('group_on'),
             'end' => Lang::get('group_end'),
             'finished' => Lang::get('group_finished'),
             'canceled' => Lang::get('group_canceled'))
        );
        $this->assign('orders', $orders);
        // 当前位置
        $this->_curlocal(array(array('text' => Lang::get('groupbuy'))));
        $this->_config_seo('title', Lang::get('groupbuy') . ' - ' . Conf::get('site_title'));
        $page['item_count'] = $groupbuy_mod->getCount();   //获取统计数据
        $this->_format_page($page);
        $this->assign('nav_groupbuy', 1); // 标识当前页面是团购列表，用于设置导航状态
        $this->assign('page_info', $page);
        $this->assign('groupbuy_list',$groupbuy_list);
        $this->assign('recommended_groupbuy', $this->_recommended_groupbuy(2));
        $this->assign('last_join_groupbuy', $this->_last_join_groupbuy(2));
        $this->display('search.groupbuy.html');
    }

    // 推荐团购活动
    function _recommended_groupbuy($_num)
    {
        $model_groupbuy =& m('groupbuy');
        $data = $model_groupbuy->find(array(
            'join'          => 'belong_goods',
            'conditions'    => 'gb.recommended=1 AND gb.state=' . GROUP_ON . ' AND gb.end_time>' . gmtime(),
            'fields'        => 'group_id, goods.default_image, group_name, end_time, spec_price',
            'order'         => 'group_id DESC',
            'limit'         => $_num,
        ));
        foreach ($data as $gb_id => $gb_info)
        {
            $price = current(unserialize($gb_info['spec_price']));
            empty($gb_info['default_image']) && $data[$gb_id]['default_image'] = Conf::get('default_goods_image');
            $data[$gb_id]['lefttime']   = lefttime($gb_info['end_time']);
            $data[$gb_id]['price']      = $price['price'];
        }
        return $data;
    }

    // 最新参加的团购
    function _last_join_groupbuy($_num)
    {
        $model_groupbuy =& m('groupbuy');
        $data = $model_groupbuy->find(array(
            'join' => 'be_join,belong_goods',
            'fields' => 'gb.group_id,gb.group_name,gb.group_id,groupbuy_log.add_time,gb.spec_price,goods.default_image',
            'conditions' => 'groupbuy_log.user_id > 0',
            'order' => 'groupbuy_log.add_time DESC',
            'limit' => $_num,
        ));
        foreach ($data as $gb_id => $gb_info)
        {
            $price = current(unserialize($gb_info['spec_price']));
            empty($gb_info['default_image']) && $data[$gb_id]['default_image'] = Conf::get('default_goods_image');
            $data[$gb_id]['price']      = $price['price'];
        }
        return $data;
    }

                /* 取得店铺分类 */
    function _list_scategory()
    {
        $scategory_mod =& m('scategory');
        $scategories = $scategory_mod->get_list(-1,true);

        import('tree.lib');
        $tree = new Tree();
        $tree->setTree($scategories, 'cate_id', 'parent_id', 'cate_name');
        return $tree->getArrayList(0);
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
     * 取得过滤条件
     */
    function _get_filter($param)
    {
        static $filters = null;
        if ($filters === null)
        {
            $filters = array();
            if (isset($param['keyword']))
            {
                $keyword = join(' ', $param['keyword']);
                $filters['keyword'] = array('key' => 'keyword', 'name' => LANG::get('keyword'), 'value' => $keyword);
            }
            isset($param['brand']) && $filters['brand'] = array('key' => 'brand', 'name' => LANG::get('brand'), 'value' => $param['brand']);
            if (isset($param['region_id']))
            {
                // todo 从地区缓存中取
                $region_mod =& m('region');
                $row = $region_mod->get(array(
                    'conditions' => $param['region_id'],
                    'fields' => 'region_name'
                ));
                $filters['region_id'] = array('key' => 'region_id', 'name' => LANG::get('region'), 'value' => $row['region_name']);
            }
            if (isset($param['price']))
            {
                $min = $param['price']['min'];
                $max = $param['price']['max'];
                if ($min <= 0)
                {
                    $filters['price'] = array('key' => 'price', 'name' => LANG::get('price'), 'value' => LANG::get('le') . ' ' . price_format($max));
                }
                elseif ($max <= 0)
                {
                    $filters['price'] = array('key' => 'price', 'name' => LANG::get('price'), 'value' => LANG::get('ge') . ' ' . price_format($min));
                }
                else
                {
                    $filters['price'] = array('key' => 'price', 'name' => LANG::get('price'), 'value' => price_format($min) . ' - ' . price_format($max));
                }
            }
        }


        return $filters;
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

        if ($data === false)
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
     * 根据关键词取得查询条件（可能是like，也可能是in）
     *
     * @param   array       $keyword    关键词
     * @param   bool        $cached     是否缓存
     * @return  string      " AND (0)"
     *                      " AND (goods_name LIKE '%a%' AND goods_name LIKE '%b%')"
     *                      " AND (goods_id IN (1,2,3))"
     */
    function _get_conditions_by_keyword($keyword, $cached)
    {
        $conditions = false;

        if ($cached)
        {
            $cache_server =& cache_server();
            $key1 = 'query_conditions_of_keyword_' . join("\t", $keyword);
            $conditions = $cache_server->get($key1);
        }

        if ($conditions === false)
        {
            /* 组成查询条件 */
            $conditions = array();
            foreach ($keyword as $word)
            {
                $conditions[] = "g.goods_name LIKE '%{$word}%'";
            }
            $conditions = join(' AND ', $conditions);

            /* 取得满足条件的商品数 */
            $goods_mod =& m('goods');
            $sql = "SELECT COUNT(*) FROM {$goods_mod->table} g WHERE " . $conditions;
            $current_count = $goods_mod->getOne($sql);
            if ($current_count > 0)
            {
                if ($current_count < MAX_ID_NUM_OF_IN)
                {
                    /* 取得商品表记录总数 */
                    $cache_server =& cache_server();
                    $key2 = 'record_count_of_goods';
                    $total_count = $cache_server->get($key2);
                    if ($total_count === false)
                    {
                        $sql = "SELECT COUNT(*) FROM {$goods_mod->table}";
                        $total_count = $goods_mod->getOne($sql);
                        $cache_server->set($key2, $total_count, SEARCH_CACHE_TTL);
                    }

                    /* 不满足条件，返回like */
                    if (($current_count / $total_count) < MAX_HIT_RATE)
                    {
                        /* 取得满足条件的商品id */
                        $sql = "SELECT goods_id FROM {$goods_mod->table} g WHERE " . $conditions;
                        $ids = $goods_mod->getCol($sql);
                        $conditions = 'g.goods_id' . db_create_in($ids);
                    }
                }
            }
            else
            {
                /* 没有满足条件的记录，返回0 */
                $conditions = "0";
            }

            if ($cached)
            {
                $cache_server->set($key1, $conditions, SEARCH_CACHE_TTL);
            }
        }

        return ' AND (' . $conditions . ')';
    }


    function _get_seo_info($type, $cate_id)
    {
        $seo_info = array(
            'title'       => '',
            'keywords'    => '',
            'description' => ''
        );
        $parents = array(); // 所有父级分类包括本身
        switch ($type)
        {
            case 'goods':
                if ($cate_id)
                {
                    $gcategory_mod =& bm('gcategory');
                    $parents = $gcategory_mod->get_ancestor($cate_id, true);
                    $parents = array_reverse($parents);
                }
                $filters = $this->_get_filter($this->_get_query_param());
                foreach ($filters as $k => $v)
                {
                    $seo_info['keywords'] .= $v['value']  . ',';
                }
                break;
            case 'store':
                if ($cate_id)
                {
                    $scategory_mod =& m('scategory');
                    $scategory_mod->get_parents($parents, $cate_id);
                    $parents = array_reverse($parents);
                }
        }

        foreach ($parents as $key => $cate)
        {
            $seo_info['title'] .= $cate['cate_name'] . ' - ';
            $seo_info['keywords'] .= $cate['cate_name']  . ',';
            if ($cate_id == $cate['cate_id'])
            {
                $seo_info['description'] = $cate['cate_name'] . ' ';
            }
        }
        $seo_info['title'] .= Lang::get('searched_'. $type) . ' - ' .Conf::get('site_title');
        $seo_info['keywords'] .= Conf::get('site_title');
        $seo_info['description'] .= Conf::get('site_title');
        return $seo_info;
    }
    private function _get_brands($param){
        $brand_model = m('brand');
        $brands = $brand_model->find(array('store_id'=>$this->agent_id));
        return $brands;
    }
}

?>
