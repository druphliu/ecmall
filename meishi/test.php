<?php
/**
 * Created by PhpStorm.
 * User: druphliu
 * Date: 14-3-29
 * Time: 下午12:15
 */

if (@$_GET['type'] == 'ajax') {
    $id = intval($_GET['id']);
    $list = show_select($id);
    if (@$list[1]) {
        echo '<script>
    $().ready(function(){
        var $category_id = $("#category' . $id . '");
        var $html_id = $("#html' . $id . '");
        $category_id.change(function(){
            var id = $(this).val();
            $.get("test.php",{type:"ajax",id:id},
            function(data){
                $html_id.html(data);
            })
        })
    })
</script>';
        echo "<select id='category{$id}'>";
        foreach ($list[1] as $v) {
            echo "<option value='{$v['id']}'>{$v['name']}</option>";
        }
        echo "</select><span id='html{$id}'></span>";
    }
    exit;
}

$list = array(
    array('catid' => 1,
        'parentid' => 0,
        'name' => '1'),
    array('catid' => 2,
        'parentid' => 1,
        'name' => '2'),
    array('catid' => 3,
        'parentid' => 2,
        'name' => '3'),
    array('catid' => 4,
        'parentid' => 3,
        'name' => '4'),
    array('catid' => 5,
        'parentid' => 0,
        'name' => '5'),
    array('catid' => 6,
        'parentid' => 5,
        'name' => '6'),
    array('catid' => 7,
        'parentid' => 6,
        'name' => '7'),
    array('catid' => 8,
        'parentid' => 7,
        'name' => '8'),
    array('catid' => 9,
        'parentid' => 8,
        'name' => '9'),
    array('catid' => 10,
        'parentid' => 8,
        'name' => '10'),
    array('catid' => 11,
        'parentid' => 8,
        'name' => '11'),
    array('catid' => 12,
        'parentid' => 9,
        'name' => '12'),
);

function get_list($list, $level = 0, $parentid = 0)
{
    $tag = str_repeat("-----", $level);
    foreach ($list as $key=>$v) {
        if ($v['parentid'] == $parentid) {
            echo "<option value='$v[catid]'>{$tag}{$v['name']}</option>";
            unset($list[$key]);
            get_list($list, ++$level, $v['catid']);
            $level = 0;
        }
    }
}

echo "<select>";
list($start_time) = explode(' ', microtime());
get_list($list);
list($end_time) = explode(' ', microtime());
echo "</select><br>";
echo ($start_time*1000000)."<br>";
echo ($end_time*1000000)."<br>";
echo (intval($end_time*1000000) - intval($start_time*1000000));


$conn = mysql_connect ( 'localhost', 'root', '' );
mysql_select_db ( 'test', $conn );
mysql_query ( 'set names UTF8' );
$sql = "select id, concat(catpath,'-',id) as abspath,name from category order by abspath";
$query = mysql_query ( $sql );
$option = '';
while ( $row = mysql_fetch_array ( $query ) )
{
    //第一种展示方法
//    $space = str_repeat ( '&nbsp;&nbsp;&nbsp;&nbsp;', count ( explode ( '-', $row ['abspath'] ) ) - 1 );
//    echo $space . $row ['name'] . '<br>';

    //第二种展示方法
    $space = str_repeat ( '&nbsp;&nbsp;&nbsp;&nbsp;', count ( explode ( '-', $row ['abspath'] ) ) - 2 );
    $option .= '<option value="' . $row ['id'] . '">' . $space . '|-'.$row ['name'] . '</option>';
}
echo '<select name="opt">' . $option . '</select>';

echo strcasecmp('0-1-3','0-1-2-7');
echo "<br>";
echo base_convert('0-1-2-6', 16, 2);
echo "<br>";
echo base_convert('0-1-2-7', 16, 2);
echo "<br>";
echo base_convert('0-1-3', 16, 2);
echo "<br>";
function display_tree($id=1){
    $conn = mysql_connect ( 'localhost', 'root', '' );
    mysql_select_db ( 'test', $conn );
    mysql_query ( 'set names UTF8' );
    $sql = "select * from categorys where id = $id";
    $query = mysql_query ( $sql );
    $arr_lr = mysql_fetch_array($query);
    if($arr_lr){
        $right = array();
        $arr_tree_query = mysql_query("SELECT id, type, title, rgt FROM categorys  WHERE lft >= ". $arr_lr['lft'] ."
        AND  lft <=" .$arr_lr['rgt']." ORDER BY lft");
        while($v = mysql_fetch_array($arr_tree_query)){
            if(count($right)){
                while ($right[count($right) -1] < $v['rgt']){
                    array_pop($right);
                }
            }
            $title = $v['title'];
            if(count($right)){
                $title = '|-'.$title;
            }
            $arr_list[count($right)][] = array('id' => $v['id'], 'type' => $v['type'],
                'title' => str_repeat('&nbsp;&nbsp;', count($right)).$title, 'name' =>$v['title']);
            echo "<option value='{$v['id']}'>".str_repeat('&nbsp;&nbsp;', count($right)).$title."</option> ";
            $right[]  = $v['rgt'];
        }
        return $arr_list;
    }
}
echo '<select name="opt">';
$list = display_tree();
echo '</select>';
print_r($list);

class father
{
    //    function __construct(){
    //        echo "father<br>";
    //    }
    static function message()
    {
        echo "this is a message<br>";
    }
}

class test extends father
{
    //    function __construct(){
    //        parent::__construct();
    //        echo "construction<br>";
    //    }
    function test_message()
    {
        parent::message();
    }
}

header("Content-Type:text/html;charset=utf-8");

$test = new test();
$test->test_message();
include '/test2.php';
$test2 = new test2();
$test2->test2_message();

$year_now = date("Y", time());
$indent = 100;
for ($i = $year_now - $indent; $i < $year_now + $indent; $i++) {
    if (checkdate(2, 29, $i)) {
        echo "闰年:$i";
    };
}

abstract class abstract_class
{
    abstract function abstract_test();

    function test()
    {
        $this->abstract_test();
    }
}

class extend_class extends abstract_class
{

    function abstract_test()
    {
        // TODO: Implement abstract_test() method.
        echo 'sub_class';
    }
}

$abstract_obj = new extend_class();
$abstract_obj->test();
echo "<br>";
$S_ROOT = dirname(__FILE__);
echo $S_ROOT;

class arraySort
{
    protected $array;

    function __construct($array)
    {
        $this->array = $array;
    }

    function sort()
    {
        if (is_array($this->array)) {
            $count = count($this->array);
            for ($i = 0; $i < $count; $i++) {
                for ($j = $count - 1; $j > $i; $j--) {
                    if ($this->array[$j] > $this->array[$j - 1]) {
                        $tmp = $this->array[$j];
                        $this->array[$j] = $this->array[$j - 1];
                        $this->array[$j - 1] = $tmp;
                    }
                }
            }
            return $this->array;
        } else {
            return;
        }
    }
}

$pattern = '/(\w+)@(\w+)+.[a-z]{2,3}$/';
$array = array('test@tes.com', 't@sd.ds', '_90*@*.c.com', 'test@test.com.cn');
foreach ($array as $v) {
    if (preg_match($pattern, $v)) {
        echo "is ok :" . $v . "<br>";
    } else {
        echo "not ok:" . $v . "<br>";
    }
}

$a = 1;
$b = 2;
$a = explode("_", $a . '_' . $b);
$b = $a[0];
$a = $a[1];
echo $a . $b;

function money($value)
{
    $result = '';
    $v_a = array('分', '角', '零', '块', '十', '百', '千', '万', '十', '百', '千', '亿');
    $v_b = array('零', '一', '二', '三', '四', '五', '六', '七', '八', '九', '十');

    $value = (string)$value;
    $value = sprintf("%0.2f", $value);
    $len = strlen($value);
    $array_offset = 1;
    for ($i = $len; $i > 0; $i--) {
        $key = $len - $i;
        if ($value{$key} != '.') {
            $v = $v_b[$value{$key}];
        } else {
            $v = '';
        }
        $result .= $v . $v_a[$i - $array_offset];
    }

    return $result;
}

echo money('2345.56');
echo "<br>----------------------------------------------------------------------------------------------------<br>";
$a = 1;
$b =& $a;
$a = 3;
echo $b;
$b = 4;
echo $a;
echo "<br>----------------------------------------------------------------------------------------------------<br>";

class testReload
{
    function reload_function()
    {
        echo "parent reload function";
    }
}
class static_one {
    static function test1(){
        return  1;
    }
    static function test2(){
        return self::test1();
    }
}
class static_two extends static_one{
    static function test3(){
        parent::test1();
    }
}
class testExtendReload extends testReload
{
    function reload_function()
    {
        echo "child reload function" ;
    }
}

$testReloadObj = new testExtendReload();
$testReloadObj->reload_function();

function show_select($id=1){
    $conn = mysql_connect ( 'localhost', 'root', '' );
    mysql_select_db ( 'test', $conn );
    mysql_query ( 'set names UTF8' );
    $sql = "select * from categorys where id = $id";
    $query = mysql_query ( $sql );
    $arr_lr = mysql_fetch_array($query);
    if($arr_lr){
        $right = array();
        $arr_tree_query = mysql_query("SELECT id, type, title, rgt FROM categorys  WHERE lft >= ". $arr_lr['lft'] ."
        AND  lft <=" .$arr_lr['rgt']." ORDER BY lft");
        while($v = mysql_fetch_array($arr_tree_query)){
            if(count($right)){
                while ($right[count($right) -1] < $v['rgt']){
                    array_pop($right);
                }
            }
            $title = $v['title'];
            if(count($right)){
                $title = '|-'.$title;
            }
            $arr_list[count($right)][] = array('id' => $v['id'], 'type' => $v['type'],
                'title' => str_repeat('&nbsp;&nbsp;', count($right)).$title, 'name' =>$v['title']);
            $right[]  = $v['rgt'];
        }
        return $arr_list;
    }
};

echo '<br>----------------------------------------------------------------<br>';
$test = null;
echo '$test = null';
echo '<br>----------------------------------------------------------------<br>';
echo 'isset($test):'.isset($test);
echo '<br>----------------------------------------------------------------<br>';
echo 'empty($test):'.empty($test);
echo '<br>----------------------------------------------------------------<br>';
echo 'count("123"):'.count('123');
echo '<br>----------------------------------------------------------------<br>';
echo '<br>----------------------------------------------------------------<br>';
function ExplodesLines($text, $columnNames)
{
    if (!is_array($columnNames) && empty($text)) {
        return;
    }
    $text_arr = explode("\n", $text);
    foreach ($text_arr as $str) {
        if (trim($str)) {
            $line_arr = explode(',', $str);
            foreach ($line_arr as $k => $value) {
                $array[$columnNames[$k]] = $value;
            }
            $result[] = $array;
        }
    }
    return $result;
}
$text = "
Apple,20,red
Pear,10,yellow
";

$columnNames = array('Fruit', 'Number', 'Color');
print_r(ExplodesLines($text, $columnNames));
echo '<br>----------------------------------------------------------------<br>';
function CalcRefundAmount($orderItems, $discountAmount, $refundItems){
    $total = $refundMoney = 0;
    if(!is_array($orderItems)&&!is_array($refundItems[0])){
        return;
    }
    foreach($orderItems as $item){
        $total +=$item['price']*$item['quantity'];
    }
    $rate = 1 - $discountAmount / $total;
    foreach($refundItems[0] as $refundItem=>$count){
      if($orderItems[$refundItem]){
          $price = $orderItems[$refundItem]['price'];
          $refundMoney +=$price*$count*$rate;
      }
        return round($refundMoney, 2);
    }
}
$orderItems = array(
    'ItemA'=>array('price'=>5.0, 'quantity'=>2),
    'ItemB'=>array('price'=>20.0, 'quantity'=>1)
);
$refundItems = array( array('ItemA'=>2, 'ItemB'=>1) ) ;
$discountAmount = 10.0;
echo CalcRefundAmount($orderItems, $discountAmount, $refundItems);
echo '<br>----------------------------------------------------------------<br>';
function string_sort($string)
{
    $string_to_array = explode(',', $string);
    $key = array_search(5, $string_to_array);
    $key_prev = $key - 1;
    $array_prev = array_key_exists($key_prev, $string_to_array) ? $string_to_array[$key_prev] : '';
    $key_next = $key + 1;
    $array_next = array_key_exists($key_next, $string_to_array) ? $string_to_array[$key_next] : '';
    if ($key == 2 || ($array_prev && $array_prev == 6) || ($array_next && $array_next == 6)) {
        shuffle($string_to_array);
        $array_to_string = implode(',', $string_to_array);
        $string = string_sort($array_to_string);
    } else {
        $string = implode(',', $string_to_array);
    }
    return $string;
}
echo string_sort('1,2,5,6,3,4,7');
echo '<br>----------------------------------------------------------------<br>';
$string = 'aaabbdcdess';
function signal_string_first_pos($string)
{
    $length = strlen($string);
    for ($i = 0; $i < $length; $i++) {
        $on_string = $string{$i};
        if (strrpos($string, $on_string) == $i && strpos($string, $on_string) == $i) {
            return $on_string;
        }
    }
    return;
}
echo signal_string_first_pos($string);
echo '<br>----------------------------------------------------------------<br>';
echo date("Y-m-d h:i:s",strtotime("-1 days"));
echo '<br>----------------------------------------------------------------<br>';
$p = Null;
echo isset($p);
echo '<br>----------------------------------------------------------------<br>';
echo strrev('abcdef');
echo '<br>----------------------------------------------------------------<br>';
echo $_SERVER['REMOTE_ADDR'];
echo '<br>----------------------------------------------------------------<br>';
echo $_SERVER['SERVER_ADDR'];
echo '<br>----------------------------------------------------------------<br>';
echo <<<START
kdsjfkdsfsdf
START;
echo '<br>----------------------------------------------------------------<br>';
$pattern = "/(\w+)+\@+(\w+)+\.+[a-z]{2,4}/";
echo preg_match($pattern, "druphliu@gmail.com");
echo '<br>----------------------------------------------------------------<br>';
echo dirname(__file__);
header("HTTP/1.0 404 not found");
echo '<br>----------------------------------------------------------------<br>';
echo "sss"+12;
echo '<br>----------------------------------------------------------------<br>';
$str1 = null;
$str2 = false;
echo $str1==$str2 ?'相等' :'不相等';
echo '<br>----------------------------------------------------------------<br>';
$str3 = '';
$str4 = 0;
echo $str3==$str4 ? '相等' : '不相等';
echo '<br>----------------------------------------------------------------<br>';
$str5 = 0;
$str6 = '0';
echo $str5===$str6 ? '相等' : '不相等';
echo '<br>----------------------------------------------------------------<br>';
$str7 = 0;
$str8 = null;
echo $str7==$str8 ? '相等' : '不相等';
echo '<br>----------------------------------------------------------------<br>';
$test = 'aaaaaa';
$abc = & $test;
unset($test);
echo $abc;
echo '<br>----------------------------------------------------------------<br>';
$a1 = null;
$a2 = false;
$a3 = 0;
$a4 = '';
$a5 = '0';
$a6 = 'null';
$a7 = array();
$a8 = array(array());
echo empty($a1) ? 'true' : 'false';
echo empty($a2) ? 'true' : 'false';
echo empty($a3) ? 'true' : 'false';
echo empty($a4) ? 'true' : 'false';
echo empty($a5) ? 'true' : 'false';
echo empty($a6) ? 'true' : 'false';//false
echo empty($a7) ? 'true' : 'false';//true
echo empty($a8) ? 'true' : 'false';//false
echo '<br>----------------------------------------------------------------<br>';
$count = 5;
function get_count(){
    static $count = 0;
    return $count++;
}
echo $count;
++$count;
echo get_count();
echo get_count();
echo '<br>----------------------------------------------------------------<br>';
$GLOBALS['var1'] = 5;
$var2 = 1;
function get_value(){
    global $var2;
    $var1 = 0;
    return $var2++;
}
echo get_value();
echo $var1;
echo $var2;
echo '<br>----------------------------------------------------------------<br>';
function get_arr($arr){
    unset($arr[0]);
}
$arr1 = array(1, 2);
$arr2 = array(1, 2);
get_arr(&$arr1);
get_arr($arr2);
echo count($arr1);
echo count($arr2);
echo '<br>----------------------------------------------------------------<br>';
$file = 'dir/upload.image.jpg';
$start = strrpos($file,'.');
echo substr($file,$start+1);
?>

<script type="application/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<select id="category">
    <option>请选择</option>
    <?php foreach($list[1] as $v){?>
        <option value="<?=$v['id']?>"><?= $v['name'] ?></option>
    <?php }?>
</select>
<span id="html"></span>
<script>
    $().ready(function(){
        $("#category").change(function(){
            var id = $(this).val();
            $.get("test.php",{type:"ajax",id:id},
            function(data){
                $("#html").html(data);
            })
        })
    })
</script>