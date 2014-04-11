<?php

define('THUMB_WIDTH', 300);
define('THUMB_HEIGHT', 300);
define('THUMB_QUALITY', 85);

/* 品牌申请状态 */
define('BRAND_PASSED', 1);
define('BRAND_REFUSE', 0);

/* 商品管理控制器 */
class My_goodsApp extends StoreadminbaseApp
{
    var $_goods_mod;
    var $_spec_mod;
    var $_image_mod;
    var $_uploadedfile_mod;
    var $_store_id;
    var $_brand_mod;
    var $_last_update_id;

    /* 构造函数 */
    function __construct()
    {
         $this->My_goodsApp();
    }

    function My_goodsApp()
    {
        parent::__construct();

        $this->_store_id  = intval($this->visitor->get('manage_store'));
        $this->_goods_mod =& bm('goods', array('_store_id' => $this->_store_id));
        $this->_spec_mod  =& m('goodsspec');
        $this->_image_mod =& m('goodsimage');
        $this->_uploadedfile_mod =& m('uploadedfile');
        $this->_brand_mod =& m('brand');
    }

    function index()
    {
        /* 取得店铺商品分类 */
//        $this->assign('sgcategories', $this->_get_sgcategory_options());

        $conditions = $this->_get_conditions();
        $page = $this->_get_page();
        $page_nolimit = array();
        $goods_list = $this->_get_goods($conditions, $page);
        $all_goods = $this->_get_goods($conditions, $page_nolimit);      
        
        foreach ($goods_list as $key => $goods)
        {
            $goods_list[$key]['cate_name'] = $this->_goods_mod->format_cate_name($goods['cate_name']);
        }
        $this->assign('goods_list', $goods_list);

        $page['item_count'] = $this->_goods_mod->getCount();

        $this->_format_page($page);
        $this->assign('page_info', $page);
        $this->assign('order', $order);
        $this->assign('sort', $sort);
        $this->import_resource(array(
            'script' => array(
                array(
                    'path' => 'dialog/dialog.js',
                    'attr' => 'id="dialog_js" charset="utf-8"',
                ),
                array(
                    'path' => 'jquery.ui/jquery.ui.js',
                    'attr' => 'charset="utf-8"',
                ),
                array(
                    'path' => 'jquery.plugins/jquery.validate.js',
                    'attr' => 'charset="utf-8"',
                ),
                array(
                    'path' => 'utils.js',
                    'attr' => 'charset="utf-8"',
                ),
            ),
            'style' =>  'jquery.ui/themes/ui-lightness/jquery.ui.css',
      ));
        /* 当前页面信息 */
        $this->_curlocal(LANG::get('member_center'), url('app=member'),
                         LANG::get('my_goods'), url('app=my_goods'),
                         LANG::get('goods_list'));
        $this->_curitem('my_goods');
        $this->_curmenu('goods_list');
        //$this->import_resource(array('script' => 'utils.js'));
        $this->_config_seo('title', Lang::get('member_center') . ' - ' . Lang::get('my_goods'));
        $this->assign('goods_ids', implode(',', array_keys($all_goods)));
        
        $store_mod  =& m('store');
        $store = $store_mod->get_info($this->_store_id);
        $this->assign('store', $store);
        $this->display('my_goods.index.html');
    }
    
    function truncate()
    {
        $id = isset($_POST['goods_ids']) ? trim($_POST['goods_ids']) : '';
        if (!$id)
        {
            $this->show_warning('no_goods_to_drop');
            return;
        }

        $ids = explode(',', $id);
        $this->_goods_mod->drop_data($ids);
        $rows = $this->_goods_mod->drop($ids);
        if ($this->_goods_mod->has_error())
        {
            $this->show_warning($this->_goods_mod->get_error());
            return;
        }

        $this->show_message(sprintf(Lang::get('truncate_ok'), $rows),
            'back_list', url('app=my_goods')
        );
    }
    
    function _get_goods($conditions, &$page)
    {
        if (intval($_GET['sgcate_id']) > 0)
        {
            $cate_mod =& bm('gcategory', array('_store_id' => $this->_store_id));
            $cate_ids = $cate_mod->get_descendant_ids(intval($_GET['sgcate_id']));
        }
        else
        {
            $cate_ids = 0;
        }

        // 标识有没有过滤条件
        if ($conditions != '1 = 1' || !empty($_GET['sgcate_id']))
        {
            $this->assign('filtered', 1);
        }

        //更新排序
        if (isset($_GET['sort']) && isset($_GET['order']))
        {
            $sort  = strtolower(trim($_GET['sort']));
            $order = strtolower(trim($_GET['order']));
            if (!in_array($order,array('asc','desc')))
            {
                $sort  = 'goods_id';
                $order = 'desc';
            }
        }
        else
        {
            $sort  = 'goods_id';
            $order = 'desc';
        }
        
        if ($page)
        {
            $limit = $page['limit'];
            $count = true;
        }
        else
        {
            $limit = '';
            $count = false;
        }

        /* 取得商品列表 */
        
        $goods_list = $this->_goods_mod->get_list(array(
            'conditions' => $conditions,
            'count' => $count,
            'order' => "$sort $order",
            'limit' => $limit,
        ), $cate_ids);
        return $goods_list;
    }
    
    function _get_conditions()
    {
        /* 搜索条件 */
        $conditions = "1 = 1";
        if (trim($_GET['keyword']))
        {
            $str = "LIKE '%" . trim($_GET['keyword']) . "%'";
            $conditions .= " AND (goods_name {$str} OR brand {$str} OR cate_name {$str})";
        }
        if ($_GET['character'])
        {
            switch ($_GET['character'])
            {
                case 'show':
                    $conditions .= " AND if_show = 1";
                    break;
                case 'hide':
                    $conditions .= " AND if_show = 0";
                    break;
                case 'closed':
                    $conditions .= " AND closed = 1";
                    break;
                case 'recommended':
                    $conditions .= " AND g.recommended = 1";
                    break;
            }
        }
        
        return $conditions;
    }

    function batch_edit()
    {
        if (!IS_POST)
        {
             /* 取得商品分类 */
             $this->assign('mgcategories', $this->_get_mgcategory_options(0)); // 商城分类第一级
//             $this->assign('sgcategories', $this->_get_sgcategory_options());  // 店铺分类

             /* 当前页面信息 */
             $this->_curlocal(LANG::get('member_center'), url('app=member'),
                              LANG::get('my_goods'), url('app=my_goods'),
                              LANG::get('goods_add'));
             $this->_curitem('my_goods');
             $this->_curmenu('batch_edit');
             $this->_config_seo('title', Lang::get('member_center') . Lang::get('my_goods'));

             $this->headtag('<script type="text/javascript" src="{lib file=mlselection.js}"></script>');
             $this->import_resource(array(
                 'script' => array(
                     array(
                         'path' => 'mlselection.js',
                         'attr' => 'charset="utf-8"',
                     ),
                     array(
                         'path' => 'my_goods.js',
                         'attr' => 'charset="utf-8"',
                     ),
                     array(
                         'path' => 'jquery.plugins/jquery.validate.js',
                         'attr' => 'charset="utf-8"',
                     ),
                 ),
             ));
             $this->display('my_goods.batch.html');
        }
        else
        {
             $id = isset($_POST['id']) ? trim($_POST['id']) : '';
             if (!$id)
             {
                 $this->show_warning('Hacking Attempt');
                 return;
             }

             $ids = explode(',', $id);
             $ids = $this->_goods_mod->get_filtered_ids($ids); // 过滤掉非本店goods_id
             // edit goods
             $data = array();
             if ($_POST['cate_id'] > 0)
             {
                 if (!$this->_check_mgcate($_POST['cate_id']))
                 {
                     $this->show_warning('select_leaf_category');
                     return;
                 }
                 $data['cate_id'] = $_POST['cate_id'];
                 $data['cate_name'] = $_POST['cate_name'];
             }
             
             if (trim($_POST['brand']))
             {
                 $data['brand'] = trim($_POST['brand']);
             }
             if ($_POST['if_show'] >= 0)
             {
                 $data['if_show'] = $_POST['if_show'] ? 1 : 0;
             }
             if ($_POST['recommended'] >= 0)
             {
                 $data['recommended'] = $_POST['recommended'] ? 1 : 0;
             }
             if ($data)
             {
                 $this->_goods_mod->edit($ids, $data);
             }

             // edit category_goods
             $cate_ids = array();
             foreach ($_POST['sgcate_id'] as $cate_id)
             {
                 if ($cate_id)
                 {
                     $cate_ids[] = intval($cate_id);
                 }
             }
             $cate_ids = array_unique($cate_ids);
             if (!empty($cate_ids))
             {
                 foreach ($ids as $goods_id)
                 {
                     $this->_goods_mod->unlinkRelation('belongs_to_gcategory', $goods_id);
                     $this->_goods_mod->createRelation('belongs_to_gcategory', $goods_id, $cate_ids);
                 }
             }
             // edit goods_spec
             $sql = "";
             if ($_POST['price_change'])
             {
                 $delta_price = $this->_filter_price($_POST['price']); // 价格变化量
                 switch ($_POST['price_change'])
                 {
                     case 'change_to':
                         $sql .= "price = '" . $delta_price . "'";
                         break;
                     case 'inc_by':
                         $sql .= "price = price + '" . $delta_price . "'";
                         break;
                     case 'dec_by':
                         
                         $sql .= "price = IF((price - '" . $delta_price . "') <0 , 0, price - '" . $delta_price . "')";
                         break;
                 }
             }
             if ($sql)
             {
                 $this->_spec_mod->edit("goods_id" . db_create_in($ids), $sql);
                 $this->_goods_mod->edit($ids, $sql);
             }

             $sql = "";
             if ($_POST['stock_change'])
             {
                 $delta_stock = intval($_POST['stock']); // 库存变化量
                 switch ($_POST['stock_change'])
                 {
                     case 'change_to':
                         $sql .= "stock = '" . $delta_stock . "'";
                         break;
                     case 'inc_by':
                         $sql .= "stock = stock + '" . $delta_stock . "'";
                         break;
                     case 'dec_by':
                         $sql .= "stock = IF((stock - '" . $delta_stock . "') <0 , 0, stock - '" . $delta_stock . "')";
                         break;
                 }
             }
             if ($sql)
             {
                 $this->_spec_mod->edit("goods_id" . db_create_in($ids), $sql);
             }

             $ret_page = isset($_GET['ret_page']) ? intval($_GET['ret_page']) : 1;
             $this->show_message('edit_ok',
                 'back_list', url('app=my_goods&page=' . $ret_page));
        }
    }

    /* 检查商品分类：添加、编辑商品表单验证时用到 */
    function check_mgcate()
    {
        $cate_id = isset($_GET['cate_id']) ? intval($_GET['cate_id']) : 0;

        echo ecm_json_encode($this->_check_mgcate($cate_id));
    }

    function export_ubbcode()
    {
        $code = '';
        $crlf = "\n";
        $goods_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $goods_info = $this->_get_goods_info($goods_id);

        /* 默认图片 */
        $goods_info['default_image'] && $code .= '[img]' . SITE_URL . '/' . $goods_info['default_image'] . '[/img]' . $crlf;

        /* 商品名称 */
        $code .= '[b]' . Lang::get('goods_name') . ':[/b]' . addslashes($goods_info['goods_name']) . $crlf ;

        /* 品牌 */
        $goods_info['brand'] && $code .= '[b]' . Lang::get('brand_name') . ':[/b]' . addslashes($goods_info['brand']) . $crlf;

        /* 规格 */
        if ($goods_info['spec_qty'] == 0)
        {
            $code .= '[b]' . Lang::get('price') . ':[/b][color=Red]' . str_replace('&yen;', ' RMB', price_format($goods_info['price'])) . '[/color]' . $crlf;
        }
        elseif ($goods_info['spec_qty'] == 1 || $goods_info['spec_qty'] == 2)
        {
            $code .= '[b]' . Lang::get('price') . ':[/b]';
            foreach ($goods_info['_specs'] as $goods)
            {
                 $code .=  addslashes($goods['spec_1']) . '  ' . addslashes($goods['spec_2']) . '[color=Red]' . str_replace('&yen;', ' RMB', price_format($goods_info['price'])) . "[/color]\t";
            }
            $code .= $crlf;
        }

        /* 购买地址 */
        $url = SITE_URL . '/' . url('app=goods&id=' . $goods_info['goods_id']);
        $url = str_replace('&amp;', '&' , $url);
        $code .= '[b]' . Lang::get('buy_now') . ':[/b]' . '[url=' .$url . ']' . $url . '[/url]';
        $this->assign('code', $code);
        $this->assign('alert_code', str_replace("\n", '\\n', $code));

        header("Content-type:text/html;charset=" . CHARSET, true);
        $this->display('export_ubbcode.html');
    }

    /**
     * 检查商品分类（必选，且是叶子结点）
     *
     * @param   int     $cate_id    商品分类id
     * @return  bool
     */
    function _check_mgcate($cate_id)
    {
        if ($cate_id > 0)
        {
            $gcategory_mod =& bm('gcategory');
            $info = $gcategory_mod->get_info($cate_id);
            if ($info && $info['if_show'] && $gcategory_mod->is_leaf($cate_id))
            {
                return true;
            }
        }

        return false;
    }

    function add()
    {
        /* 检测商品数量 */
        if (!$this->_addible()) {
            return;
        }

        if (!IS_POST)
        {
             /* 添加传给iframe空的id,belong*/
             $this->assign("id", 0);
             $this->assign("belong", BELONG_GOODS);

             $this->assign('goods', $this->_get_goods_info(0));

             /* 取得游离状的图片 */
             $goods_images =array();
             $desc_images =array();
             $uploadfiles = $this->_uploadedfile_mod->find(array(
                 'join' => 'belongs_to_goodsimage',
                 'conditions' => "belong=".BELONG_GOODS." AND item_id=0 AND store_id=".$this->_store_id,
                 'order' => 'add_time ASC'
             ));
             foreach ($uploadfiles as $key => $uploadfile)
             {
                 if ($uploadfile['goods_id'] == null)
                 {
                     $desc_images[$key] = $uploadfile;
                 }
                 else
                 {
                     $goods_images[$key] = $uploadfile;
                 }
             }

             $this->assign('goods_images', $goods_images);
             $this->assign('desc_images', $desc_images);
             /* 取得商品分类 */
             $this->assign('mgcategories', $this->_get_mgcategory_options(0)); // 商城分类第一级
//             $this->assign('sgcategories', $this->_get_sgcategory_options());  // 店铺分类

             /* 当前页面信息 */
             $this->_curlocal(LANG::get('member_center'), url('app=member'),
                              LANG::get('my_goods'), url('app=my_goods'),
                              LANG::get('goods_add'));
             $this->_curitem('my_goods');
             $this->_curmenu('goods_add');
             $this->_config_seo('title', Lang::get('member_center') . ' - ' . Lang::get('goods_add'));

             /* 商品图片批量上传器 */
             $this->assign('images_upload', $this->_build_upload(array(
                 'obj' => 'GOODS_SWFU',
                 'belong' => BELONG_GOODS,
                 'item_id' => 0,
                 'button_text' => Lang::get('bat_upload'),
                 'progress_id' => 'goods_upload_progress',
                 'upload_url' => url('app=swfupload&instance=goods_image'),
                 'if_multirow' => 1,
             )));

             $this->import_resource(array(
                 'script' => array(
                     array(
                         'path' => 'mlselection.js',
                         'attr' => 'charset="utf-8"',
                     ),
                     array(
                         'path' => 'jquery.plugins/jquery.validate.js',
                         'attr' => 'charset="utf-8"',
                     ),
                     array(
                         'path' => 'jquery.ui/jquery.ui.js',
                         'attr' => 'charset="utf-8"',
                     ),
                     array(
                         'path' => 'my_goods.js',
                         'attr' => 'charset="utf-8"',
                     ),
                     array(
                         'attr' => 'id="dialog_js" charset="utf-8"',
                         'path' => 'dialog/dialog.js',
                     ),
                 ),
                 'style' =>  'jquery.ui/themes/ui-lightness/jquery.ui.css',
             ));

             $this->display('my_goods.form.html');
        }
        else
        {
            /* 取得数据 */
            $data = $this->_get_post_data(0);
            /* 检查数据 */
            if (!$this->_check_post_data($data, 0))
            {
                $this->show_warning($this->get_error());
                return;
            }
            /* 保存数据 */
            if (!$this->_save_post_data($data, 0))
            {
                $this->show_warning($this->get_error());
                return;
            }
            $goods_info = $this->_get_goods_info($this->_last_update_id);
            if ($goods_info['if_show'])
            {
                $goods_url = SITE_URL . '/' . url('app=goods&id=' . $goods_info['goods_id']);
                $feed_images = array();
                $feed_images[] = array(
                    'url'   => SITE_URL . '/' . $goods_info['default_image'],
                    'link'  => $goods_url,
                );
                $this->send_feed('goods_created', array(
                    'user_id' => $this->visitor->get('user_id'),
                    'user_name' => $this->visitor->get('user_name'),
                    'goods_url' => $goods_url,
                    'goods_name' => $goods_info['goods_name'],
                    'images' => $feed_images
                ));
            }

            $this->show_message('add_ok',
                'back_list', url('app=my_goods'),
                'continue_add', url('app=my_goods&amp;act=add')
            );
        }
    }

    function edit()
    {
        import('image.func');
        import('uploader.lib');
        $id = empty($_GET['id']) ? 0 : intval($_GET['id']);
        if (!IS_POST)
        {
            /* 传给iframe id */
            $this->assign('id', $id);
            $this->assign('belong', BELONG_GOODS);
            if(!$id || !($goods = $this->_get_goods_info($id)))
            {
                $this->show_warning('no_such_goods');
                return;
            }
            $goods['tags'] = trim($goods['tags'], ',');
            $this->assign('goods', $goods);
            /* 取到商品关联的图片 */
            $uploadedfiles = $this->_uploadedfile_mod->find(array(
                'fields' => "f.*,goods_image.*",
                'conditions' => "store_id=".$this->_store_id." AND belong=".BELONG_GOODS." AND item_id=".$id,
                'join'       => 'belongs_to_goodsimage',
                'order' => 'add_time ASC'
            ));
            $default_goods_images = array(); // 默认商品图片
            $other_goods_images = array(); // 其他商品图片
            $desc_images = array(); // 描述图片
            /*if (!empty($goods['default_image']))
            {
                   $goods_images
            }*/
            foreach ($uploadedfiles as $key => $uploadedfile)
            {
                if ($uploadedfile['goods_id'] == null)
                {
                    $desc_images[$key] = $uploadedfile;
                }
                else
                {
                    if (!empty($goods['default_image']) && ($uploadedfile['thumbnail'] == $goods['default_image']))
                    {
                        $default_goods_images[$key] = $uploadedfile;
                    }
                    else
                    {
                        $other_goods_images[$key] = $uploadedfile;
                    }
                }
            }

            $this->assign('goods_images', array_merge($default_goods_images, $other_goods_images));
            $this->assign('desc_images', $desc_images);

            /* 取得商品分类 */
            $this->assign('mgcategories', $this->_get_mgcategory_options(0)); // 商城分类第一级
//            $this->assign('sgcategories', $this->_get_sgcategory_options());  // 店铺分类

            /* 当前页面信息 */
            $this->_curlocal(LANG::get('member_center'), url('app=member'),
                             LANG::get('my_goods'), url('app=my_goods'),
                             LANG::get('goods_list'));
            $this->_curitem('my_goods');
            $this->_curmenu('edit_goods');
            $this->_config_seo('title', Lang::get('member_center') . ' - ' . Lang::get('edit_goods'));

            $this->import_resource(array(
                'script' => array(
                    array(
                         'path' => 'mlselection.js',
                         'attr' => 'charset="utf-8"',
                    ),
                    array(
                         'path' => 'jquery.plugins/jquery.validate.js',
                         'attr' => 'charset="utf-8"',
                    ),
                    array(
                         'path' => 'jquery.ui/jquery.ui.js',
                         'attr' => 'charset="utf-8"',
                    ),
                    array(
                         'path' => 'my_goods.js',
                         'attr' => 'charset="utf-8"',
                     ),
                    array(
                        'attr' => 'id="dialog_js" charset="utf-8"',
                        'path' => 'dialog/dialog.js',
                    ),
                ),
                'style' =>  'jquery.ui/themes/ui-lightness/jquery.ui.css',
            ));

            /* 商品图片批量上传器 */
            $this->assign('images_upload', $this->_build_upload(array(
                'obj' => 'GOODS_SWFU',
                'belong' => BELONG_GOODS,
                'item_id' => $id,
                'button_text' => Lang::get('bat_upload'),
                'progress_id' => 'goods_upload_progress',
                'upload_url' => url('app=swfupload&instance=goods_image'),
                'if_multirow' => 1,
            )));

            /* 编辑器图片批量上传器 */
//            $this->assign('editor_upload', $this->_build_upload(array(
//                'obj' => 'EDITOR_SWFU',
//                'belong' => BELONG_GOODS,
//                'item_id' => $id,
//                'button_text' => Lang::get('bat_upload'),
//                'button_id' => 'editor_upload_button',
//                'progress_id' => 'editor_upload_progress',
//                'upload_url' => url('app=swfupload&instance=desc_image'),
//                'if_multirow' => 1,
//                'ext_js' => false,
//                'ext_css' => false,
//            )));

            /* 所见即所得编辑器 */
//            extract($this->_get_theme());
//            $this->assign('build_editor', $this->_build_editor(array(
//                'name' => 'description',
//                'content_css' => SITE_URL . "/themes/store/{$template_name}/styles/{$style_name}" . '/shop.css', // for preview
//            )));

            $this->display('my_goods.form.html');
        }
        else
        {
            /* 取得数据 */
            $data = $this->_get_post_data($id);

            /* 检查数据 */
            if (!$this->_check_post_data($data, $id))
            {
                $this->show_warning($this->get_error());
                return;
            }
            /* 保存商品 */
            if (!$this->_save_post_data($data, $id))
            {
                $this->show_warning($this->get_error());
                return;
            }

            $this->show_message('edit_ok',
                'back_list', url('app=my_goods'),
                'edit_again', url('app=my_goods&act=edit&id=' . $id));
        }
    }

   function spec_edit()
   {
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if (!IS_POST)
        {
            $goods_spec = $this->_goods_mod->findAll(array(
                'fields' => "this.goods_name,this.goods_id,this.spec_name_1,this.spec_name_2",
                'conditions' => "goods_id = $id",
                'include' => array('has_goodsspec' => array('order'=>'spec_id')),
            ));

            header('Content-Type:text/html;charset=' . CHARSET);
            $this->assign('goods', current($goods_spec));
            $this->display("spec_edit.html");
        }
        else
        {
            $data = $this->save_spec($_POST);
            if (empty($data))
            {
                $this->pop_warning('not_data');
            }
            $default_spec = array(); // 更新商品中默认规格的信息
            foreach ($data as $key => $val)
            {
                if (empty($default_spec))
                {
                    $default_spec = array('price' => $val['price']);
                }
                $this->_spec_mod->edit($key, $val);
            }
            $this->_goods_mod->edit($id, $default_spec);
            $this->pop_warning('ok', 'my_goods_spec_edit');
        }
   }

   function save_spec($spec)
   {
        $data = array();
        if (empty($spec['price']) || empty($spec['stock']))
        {
            return $data;
        }
        foreach ($spec['price'] as $key => $val)
        {
            $data[$key]['price'] = $this->_filter_price($val);
        }
        foreach ($spec['stock'] as $key => $val)
        {
            $data[$key]['stock'] = intval($val);
        }
        return $data;
   }
     //异步修改数据
   function ajax_col()
   {
       $id        = empty($_GET['id']) ? 0 : intval($_GET['id']);
       $column = empty($_GET['column']) ? '' : trim($_GET['column']);
       $value  = isset($_GET['value']) ? trim($_GET['value']) : '';
       $data      = array('goods' => array(),
                          'specs' => array(),
                          'cates' => array());
       if (in_array($column ,array('goods_name','description', 'cate_id', 'cate_name', 'brand', 'spec_qty','if_show','closed','recommended')))
       {
           $data['goods'][$column] = $value;
           $this->_goods_mod->edit($id, $data['goods']);
           if(!$this->_goods_mod->has_error())
           {
               $result = $this->_goods_mod->get_info($id);
               $this->json_result($result[$column]);
               return;
           }
           else
           {
               $this->json_error($this->_goods_mod->get_error());
               return;
           }
       }
       elseif (in_array($column, array('price', 'stock', 'sku')))
       {
           if ($column == 'price')
           {
               $value = $this->_filter_price($value);
           }
           elseif ($column == 'stock')
           {
               $value = intval($value);
           }
       
           $data['specs'][$column] = $value;
           $this->_spec_mod->edit("goods_id = $id", $data['specs']);
           if(!$this->_spec_mod->has_error())
           {
               $result = $this->_spec_mod->get("goods_id = $id");
               //修改商品表中默认的字段的价格
               if ($column == 'price')
               {
                    $this->_goods_mod->edit($id, $data['specs']);
               }
               $this->json_result($result[$column]);
               return;
           }
           else
           {
               $this->json_error($this->_spec_mod->get_error());
               return;
           }

       }
       else
       {
           $this->json_error('unallow edit');
           return ;
       }
   }

    function drop()
    {
        $id = isset($_GET['id']) ? trim($_GET['id']) : '';
        if (!$id)
        {
            $this->show_warning('no_goods_to_drop');
            return;
        }

        $ids = explode(',', $id);
        $this->_goods_mod->drop_data($ids);
        $rows = $this->_goods_mod->drop($ids);
        if ($this->_goods_mod->has_error())
        {
            $this->show_warning($this->_goods_mod->get_error());
            return;
        }

        $this->show_message('drop_ok');
    }

    function unicodeToUtf8($str,$order="little")
    {
        $utf8string ="";
        $n=strlen($str);
        for ($i=0;$i<$n ;$i++ )
        {
            if ($order=="little")
            {
                $val = str_pad(dechex(ord($str[$i+1])), 2, 0, STR_PAD_LEFT) .
                       str_pad(dechex(ord($str[$i])),      2, 0, STR_PAD_LEFT);
            }
            else
            {
                $val = str_pad(dechex(ord($str[$i])),      2, 0, STR_PAD_LEFT) .
                       str_pad(dechex(ord($str[$i+1])), 2, 0, STR_PAD_LEFT);
            }
            $val = intval($val,16); // 由于上次的.连接，导致$val变为字符串，这里得转回来。
            $i++; // 两个字节表示一个unicode字符。
            $c = "";
            if($val < 0x7F)
            { // 0000-007F
                $c .= chr($val);
            }
            elseif($val < 0x800)
            { // 0080-07F0
                $c .= chr(0xC0 | ($val / 64));
                $c .= chr(0x80 | ($val % 64));
            }
            else
            { // 0800-FFFF
                $c .= chr(0xE0 | (($val / 64) / 64));
                $c .= chr(0x80 | (($val / 64) % 64));
                $c .= chr(0x80 | ($val % 64));
            }
            $utf8string .= $c;
        }
        /* 去除bom标记 才能使内置的iconv函数正确转换 */
        if (ord(substr($utf8string,0,1)) == 0xEF && ord(substr($utf8string,1,2)) == 0xBB && ord(substr($utf8string,2,1)) == 0xBF)
        {
            $utf8string = substr($utf8string,3);
        }
        return $utf8string;
    }

    function drop_image()
    {
        $id = empty($_GET['id']) ? 0 : intval($_GET['id']);
        $uploadedfile = $this->_uploadedfile_mod->get(array(
                  'conditions' => "f.file_id = '$id' AND f.store_id = '{$this->_store_id}'",
                  'join' => 'belongs_to_goodsimage',
                  'fields' => 'goods_image.image_url, goods_image.thumbnail, goods_image.image_id, f.file_id',
        ));
        if ($uploadedfile)
        {
            $this->_uploadedfile_mod->drop($id);
            if ($this->_image_mod->drop($uploadedfile['image_id']))
            {
                // 删除文件
                if (file_exists(ROOT_PATH . '/' . $uploadedfile['image_url']))
                {
                       @unlink(ROOT_PATH . '/' . $uploadedfile['image_url']);
                }
                if (file_exists(ROOT_PATH . '/' . $uploadedfile['thumbnail']))
                {
                       @unlink(ROOT_PATH . '/' . $uploadedfile['thumbnail']);
                }

                $this->json_result($id);
                return;
            }
            $this->json_result($id);
            return;
        }
        $this->json_error(Lang::get('no_image_droped'));
    }

    function _get_member_submenu()
    {
        if (ACT == 'index')
        {
            $menus = array(
                array(
                    'name' => 'goods_list',
                    'url'  => url('app=my_goods'),
                ),
                array(
                    'name' => 'brand_apply_list',
                    'url' => url('app=my_goods&act=brand_list')
                ),
            );
        }
        else
        {
             $menus = array(
                 array(
                     'name' => 'goods_list',
                     'url'  => url('app=my_goods'),
                 ),
                 array(
                     'name' => 'goods_add',
                     'url'  => url('app=my_goods&act=add'),
                 ),
//                 array(
//                     'name' => 'import_taobao',
//                     'url'  => 'app=my_goods&amp;act=import_taobao',
//                 ),
                 array(
                    'name' => 'brand_apply_list',
                    'url' => url('app=my_goods&act=brand_list')
                ),
             );
        }
        if (ACT == 'batch_edit')
        {
            $menus[] = array(
                'name' => 'batch_edit',
                'url'  => '',
            );
        }
        elseif (ACT == 'edit')
        {
            $menus[] = array(
                'name' => 'edit_goods',
                'url'  => '',
            );
        }
        elseif (ACT == 'brand_list')
        {
            $menus = array(
                array(
                    'name' => 'goods_list',
                    'url'  => url('app=my_goods'),
                ),
                array(
                    'name' => 'brand_apply_list',
                    'url' => url('app=my_goods&act=brand_list')
                ),
            );
        }
        return $menus;
    }

    /* 构造并返回树 */
    function &_tree($gcategories)
    {
        import('tree.lib');
        $tree = new Tree();
        $tree->setTree($gcategories, 'cate_id', 'parent_id', 'cate_name');
        return $tree;
    }

    /* 取得本店所有商品分类 */
    function _get_sgcategory_options()
    {
        $mod =& bm('gcategory', array('_store_id' => $this->_store_id));
        $gcategories = $mod->get_list();
        import('tree.lib');
        $tree = new Tree();
        $tree->setTree($gcategories, 'cate_id', 'parent_id', 'cate_name');
        return $tree->getOptions();
    }

    /* 取得商城商品分类，指定parent_id */
    function _get_mgcategory_options($parent_id = 0)
    {
        $res = array();
        $mod =& bm('gcategory', array('_store_id' => 0));
        $gcategories = $mod->get_list($parent_id, true);
        foreach ($gcategories as $gcategory)
        {
                  $res[$gcategory['cate_id']] = $gcategory['cate_name'];
        }
        return $res;
    }

    /**
     * 上传商品图片
     *
     * @param int $goods_id
     * @return bool
     */
    function _upload_image($goods_id)
    {
        import('image.func');
        import('uploader.lib');
        $uploader = new Uploader();
        $uploader->allowed_type(IMAGE_FILE_TYPE);
        $uploader->allowed_size(SIZE_GOODS_IMAGE); // 400KB

        /* 取得剩余空间（单位：字节），false表示不限制 */
        $store_mod  =& m('store');
        $settings      = $store_mod->get_settings($this->_store_id);
        $upload_mod =& m('uploadedfile');
        $remain        = $settings['space_limit'] > 0 ? $settings['space_limit'] * 1024 * 1024 - $upload_mod->get_file_size($this->_store_id) : false;

        $files = $_FILES['new_file'];
        foreach ($files['error'] as $key => $error)
        {
            if ($error == UPLOAD_ERR_OK)
            {
                /* 处理文件上传 */
                $file = array(
                    'name'            => $files['name'][$key],
                    'type'            => $files['type'][$key],
                    'tmp_name'  => $files['tmp_name'][$key],
                    'size'            => $files['size'][$key],
                    'error'        => $files['error'][$key]
                );
                $uploader->addFile($file);
                if (!$uploader->file_info())
                {
                    $this->_error($uploader->get_error());
                    return false;
                }

                /* 判断能否上传 */
                if ($remain !== false)
                {
                    if ($remain < $file['size'])
                    {
                        $this->_error('space_limit_arrived');
                        return false;
                    }
                    else
                    {
                        $remain -= $file['size'];
                    }
                }

                $uploader->root_dir(ROOT_PATH);
                $dirname      = 'data/files/store_' . $this->_store_id . '/goods_' . (time() % 200);
                $filename  = $uploader->random_filename();
                $file_path = $uploader->save($dirname, $filename);
                $thumbnail = dirname($file_path) . '/small_' . basename($file_path);
                make_thumb(ROOT_PATH . '/' . $file_path, ROOT_PATH . '/' . $thumbnail, THUMB_WIDTH, THUMB_HEIGHT, THUMB_QUALITY);

                /* 处理文件入库 */
                $data = array(
                    'store_id'  => $this->_store_id,
                    'file_type' => $file['type'],
                    'file_size' => $file['size'],
                    'file_name' => $file['name'],
                    'file_path' => $file_path,
                    'add_time'  => gmtime(),
                );
                $uf_mod =& m('uploadedfile');
                $file_id = $uf_mod->add($data);
                if (!$file_id)
                {
                    $this->_error($uf_mod->get_error());
                    return false;
                }

                /* 处理商品图片入库 */
                $data = array(
                    'goods_id'      => $goods_id,
                    'image_url'  => $file_path,
                    'thumbnail'  => $thumbnail,
                    'sort_order' => 255,
                    'file_id'       => $file_id,
                );
                if (!$this->_image_mod->add($data))
                {
                    $this->_error($this->_image_mod->get_error());
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * 检测店铺是否能添加商品
     *
     */
    function _addible()
    {
        /* 判断商品数是否已超过限制 */
        $store_mod =& m('store');
        $settings = $store_mod->get_settings($this->_store_id);
        if ($settings['goods_limit'] > 0)
        {
                  $goods_count = $this->_goods_mod->get_count();
                  if ($goods_count >= $settings['goods_limit'])
                  {
                         $this->show_warning('goods_limit_arrived');
                         return false;
                  }
        }
        return true;
    }
    /**
     * 保存远程图片
     */
    function _add_remote_image($goods_id)
    {
        foreach ($_POST['new_url'] as $image_url)
        {
            if ($image_url && $image_url != 'http://')
            {
                $data = array(
                    'goods_id' => $goods_id,
                    'image_url' => $image_url,
                    'thumbnail' => $image_url, // 远程图片暂时没有小图
                    'sort_order' => 255,
                    'file_id' => 0,
                );
                if (!$this->_image_mod->add($data))
                {
                    $this->_error($this->_image_mod->get_error());
                    return false;
                }
            }
        }

        return true;
    }
    /**
     * 编辑图片
     */
    function _edit_image($goods_id)
    {
        if (isset($_POST['old_order']))
        {
            foreach ($_POST['old_order'] as $image_id => $sort_order)
            {
                $data = array('sort_order' => $sort_order);
                if (isset($_POST['old_url'][$image_id]))
                {
                    $data['image_url'] = $_POST['old_url'][$image_id];
                }
                $this->_image_mod->edit("image_id = '$image_id' AND goods_id = '$goods_id'", $data);
            }
        }

        return true;
    }

    /**
     * 取得商品信息
     */
    function _get_goods_info($id = 0)
    {
        $default_goods_image = Conf::get('default_goods_image'); // 商城默认商品图片
        if ($id > 0)
        {
            $goods_info = $this->_goods_mod->get_info($id);
            if ($goods_info === false)
            {
                return false;
            }
            $goods_info['default_goods_image'] = $default_goods_image;
            if (empty($goods_info['default_image']))
            {
                   $goods_info['default_image'] = $default_goods_image;
            }
        }
        else
        {
            $goods_info = array(
                'cate_id' => 0,
                'if_show' => 1,
                'recommended' => 1,
                'price' => 1,
                'stock' => 1,
                'spec_qty' => 0,
                'spec_name_1' => Lang::get('amount'),
//                'spec_name_2' => Lang::get('size'),
                'default_goods_image' => $default_goods_image,
            );
        }
        $goods_info['spec_json'] = ecm_json_encode(array(
            'spec_qty' => $goods_info['spec_qty'],
            'spec_name_1' => isset($goods_info['spec_name_1']) ? $goods_info['spec_name_1'] : '',
//            'spec_name_2' => isset($goods_info['spec_name_2']) ? $goods_info['spec_name_2'] : '',
            'specs' => $goods_info['_specs'],
        ));
        return $goods_info;
    }

    /**
     * 提交的数据
     */
    function _get_post_data($id = 0)
    {
        $goods = array(
            'goods_name'       => $_POST['goods_name'],
            'description'      => trim($_POST['description']),
            'cate_id'             => $_POST['cate_id'],
            'cate_name'        => $_POST['cate_name'],
            'brand'                  => $_POST['brand'],
            'if_show'             => $_POST['if_show'],
            'last_update'      => gmtime(),
            'recommended'      => $_POST['recommended'],
            'tags'             => trim($_POST['tags']),
            'morning_start' => trim($_POST['morning_start']),
            'morning_end' => trim($_POST['morning_end']),
            'afternoon_start' => trim($_POST['afternoon_start']),
            'afternoon_end' => trim($_POST['afternoon_end']),
        );
        $spec_name_1 = !empty($_POST['spec_name_1']) ? $_POST['spec_name_1'] : '';
        $spec_name_2 = !empty($_POST['spec_name_2']) ? $_POST['spec_name_2'] : '';
        if ($spec_name_1 && $spec_name_2)
        {
            $goods['spec_qty'] = 2;
        }
        elseif ($spec_name_1 || $spec_name_2)
        {
            $goods['spec_qty'] = 1;
        }
        else
        {
            $goods['spec_qty'] = 0;
        }

        $goods_file_id = array();
        $desc_file_id =array();
        if (isset($_POST['goods_file_id']))
        {
            $goods_file_id = $_POST['goods_file_id'];
        }
        if (isset($_POST['desc_file_id']))
        {
            $desc_file_id = $_POST['desc_file_id'];
        }
        if ($id <= 0)
        {
            $goods['type'] = 'material';
            $goods['closed'] = 0;
            $goods['add_time'] = gmtime();
        }

        $specs = array(); // 原始规格
        switch ($goods['spec_qty'])
        {
            case 0: // 没有规格
                $specs[intval($_POST['spec_id'])] = array(
                    'price' => $this->_filter_price($_POST['price']),
                    'stock' => intval($_POST['stock']),
                    'sku'      => trim($_POST['sku']),
                    'spec_id'  => trim($_POST['spec_id']),
                );
                break;
            case 1: // 一个规格
                $goods['spec_name_1'] = $spec_name_1 ? $spec_name_1 : $spec_name_2;
                $goods['spec_name_2'] = '';
                $spec_data = $spec_name_1 ? $_POST['spec_1'] : $_POST['spec_2'];
                foreach ($spec_data as $key => $spec_1)
                {
                    $spec_1 = trim($spec_1);
                    if ($spec_1)
                    {
                        if (($spec_id = intval($_POST['spec_id'][$key]))) // 已有规格ID的
                        {
                            $specs[$key] = array(
                                'spec_id' => $spec_id,
                                'spec_1' => $spec_1,
                                'price'  => $this->_filter_price($_POST['price'][$key]),
                                'stock'  => intval($_POST['stock'][$key]),
                                'sku'       => trim($_POST['sku'][$key]),
                            );
                        }
                        else  // 新增的规格
                        {
                            $specs[$key] = array(
                                'spec_1' => $spec_1,
                                'price'  => $this->_filter_price($_POST['price'][$key]),
                                'stock'  => intval($_POST['stock'][$key]),
                                'sku'       => trim($_POST['sku'][$key]),
                            );
                        }

                    }
                }
                break;
            case 2: // 二个规格
                $goods['spec_name_1'] = $spec_name_1;
                $goods['spec_name_2'] = $spec_name_2;
                foreach ($_POST['spec_1'] as $key => $spec_1)
                {
                    $spec_1 = trim($spec_1);
                    $spec_2 = trim($_POST['spec_2'][$key]);
                    if ($spec_1 && $spec_2)
                    {
                        if (($spec_id = intval($_POST['spec_id'][$key]))) // 已有规格ID的
                        {
                            $specs[$key] = array(
                                'spec_id'   => $spec_id,
                                'spec_1'    => $spec_1,
                                'spec_2'    => $spec_2,
                                'price'     => $this->_filter_price($_POST['price'][$key]),
                                'stock'     => intval($_POST['stock'][$key]),
                                'sku'       => trim($_POST['sku'][$key]),
                            );
                        }
                        else // 新增的规格
                        {
                            $specs[$key] = array(
                                'spec_1'    => $spec_1,
                                'spec_2'    => $spec_2,
                                'price'     => $this->_filter_price($_POST['price'][$key]),
                                'stock'     => intval($_POST['stock'][$key]),
                                'sku'       => trim($_POST['sku'][$key]),
                            );
                        }


                    }
                }
                break;
            default:
                break;
        }

        /* 分类 */
        $cates = array();

//        foreach ($_POST['sgcate_id'] as $cate_id)
//        {
//            if (intval($cate_id) > 0)
//            {
//                $cates[$cate_id] = array(
//                    'cate_id'      => $cate_id,
//                );
//            }
//        }

        return array('goods' => $goods, 'specs' => $specs, 'cates' => $cates, 'goods_file_id' => $goods_file_id, 'desc_file_id' => $desc_file_id);
    }

    /**
     * 检查提交的数据
     */
    function _check_post_data($data, $id = 0)
    {
        if (!$this->_check_mgcate($data['goods']['cate_id']))
        {
            $this->_error('select_leaf_category');
            return;
        }
        if (!$this->_goods_mod->unique(trim($data['goods']['goods_name']), $id))
        {
            $this->_error('name_exist');
            return false;
        }
        if ($data['goods']['spec_qty'] == 1 && empty($data['goods']['spec_name_1'])
                  || $data['goods']['spec_qty'] == 2 && (empty($data['goods']['spec_name_1']) || empty($data['goods']['spec_name_2'])))
        {
            $this->_error('fill_spec_name');
            return false;
        }
        $pattern='/[0-9][0-9]:[0-9][0-9]/';
        if (preg_match($pattern, $data['morning_start']) && preg_match($pattern, $data['morning_end']) && preg_match
            ($pattern, $data['afternoon_start']) && preg_match($pattern, $data['afternoon_end']))
        {
            $this->_error('format_error');
            return false;
        }
        if (empty($data['specs']))
        {
            $this->_error('fill_spec');
            return false;
        }

        return true;
    }

    function _format_goods_tags($tags)
    {
        if (!$tags)
        {
            return '';
        }
        $tags = explode(',', str_replace(Lang::get('comma'), ',', $tags));
        array_walk($tags, create_function('&$item, $key', '$item=trim($item);'));
        $tags = array_filter($tags);
        $tmp = implode(',', $tags);
        if (strlen($tmp) > 100)
        {
            $tmp = sub_str($tmp, 100, false);
        }

        return ',' . $tmp . ',';
    }

    /**
     * 保存数据
     */
    function _save_post_data($data, $id = 0)
    {
        import('image.func');
        import('uploader.lib');

        if ($data['goods']['tags'])
        {
            $data['goods']['tags'] = $this->_format_goods_tags($data['goods']['tags']);
        }

        /* 保存商品 */
        if ($id > 0)
        {
            // edit
            if (!$this->_goods_mod->edit($id, $data['goods']))
            {
                $this->_error($this->_goods_mod->get_error());
                return false;
            }

            $goods_id = $id;
        }
        else
        {
            // add
            $goods_id = $this->_goods_mod->add($data['goods']);
            if (!$goods_id)
            {
                $this->_error($this->_goods_mod->get_error());
                return false;
            }
            if (($data['goods_file_id'] || $data['desc_file_id'] ))
            {
                $uploadfiles = array_merge($data['goods_file_id'], $data['desc_file_id']);
                $this->_uploadedfile_mod->edit(db_create_in($uploadfiles, 'file_id'), array('item_id' => $goods_id));
            }
            if (!empty($data['goods_file_id']))
            {
                $this->_image_mod->edit(db_create_in($data['goods_file_id'], 'file_id'), array('goods_id' => $goods_id));
            }
        }
        /* 保存规格 */
        if ($id > 0)
        {
            /* 删除的规格 */
            $goods_specs = $this->_spec_mod->find(array(
                'conditions' => "goods_id = '{$id}'",
                'fields' => 'spec_id'
            ));
            $drop_spec_ids = array_diff(array_keys($goods_specs), array_keys($data['specs']));
            if (!empty($drop_spec_ids))
            {
                $this->_spec_mod->drop($drop_spec_ids);
            }

        }
        $default_spec = array(); // 初始化默认规格
        foreach ($data['specs'] as $key => $spec)
        {
            if ($spec_id = $spec['spec_id']) // 更新已有规格ID
            {
                $this->_spec_mod->edit($spec_id,$spec);
            }
            else // 新加规格ID
            {
                $spec['goods_id'] = $goods_id;
                $spec_id = $this->_spec_mod->add($spec);
            }
            if (empty($default_spec))
            {
                $default_spec = array('default_spec' => $spec_id, 'price' => $spec['price']);
            }
        }

        /* 更新默认规格 */
        $this->_goods_mod->edit($goods_id, $default_spec);
        if ($this->_goods_mod->has_error())
        {
            $this->_error($this->_goods_mod->get_error());
            return false;
        }

        /* 保存商品分类 */
        $this->_goods_mod->unlinkRelation('belongs_to_gcategory', $goods_id);
        if ($data['cates'])
        {
            $this->_goods_mod->createRelation('belongs_to_gcategory', $goods_id, $data['cates']);
        }

        /* 设置默认图片 */
        if (isset($data['goods_file_id'][0]))
        {
            $default_image = $this->_image_mod->get(array(
                'fields' => 'thumbnail',
                'conditions' => "goods_id = '$goods_id' AND file_id = '{$data[goods_file_id][0]}'",
            ));
            $this->_image_mod->edit("goods_id = $goods_id", array('sort_order' => 255));
            $this->_image_mod->edit("goods_id = $goods_id AND file_id = '{$data[goods_file_id][0]}'", array('sort_order' => 1));
        }

        $this->_goods_mod->edit($goods_id, array(
            'default_image' => $default_image ? $default_image['thumbnail'] : '',
        ));

        $this->_last_update_id = $goods_id;

        return true;
    }

    //品牌申请列表
    function brand_list()
    {
        $_GET['store_id'] = $this->_store_id;
        $_GET['if_show'] = BRAND_PASSED;
        $con = array(
            array(
                'field' => 'store_id',
                'name'  => 'store_id',
                'equal' => '=',
            ),
            array(
                'field' => 'if_show',
                'name'  => 'if_show',
                'equal' => '=',
                'assoc' => 'or',
            ),);
        $filtered = '';
        if (!empty($_GET['brand_name']) || !empty($_GET['store']))
        {
            $_GET['brand_name'] && $filtered = " AND brand_name LIKE '%{$_GET['brand_name']}%'";
            $_GET['store'] && $filtered = $filtered . " AND store_id = " . $this->_store_id;
        }
        if (isset($_GET['sort']) && isset($_GET['order']))
        {
            $sort  = strtolower(trim($_GET['sort']));
            $order = strtolower(trim($_GET['order']));
            if (!in_array($order,array('asc','desc')))
            {
                $sort  = 'store_id';
                $order = 'desc';
            }
        }
        else
        {
            $sort  = 'store_id';
            $order = 'desc';
        }
        $page = $this->_get_page(10);
        $conditions = $this->_get_query_conditions($con);
        $brand = $this->_brand_mod->find(array(
            'conditions' => "(1=1 $conditions)" . $filtered,
            'limit' => $page['limit'],
            'order' => "$sort $order",
            'count' => true,
        ));
        $page['item_count'] = $this->_brand_mod->getCount();
        $this->_format_page($page);
        $this->_curlocal(LANG::get('member_center'), url('app=member'),
                         LANG::get('my_goods'), url('app=my_goods'),
                         LANG::get('brand_list'));
        $this->_curitem('my_goods');
        $this->_curmenu('brand_apply_list');
        $this->import_resource(array(
                 'script' => array(
                     array(
                         'path' => 'jquery.plugins/jquery.validate.js',
                         'attr' => 'charset="utf-8"',
                     ),
                     array(
                         'path' => 'jquery.ui/jquery.ui.js',
                         'attr' => 'charset="utf-8"',
                     ),
                     array(
                         'attr' => 'id="dialog_js" charset="utf-8"',
                         'path' => 'dialog/dialog.js',
                     ),
                 ),
                 'style' =>  'jquery.ui/themes/ui-lightness/jquery.ui.css',
             ));
        $this->assign('page_info', $page);
        $this->assign('filtered', empty($filtered) ? 0 : 1);
        $this->assign('brands', $brand);
        $this->display('brand_list.html');
    }

    //品牌申请

    function brand_apply()
    {
        if (!IS_POST)
        {
            header("Content-Type:text/html;charset=" . CHARSET);
            $this->display('brand_apply.html');
        }
        else
        {
            $brand_name = trim($_POST['brand_name']);
            $brand_desc = time($_POST['brand_desc']);
            if (empty($brand_name))
            {
                $this->pop_warning("brand_name_required");
                exit;
            }
            if(empty($brand_desc)){
                $this->pop_warning('brand_desc_required');
                exit;
            }

            if (!$this->_brand_mod->unique($brand_name, $this->_store_id))
            {
                $this->pop_warning('brand_name_exist');
                return;
            }
            if (!$brand_id = $this->_brand_mod->add(array('brand_name' => $brand_name, 'brand_desc' => $brand_desc, 'store_id' => $this->_store_id, 'if_show' => 1, 'tag' => trim($_POST['tag']))))  //获取brand_id
            {
                $this->pop_warning($this->_brand_mod->get_error());

                return;
            }
            $this->pop_warning('ok',
                'my_goods_brand_apply', url('app=my_goods&act=brand_list'));
        }
    }

    function brand_edit()
    {
        $id = intval($_GET['id']);
        $brand = $this->_brand_mod->find('store_id = ' . $this->_store_id . ' AND brand_id = ' . $id);
        $brand = current($brand);
        if (empty($brand))
        {
            $this->show_warning("not_rights");
            exit;
        }
        if (!IS_POST)
        {
            header("Content-Type:text/html;charset=" . CHARSET);
            $this->assign('brand', $brand);
            $this->display('brand_apply.html');
        }
        else
        {
            $brand_desc = trim($_POST['brand_desc']);
            $brand_name = trim($_POST['brand_name']);
            if (!$this->_brand_mod->unique($brand_name, $this->_store_id, $id))
            {
                $this->pop_warning('brand_name_exist');
                return;
            }
            if(empty($brand_desc)){
                $this->pop_warning('brand_desc_required');
                exit;
            }
            $data['brand_name'] = $brand_name;
            $data['brand_desc'] = $brand_desc;
            $data['tag'] = trim($_POST['tag']);
            $this->_brand_mod->edit($id, $data);
            if ($this->_brand_mod->has_error())
            {
                $this->pop_warning($this->_brand_mod->get_error());
                exit;
            }
            $this->pop_warning('ok', 'my_goods_brand_edit');
        }

    }

    function brand_drop()
    {
        $id = intval($_GET['id']);
        if (empty($id))
        {
            $this->show_warning('request_error');
            exit;
        }
        $brand = $this->_brand_mod->find("store_id = " . $this->_store_id . " AND brand_id = " . $id);
        $brand = current($brand);
        if (empty($brand))
        {
            $this->show_warning('request_error');
            exit;
        }
        if (!$this->_brand_mod->drop($id))
        {
            $this->show_warning($this->_brand_mod->get_error());
            exit;
        }
        $this->show_message('drop_brand_ok',
            'back_list', url('app=my_goods&act=brand_list'));

    }

    function check_brand()
    {
        $brand_name = $_GET['brand_name'];
        if (!$brand_name)
        {
            echo ecm_json_encode(true);
            return ;
        }
        if ($this->_brand_mod->unique($brand_name, $this->_store_id))
        {
            echo ecm_json_encode(true);
        }
        else
        {
            echo ecm_json_encode(false);
        }
        return ;
    }
    
    /* 价格过滤，返回非负浮点数 */
    function _filter_price($price)
    {
        return abs(floatval($price));
    }
}

?>
