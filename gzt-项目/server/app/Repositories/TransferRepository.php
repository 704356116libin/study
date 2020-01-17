<?php
namespace App\Repositories;

use App\Models\Transfer;

class TransferRepository
{
    public function creatTransfer($data)
    {
        return Transfer::create($data);
    }

    public function getTransfer($id)
    {
        return Transfer::find($id);
    }

    /**
     * 获取转账信息列表
     * @param $pagesize
     * @return mixed
     */
    public function transferList($pagesize){
        return Transfer::orderBy('created_at','desc')
            ->paginate($pagesize);
    }

    /**
     * 更新转账信息
     * @param $id
     * @param $data
     * @return mixed
     */
    public function saveTransfer($id,$data)
    {
        return Transfer::where('id',$id)
            ->update($data);
    }

    /**
     * 删除转账记录
     * @param $id
     * @return mixed
     */
    public function deleteTransfer($id)
    {
        return Transfer::where('id',$id)
            ->delete();
    }
}