<?php
/**
 * Created by PhpStorm.
 * User: druphliu
 * Date: 14-2-11
 * Time: 下午2:30
 */
class AreaApp extends MallbaseApp
{
    function index()
    {
        $region_mod =& m('region');
        import('tree.lib');
        $tree = new Tree();
        $tree->setTree($region_mod->get_list(), 'region_id', 'parent_id', 'region_name');
        $region_list = $tree->getArrayList();
        $areas = $this->_getList($region_list,1);
        $this->assign('areas', $areas);
        //wap
        $this->assign('wapArea', $region_list);
        $this->assign('hot_area',array(13=>'四川大学.成华校区',14=>'四川大学.温江校区'));
        $this->display('area.html');
    }

    function _getList($list, $depth)
    {
        $result = '';
        if (is_array($list)) {
            foreach ($list as $m) {
                if ($m['children'] && is_array($m['children'])) {
                    $padding = $depth*10;
                    $result .= "<h{$depth} style='padding-left:{$padding}px'> " . $m['value'] . "</h{$depth}>";
                    $result .= $this->_getList($m['children'], $depth + 1);
                } else {
                    $padding = ($depth+1)*10;
                    $result .= " <span value='{$m["id"]}' style='padding-left: ".$padding."px' onclick='init_area({$m["id"]})'>{$m['value']}</span>";
                }
            }
        }
        return $result;
    }

    function init(){
        $region_id = intval($_GET['region_id']);
        if($this->set_area($region_id)){
            $retval = 1;
            $mesg = 'ok';
        }else{
            $retval = "-1";
            $mesg = 'error';
        }
        if(checkmobile()){
            header('HTTP/1.1 301 Moved Permanently');
            header('Location: /');
        }else{
            $this->json_error($mesg, $retval);
        }

    }
}