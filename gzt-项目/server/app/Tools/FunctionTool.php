<?php
/**
 * Created by PhpStorm.
 * User: mayn
 * Date: 2018/10/29
 * Time: 10:37
 */

namespace App\Tools;

use Illuminate\Support\Facades\DB;

/**
 * 公共方法类
 * Class FunctionTool
 * @package App\Tools
 */
class FunctionTool
{
    static private $functionTool;
    /**
     *私有构造函数防止new
     */
    private function __construct()
    {
    }
    /**
     * 单例模式
     */
    static public function getFunctionTool(){
        if(self::$functionTool instanceof self)
        {
            return self::$functionTool;
        }else{
            return self::$functionTool = new self;
        }
    }
    /**
     * 防止被克隆
     */
    private function _clone(){

    }
    /**
     * 提取Eloquent模型中的指定数据到数组中
     * @param $arrA:Eloquent数组
     * @param $arrB:要提取的数据列
     */
    public function extractModelColumn($arrA,$arrB){
        $data=[];
        foreach ($arrA as $model){
            $arr=null;//抽取的模型列
            foreach ($arrB as $column){
                $data[]=$model->$column;
            }
        }
        return $data;
    }
    /**
     * 将所有的id字段加密处理
     */
    public static function encrypt_id($id){
        return str_random(config('encrypt_id.str_length')).($id*config('encrypt_id.base_number')+2018);
    }
    /**
     * 将所有的id字段解密处理
     */
    public static function decrypt_id($id){
       return (substr($id,config('encrypt_id.str_length'))-2018)/config('encrypt_id.base_number');
    }
    /**
     * 将所有的id字段解密处理(array)
     */
    public static function decrypt_id_array($ids){
        $data=[];
        foreach ($ids as $id){
            $data[]=(substr($id,config('encrypt_id.str_length'))-2018)/config('encrypt_id.base_number');
        }
        return $data;
    }
    /**
     * 将所有的id字段加密处理(array)
     */
    public static function encrypt_id_array($ids){
        $data=[];
        foreach ($ids as $id){
            $data[]=str_random(config('encrypt_id.str_length')).($id*config('encrypt_id.base_number')+2018);
        }
        return $data;
    }
    /**
     * 抽取文件的扩展名
     * @param $filename:文件名
     */
    public static function get_file_extension_name(string $filename){
        if(!str_contains($filename,'.')){
            return false;
        };
        $index=strrpos($filename,'.');//
       return substr($filename,$index+1);
    }
    /**
     *置顶某个---索引数组---指定位置的元素&其余元素顺序下移(可插入重复)
     * @param $array:目标数组
     * @param $inser_data:插入的元素
     */
    public static function top_array_header(array $array,int $key){
        if(!array_key_exists($key,$array)){
            return $array;
        }
        $data[]=$array[$key];
        unset($array[$key]);
        return array_merge($data,$array);
    }
    /**
     * 判断一个json是否为有效
     * @param string $json :目标json
     * @return bool:true代表合法，false 代表不合法
     */
    public static function judge_json_legal(string $json){
        if($json!=null&&$json!='[]'&&$json!='{}'){
            return true;
        }
        return false;
    }
    /**
     * 彻底删除软删除数据(为记录日志,为部分model删除设置为软删除)
     */
    public static function del($table)
    {
        return DB::table($table)->where('deleted_at','!=',null)->delete();
    }
    /**
     * 获取用户在公司的基本信息
     */
    public static function getStaffData(array $user_id,array $company_id)
    {
        return DB::table('user_company_info')->where('user_id',$user_id)->where('company_id',$company_id)->first()->toArray();
    }
    /**
     * 获取首字母
     * @param  string $str 汉字字符串
     * @return string 首字母
     */
    public static function getInitials($str)
    {
        if (empty($str)) {return '';}
        $fchar = ord($str{0});
        if ($fchar >= ord('A') && $fchar <= ord('z')) {
            return strtoupper($str{0});
        }

        $s1  = iconv('UTF-8', 'GBK', $str);
        $s2  = iconv('GBK', 'UTF-8', $s1);
        $s   = $s2 == $str ? $s1 : $str;
        $asc = ord($s{0}) * 256 + ord($s{1}) - 65536;
        if ($asc >= -20319 && $asc <= -20284) {
            return 'A';
        }

        if ($asc >= -20283 && $asc <= -19776) {
            return 'B';
        }

        if ($asc >= -19775 && $asc <= -19219) {
            return 'C';
        }

        if ($asc >= -19218 && $asc <= -18711) {
            return 'D';
        }

        if ($asc >= -18710 && $asc <= -18527) {
            return 'E';
        }

        if ($asc >= -18526 && $asc <= -18240) {
            return 'F';
        }

        if ($asc >= -18239 && $asc <= -17923) {
            return 'G';
        }

        if ($asc >= -17922 && $asc <= -17418) {
            return 'H';
        }

        if ($asc >= -17417 && $asc <= -16475) {
            return 'J';
        }

        if ($asc >= -16474 && $asc <= -16213) {
            return 'K';
        }

        if ($asc >= -16212 && $asc <= -15641) {
            return 'L';
        }

        if ($asc >= -15640 && $asc <= -15166) {
            return 'M';
        }

        if ($asc >= -15165 && $asc <= -14923) {
            return 'N';
        }

        if ($asc >= -14922 && $asc <= -14915) {
            return 'O';
        }

        if ($asc >= -14914 && $asc <= -14631) {
            return 'P';
        }

        if ($asc >= -14630 && $asc <= -14150) {
            return 'Q';
        }

        if ($asc >= -14149 && $asc <= -14091) {
            return 'R';
        }

        if ($asc >= -14090 && $asc <= -13319) {
            return 'S';
        }

        if ($asc >= -13318 && $asc <= -12839) {
            return 'T';
        }

        if ($asc >= -12838 && $asc <= -12557) {
            return 'W';
        }

        if ($asc >= -12556 && $asc <= -11848) {
            return 'X';
        }

        if ($asc >= -11847 && $asc <= -11056) {
            return 'Y';
        }

        if ($asc >= -11055 && $asc <= -10247) {
            return 'Z';
        }

        return 'K';
    }
}