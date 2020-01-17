<?php

namespace App\Repositories;

use App\Models\EnterpriseCertificationInfo;
use App\Tools\FunctionTool;
use Carbon\Carbon;

/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/12/9
 * Time: 11:45
 */
class EnterpriseCertificationRepository
{
    private static $enterpriseCertificationRepository;

    /**
     * 构造函数(该类使用单例模式)
     */
    public function __construct()
    {
    }

    /**
     * 实例化自身类(单例模式)
     */
    public static function getEnterpriseCertificationRepository()
    {
        if (self::$enterpriseCertificationRepository instanceof self) {
            return self::$enterpriseCertificationRepository;
        } else {
            return self::$enterpriseCertificationRepository = new self;
        }
    }

    /**
     * 防止被克隆
     */
    private function _clone()
    {
    }

    /**
     * 查询企业认证信息表
     */
    public function findAllInfo()
    {
        return EnterpriseCertificationInfo::all();
    }

    /**
     * 保存企业数据
     */
    public function createCertification($data)
    {
        return EnterpriseCertificationInfo::create($data);
    }
    /**
     * 判断公司是否认证
     */
    public function isCertified($company_id)
    {
        return EnterpriseCertificationInfo::where('company_id',$company_id)->count();
    }
}