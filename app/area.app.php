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
        $region_mod =& m('region');
        $area = $region_mod->get($region_id);
        $expire = 60*60*24*30;
        if($area){
            $parent = $region_mod->get($area['parent_id']);
            ecm_setcookie('area', $area['region_id'], time() + $expire);
            ecm_setcookie('area_name', $parent['region_name'] . "." . $area['region_name'], time() + $expire);
            $retval = 1;
            $mesg = 'ok';
        }else{
            $retval = "-1";
            $mesg = 'error';
        }
        $this->json_error($mesg, $retval);
    }
}