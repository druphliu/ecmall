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
        $regions = $region_mod->get_options(0);
        $areas = $sub_options = '';
        foreach ($regions as $value => $reg) {
            $areas .= "<optgroup label='$reg'>";
            $sub_regions = $region_mod->get_options($value);
            foreach ($sub_regions as $sub_key => $sub_name) {
                $sub_options .= " <option value='$sub_key' onclick='init_area($sub_key)'>$reg.$sub_name</option>";
            }
            $areas .= $sub_options . "</optgroup>";
            unset($sub_options);
        }
        $this->assign('areas', $areas);
        $this->display('area.html');
    }

    function init(){
        $region_id = intval($_GET['region_id']);
        $region_mod =& m('region');
        $area = $region_mod->get($region_id);
        if($area){
            $parent = $region_mod->get($area['parent_id']);
            ecm_setcookie('area', $area['region_id']);
            ecm_setcookie('area_name', $parent['region_name'].".".$area['region_name']);
            $retval = 1;
            $mesg = 'ok';
        }else{
            $retval = "-1";
            $mesg = 'error';
        }
        $this->json_error($mesg, $retval);
    }
}