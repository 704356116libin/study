<?php

namespace App\Tools;

use Illuminate\Support\Facades\DB;

class CompanyLogTool
{
    /**
     * @param $data
     * @return bool
     * 写入企业日志
     */
    public static function writeLog($data)
    {
        $terminal_equipment=self::zd_sb();
        $user=array_get($data,'user');
        $log_data=[
            'module_type'=>array_get($data,'module_type'),//模块类型
            'company_id'=>$user->current_company_id,//所在公司
            'terminal_equipment'=>$terminal_equipment,//终端设备(例:web端,pc端)
            'operation_type'=>array_get($data,'operation_type'),//操作类型(审批,公告)
            'operator_id'=>$user->id,//操作人id
            'content'=>array_get($data,'content'),//内容
            'create_time'=>date('Y-m-d H:i:s',time()),//操作时间
        ];
        DB::table('company_operation_log')->insert($log_data);
        return true;
    }
    /**
     * 判断终端设备
     */
    public static function zd_sb()
    {
        $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
        $is_pc=(strpos($agent, 'windows nt')) ? true : false;
        $is_iphone=(strpos($agent, 'iphone')) ? true : false;
        $is_ipad = (strpos($agent, 'ipad')) ? true : false;
        $is_android = (strpos($agent, 'android')) ? true : false;
        //输出数据 
        if ($is_pc) {
            return "PC";
        }
        if ($is_iphone) {
            return "iPhone";
        }
        if ($is_ipad) {
            return "iPad";
        }
        if ($is_android) {
            return "Android";
        }
        return 'other';
    }

}
