<?php

namespace App\Repositories;


use App\Models\CollaborationInvitation;
use App\Models\CollaborativeTask;
use App\Models\User;

/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/12/9
 * Time: 11:45
 */
class CollaborativeRepository
{
    static private $collaborativeRepository;

    /**
     * 构造函数(该类使用单例模式)
     * CollaborativeRepository constructor.
     */
    public function __construct()
    {
    }

    /**
     * 实例化自身类(单例模式)
     * @return CollaborativeRepository
     */
    static public function getCollaborativeRepository()
    {
        if (self::$collaborativeRepository instanceof self) {
            return self::$collaborativeRepository;
        } else {
            return self::$collaborativeRepository = new self;
        }
    }

    /**
     * 防止被克隆
     */
    private function _clone()
    {
    }

    /**
     * 查找所有任务,用于查找任务总数
     */
    public function taskTotal($company_id)
    {
        $all = CollaborativeTask::where('is_delete', 0)
            ->where('company_id', $company_id)
            ->orderBy('id', 'desc')
            ->get();
        return $all->filter(function ($all) {
            $id = auth('api')->user()->id;
            if ($all->invitations) {
                return in_array($id, $all->invitations->pluck('receive_user')->toarray()) || ($all->initiate_id == $id) || ($all->principal_id == $id);
            } else {
                return ($all->initiate_id == $id) || ($all->principal_id == $id);
            }
        });
    }

    /**
     * 查找所有撤销的任务,用于查找任务总数
     */
    public function taskRevokedTotal($company_id)
    {
        $all = CollaborativeTask::where('is_delete', 1)
            ->where('company_id', $company_id)
            ->orderBy('id', 'desc')
            ->get();
        return $all->filter(function ($all) {
            $id = auth('api')->user()->id;
            if ($all->invitations) {
                return in_array($id, $all->invitations->pluck('receive_user')->toarray()) || ($all->initiate_id == $id) || ($all->principal_id == $id);
            } else {
                return ($all->initiate_id == $id) || ($all->principal_id == $id);
            }
        });
    }

    /**
     * 所有的任务
     * @return mixed
     */
    public function allTask($company_id)
    {
        return CollaborativeTask::where('company_id', $company_id)
            ->orderBy('id', 'desc')
            ->get();
    }

    /**
     * 所有已撤销的任务
     * @return mixed
     */
    public function allRevokedTask($company_id)
    {
        return CollaborativeTask::where('is_delete', 1)
            ->where('company_id', $company_id)
            ->orderBy('id', 'desc')
            ->get();
    }

    /**
     * 搜索查找所有任务,用于查找任务总数
     */
    public function searchTaskTotal($company_id, $title, $is_delete)
    {
        $all = CollaborativeTask::where('is_delete', $is_delete)
            ->where('company_id', $company_id)
            ->where('title', 'like', '%' . $title . '%')
            ->orderBy('id', 'desc')
            ->get();
        return $all->filter(function ($all) {
            $id = auth('api')->user()->id;
            if ($all->invitations) {
                return in_array($id, $all->invitations->pluck('receive_user')->toarray()) || ($all->initiate_id == $id) || ($all->principal_id == $id);
            } else {
                return ($all->initiate_id == $id) || ($all->principal_id == $id);
            }
        });
    }

    /**
     * 所有的任务
     * @return mixed
     */
    public function searchAllTask($company_id, $title,$user_id)
    {
        if($company_id===0){
            $collaborative_task_id=CollaborationInvitation::where('receive_user',$user_id)->pluck('collaborative_task_id')->toarray();
            return CollaborativeTask::where('title', 'like', '%' . $title . '%')
                ->orderBy('id', 'desc')
                ->get()->filter(function ($task) use ($collaborative_task_id){
                    return (in_array($task->id,array_unique($collaborative_task_id)));
                });
        }
        return CollaborativeTask::where('company_id', $company_id)
            ->where('title', 'like', '%' . $title . '%')
            ->orderBy('id', 'desc')
            ->get();
    }

    /**
     * 我发起全部的任务
     */
    public function myInitiateTask($company_id, $initiate_id)
    {
        return CollaborativeTask::where('is_delete', 0)
            ->where('company_id', $company_id)
            ->where('initiate_id', $initiate_id)
            ->orderBy('id', 'desc')
            ->get();
    }

    /**
     * 我发起已撤销的任务
     */
    public function myInitiateRevokedTask($company_id, $initiate_id)
    {
        return CollaborativeTask::where('is_delete', 1)
            ->where('company_id', $company_id)
            ->where('initiate_id', $initiate_id)
            ->orderBy('id', 'desc')
            ->get();
    }

    /**
     * 我负责全部的任务
     */
    public function myPrincipalTask($company_id, $principal_id)
    {
        return CollaborativeTask::where('is_delete', 0)
            ->where('company_id', $company_id)
            ->where('principal_id', $principal_id)
            ->orderBy('id', 'desc')
            ->get();
    }

    /**
     * 我负责全部的任务
     */
    public function myPrincipalRevokedTask($company_id, $principal_id)
    {
        return CollaborativeTask::where('is_delete', 1)
            ->where('company_id', $company_id)
            ->where('principal_id', $principal_id)
            ->orderBy('id', 'desc')
            ->get();
    }


    /**
     * 所有进行中的任务
     * @return mixed
     */
    public function allTasking($company_id)
    {
        return CollaborativeTask::where('is_delete', 0)
            ->where('company_id', $company_id)
            ->where('is_receive', 1)
            ->orderBy('id', 'desc')
            ->get();
    }

    /**
     * search所有进行中的任务
     * @return mixed
     */
    public function allSearchTasking($company_id, $title,$user_id)
    {
        if($company_id===0){
            $collaborative_task_id=CollaborationInvitation::where('receive_user',$user_id)->pluck('collaborative_task_id')->toarray();
            return CollaborativeTask::where('is_delete', 0)
                ->where('is_receive', 1)
                ->where('title', 'like', '%' . $title . '%')
                ->orderBy('id', 'desc')
                ->get()->filter(function ($task) use ($collaborative_task_id){
                    return (in_array($task->id,array_unique($collaborative_task_id)));
                });
        }
        return CollaborativeTask::where('is_delete', 0)
            ->where('is_receive', 1)
            ->where('company_id', $company_id)
            ->where('title', 'like', '%' . $title . '%')
            ->orderBy('id', 'desc')
            ->get();
    }

    /**
     * 我发起进行中的任务
     */
    public function myInitiateTasking($company_id, $initiate_id)
    {
        return CollaborativeTask::where('is_delete', 0)
            ->where('is_receive', 1)
            ->where('company_id', $company_id)
            ->where('initiate_id', $initiate_id)
            ->orderBy('id', 'desc')
            ->get();
    }

    /**
     * 我负责进行中的任务
     */
    public function myPrincipalTasking($company_id, $principal_id)
    {
        return CollaborativeTask::where('is_delete', 0)
            ->where('is_receive', 1)
            ->where('company_id', $company_id)
            ->where('principal_id', $principal_id)
            ->orderBy('id', 'desc')
            ->get();
    }

    /**
     * 所有待接收任务
     */
    public function allPendingReceptionTasks($company_id)
    {
        return CollaborativeTask::where('is_delete', 0)
            ->where('company_id', $company_id)
            ->where('is_receive', 0)
            ->orderBy('id', 'desc')
            ->get();
    }

    /**
     * search所有待接收任务
     */
    public function allSearchPendingReceptionTasks($company_id, $title,$user_id)
    {
        if($company_id===0){
            $collaborative_task_id=CollaborationInvitation::where('receive_user',$user_id)->pluck('collaborative_task_id')->toarray();
            return CollaborativeTask::where('is_delete', 0)
                ->where('is_receive', 0)
                ->where('title', 'like', '%' . $title . '%')
                ->orderBy('id', 'desc')
                ->get()->filter(function ($task) use ($collaborative_task_id){
                    return (in_array($task->id,array_unique($collaborative_task_id)));
                });
        }
        return CollaborativeTask::where('is_delete', 0)
            ->where('is_receive', 0)
            ->where('company_id', $company_id)
            ->where('title', 'like', '%' . $title . '%')
            ->orderBy('id', 'desc')
            ->get();
    }

    /**
     * 我发起待接收任务
     */
    public function myInitiatePendingReceptionTasks($company_id, $initiate_id)
    {
        return CollaborativeTask::where('is_delete', 0)
            ->where('is_receive', 0)
            ->where('company_id', $company_id)
            ->where('initiate_id', $initiate_id)
            ->orderBy('id', 'desc')
            ->get();
    }

    /**
     * 我负责待接收任务
     */
    public function myPrincipalPendingReceptionTasks($company_id, $principal_id)
    {
        return CollaborativeTask::where('is_delete', 0)
            ->where('is_receive', 0)
            ->where('company_id', $company_id)
            ->where('principal_id', $principal_id)
            ->orderBy('id', 'desc')
            ->get();
    }

    /**
     * 所有待审核的任务
     */
    public function allPendingReviewTasks($company_id)
    {
        return CollaborativeTask::where('is_delete', 0)
            ->where('is_receive', 3)
            ->where('company_id', $company_id)
            ->where('status', 0)
            ->orderBy('id', 'desc')
            ->get();
    }

    /**
     * search所有待审核的任务
     */
    public function allSearchPendingReviewTasks($company_id, $title,$user_id)
    {
        if($company_id===0){
            $collaborative_task_id=CollaborationInvitation::where('receive_user',$user_id)->pluck('collaborative_task_id')->toarray();
            return CollaborativeTask::where('is_delete', 0)
                ->where('is_receive', 3)
                ->where('title', 'like', '%' . $title . '%')
                ->where('status', 0)
                ->orderBy('id', 'desc')
                ->get()->filter(function ($task) use ($collaborative_task_id){
                    return (in_array($task->id,array_unique($collaborative_task_id)));
                });
        }
        return CollaborativeTask::where('is_delete', 0)
            ->where('is_receive', 3)
            ->where('company_id', $company_id)
            ->where('title', 'like', '%' . $title . '%')
            ->where('status', 0)
            ->orderBy('id', 'desc')
            ->get();
    }

    /**
     * 我发起待审核任务
     */
    public function myInitiatePendingReviewTasks($company_id, $initiate_id)
    {
        return CollaborativeTask::where('is_delete', 0)
            ->where('initiate_id', $initiate_id)
            ->where('company_id', $company_id)
            ->where('is_receive', 3)
            ->where('status', 0)
            ->orderBy('id', 'desc')
            ->get();
    }

    /**
     * 我负责待审核任务
     */
    public function myPrincipalPendingReviewTasks($company_id, $principal_id)
    {
        return CollaborativeTask::where('is_delete', 0)
            ->where('principal_id', $principal_id)
            ->where('is_receive', 1)
            ->where('company_id', $company_id)
            ->where('status', 0)
            ->orderBy('id', 'desc')
            ->get();
    }

    /**
     * 所有已拒绝的任务
     */
    public function allRejectedTask($company_id)
    {
        return CollaborativeTask::where('is_delete', 0)
            ->where('is_receive', 2)
            ->where('company_id', $company_id)
            ->orderBy('id', 'desc')
            ->get();
    }

    /**
     * search所有已拒绝的任务
     */
    public function allSearchRejectedTask($company_id, $title,$user_id)
    {
        if($company_id===0){
            $collaborative_task_id=CollaborationInvitation::where('receive_user',$user_id)->pluck('collaborative_task_id')->toarray();
            return CollaborativeTask::where('is_delete', 0)
                ->where('is_receive', 2)
                ->where('title', 'like', '%' . $title . '%')
                ->orderBy('id', 'desc')
                ->get()->filter(function ($task) use ($collaborative_task_id){
                    return (in_array($task->id,array_unique($collaborative_task_id)));
                });
        }
        return CollaborativeTask::where('is_delete', 0)
            ->where('is_receive', 2)
            ->where('company_id', $company_id)
            ->where('title', 'like', '%' . $title . '%')
            ->orderBy('id', 'desc')
            ->get();
    }

    /**
     * 我发起已拒绝的任务
     */
    public function myInitiateRejectedTask($company_id, $initiate_id)
    {
        return CollaborativeTask::where('is_delete', 0)
            ->where('initiate_id', $initiate_id)
            ->where('company_id', $company_id)
            ->where('is_receive', 2)
            ->where('status', 0)
            ->orderBy('id', 'desc')
            ->get();
    }

    /**
     * 我负责已拒绝的任务
     */
    public function myPrincipalRejectedTask($company_id, $principal_id)
    {
        return CollaborativeTask::where('is_delete', 0)
            ->where('principal_id', $principal_id)
            ->where('company_id', $company_id)
            ->where('is_receive', 2)
            ->where('status', 0)
            ->orderBy('id', 'desc')
            ->get();
    }

    /**
     * 所有已完成的任务
     */
    public function allCompletedTask($company_id)
    {
        return CollaborativeTask::where('is_delete', 0)
            ->where(function ($query) {
                $query->where('is_receive', 3)
                    ->Where('status', 1);
            })
            ->where('company_id', $company_id)
            ->orderBy('id', 'desc')
            ->get();
    }

    /**
     * search所有已完成的任务
     */
    public function allSearchevokedTask($company_id, $title,$user_id)
    {
        if($company_id===0){
            $collaborative_task_id=CollaborationInvitation::where('receive_user',$user_id)->pluck('collaborative_task_id')->toarray();
            return CollaborativeTask::where('is_delete', 1)
                ->where('title', 'like', '%' . $title . '%')
                ->orderBy('id', 'desc')
                ->get()->filter(function ($task) use ($collaborative_task_id){
                    return (in_array($task->id,array_unique($collaborative_task_id)));
                });
        }
        return CollaborativeTask::where('is_delete', 1)
            ->where('title', 'like', '%' . $title . '%')
            ->where('company_id', $company_id)
            ->orderBy('id', 'desc')
            ->get();
    }

    /**
     * search所有撤销的任务
     */
    public function allSearchCompletedTask($company_id, $title,$user_id)
    {
        if($company_id===0){
            $collaborative_task_id=CollaborationInvitation::where('receive_user',$user_id)->pluck('collaborative_task_id')->toarray();
            return CollaborativeTask::where('is_delete', 0)
                ->where('title', 'like', '%' . $title . '%')
                ->where(function ($query) {
                    $query->where('is_receive', 3)
                        ->orWhere('status', 1);
                })
                ->orderBy('id', 'desc')
                ->get()->filter(function ($task) use ($collaborative_task_id){
                    return (in_array($task->id,array_unique($collaborative_task_id)));
                });
        }
        return CollaborativeTask::where('is_delete', 0)
            ->where('title', 'like', '%' . $title . '%')
            ->where(function ($query) {
                $query->where('is_receive', 3)
                    ->orWhere('status', 1);
            })
            ->where('company_id', $company_id)
            ->orderBy('id', 'desc')
            ->get();
    }

    /**
     * 我发起已完成的任务
     */
    public function myInitiatecompletedTask($company_id, $initiate_id)
    {
        return CollaborativeTask::where('is_delete', 0)
            ->where('initiate_id', $initiate_id)
            ->where('status', 1)
            ->where('company_id', $company_id)
            ->orderBy('id', 'desc')
            ->get();
    }

    /**
     * 我负责已完成的任务
     */
    public function myPrincipalcompletedTask($company_id, $principal_id)
    {
        return CollaborativeTask::where('is_delete', 0)
            ->where('principal_id', $principal_id)
            ->where('is_receive', 3)
            ->where('company_id', $company_id)
            ->orderBy('id', 'desc')
            ->get();
    }

    /**
     * @param $id
     * @return mixed
     * 根据id判断该任务是完成还是未完成状态
     */
    public function judgeStatusOfTask($id)
    {
        return CollaborativeTask::where('id', $id)->value('status');
    }

    /**
     * 发起人确认任务完成
     * @param $id
     * @return mixed
     */
    public function endTask($id)
    {
        return CollaborativeTask::where('id', $id)->update(['status' => 1, 'updated_at' => date('Y-m-d H:i:s', time())]);
    }

    /**
     * 负责人删除协作任务
     * @param $id
     * @return mixed
     */
    public function principalDeleteTask($id)
    {
        return CollaborativeTask::where('id', $id)->delete();
    }

    /**
     * 被邀请者拒绝协助
     */
    public function refuseTask($id)
    {
        return CollaborationInvitation::where('id', $id)->update(['status' => 0, 'updated_at' => date('Y-m-d H:i:s', time())]);
    }

    /**
     * 被邀请者接受协助任务
     */
    public function acceptTask($id)
    {
        return CollaborationInvitation::where('id', $id)->update(['status' => 1, 'updated_at' => date('Y-m-d H:i:s', time())]);
    }

    /**
     * 被邀请者点击完成协助任务
     */
    public function endCollaborationTask($id)
    {
        return CollaborationInvitation::where('id', $id)->update(['status' => 2, 'updated_at' => date('Y-m-d H:i:s', time())]);
    }

    /**
     * 根据用户id,获取用户正在处理中的协作任务
     */
    public function findTasking($company_id, $receive_user)
    {
        return CollaborationInvitation::where('receive_user', $receive_user)
            ->where('status', 1)
            ->where('company_id', $company_id)
            ->where('is_delete', 0)
            ->get();
    }

    /**
     * 根据用户id,获取用户正在处理中的协作任务
     */
    public function findTask($company_id, $receive_user)
    {
        return CollaborationInvitation::where('receive_user', $receive_user)
            ->where('company_id', $company_id)
            ->where('is_delete', 0)
            ->get();
    }

    /**
     * 根据用户id,获取用户已经处理完成协作任务
     */
    public function findTasked($company_id, $receive_user)
    {
        return CollaborationInvitation::where('receive_user', $receive_user)
            ->where('status', 2)
            ->where('company_id', $company_id)
            ->get();
    }

    /**
     * 根据用户id,获取用户已经拒绝协作任务
     */
    public function findRefuseTasked($company_id, $receive_user)
    {
        return CollaborationInvitation::where('receive_user', $receive_user)
            ->where('status', 0)
            ->where('company_id', $company_id)
            ->get();
    }

    /**
     * 根据用户id,获取用户未处理的协作任务(是指被邀请协作后,既没接受,也没拒绝的任务)
     */
    public function findUnprocessedTask($company_id, $receive_user)
    {
        return CollaborationInvitation::where('receive_user', $receive_user)
            ->where('status', 3)
            ->where('company_id', $company_id)
            ->where('is_delete', 0)
            ->get();
    }

    /**
     * 用户删除接受到的协作任务(单个形式删除)
     */
    public function acceptDeleteTask($id, $user_id)
    {
        return CollaborationInvitation::where('initiate_id', $user_id)->where('id', $id)->delete();
    }

    /**
     * 根据任务id,删除collaboration_invitation表邀请记录
     * @param $task_id
     * @return mixed
     */
    public function deleteByTaskId($task_id)
    {
        return CollaborationInvitation::where('collaborative_task_id', $task_id)->delete();
    }


    /**
     * 点击接收按钮
     */
    public function receiveButton($id, $identy, $user_id)
    {
        $identity = self::identity(CollaborativeTask::find($id));
        if ($identy == '负责人' && $identity['负责人'] == true) {
            if ($identity['参与人'] == true) {
                CollaborationInvitation::where('collaborative_task_id', $id)
                    ->where('receive_user', $user_id)
                    ->update(['status' => 1]);
            }
            return CollaborativeTask::where('id', $id)->update(['is_receive' => 1]);
        } elseif ($identy == '参与人' && $identity['参与人'] == true) {
            return CollaborationInvitation::where('collaborative_task_id', $id)
                ->where('receive_user', $user_id)
                ->update(['status' => 1]);
        } else {
            return false;
        }
    }

    /**
     * 点击拒绝按钮
     */
    public function rejectButton($id, $identy, $user_id)
    {
        $identity = self::identity(CollaborativeTask::find($id));
        if ($identy == '负责人' && $identity['负责人'] == true) {
            if ($identity['参与人'] == true) {
                CollaborationInvitation::where('collaborative_task_id', $id)
                    ->where('receive_user', $user_id)
                    ->update(['status' => 0]);
            }
            return CollaborativeTask::where('id', $id)->update(['is_receive' => 2]);
        } elseif ($identy == '参与人' && $identity['参与人'] == true) {
            return CollaborationInvitation::where('collaborative_task_id', $id)
                ->where('receive_user', $user_id)
                ->update(['status' => 0]);
        } else {
            return false;
        }
    }

    /**
     * 点击完成按钮
     */
    public function carryOutButton($id, $identy, $user_id, $opinion)
    {
        $identity = self::identity(CollaborativeTask::find($id));
        if ($identy == '发起人' && $identity['发起人'] == true) {
            if ($identity['负责人'] == true) {
                CollaborativeTask::where('id', $id)->update(['is_receive' => 3,'complete_time'=>date('Y-m-d H:i:s', time())]);
            }
            if ($identity['参与人'] == true) {
                CollaborationInvitation::where('collaborative_task_id', $id)
                    ->where('receive_user', $user_id)
                    ->update(['status' => 2,'complete_time'=>date('Y-m-d H:i:s', time())]);
            }
            return CollaborativeTask::where('id', $id)->update(['status' => 1, 'initiate_opinion' => $opinion,'review_time'=>date('Y-m-d H:i:s', time())]);
        } elseif ($identy == '负责人' && $identity['负责人'] == true) {
            if ($identity['参与人'] == true) {
                CollaborationInvitation::where('collaborative_task_id', $id)
                    ->where('receive_user', $user_id)
                    ->update(['status' => 2,'complete_time'=>date('Y-m-d H:i:s', time())]);
            }
            return CollaborativeTask::where('id', $id)->update(['is_receive' => 3, 'principal_opinion' => $opinion,'complete_time'=>date('Y-m-d H:i:s', time())]);
        } elseif ($identy == '参与人' && $identity['参与人'] == true) {
            return CollaborationInvitation::where('collaborative_task_id', $id)
                ->where('receive_user', $user_id)
                ->update(['status' => 2,'complete_time'=>date('Y-m-d H:i:s', time())]);
        } else {
            return false;
        }
    }

    /**
     * 发起人审核
     */
    public function auditButton($id, $is_agree, $opinion)
    {
        $identity = self::identity(CollaborativeTask::find($id));
        if ($is_agree == 1 && $identity['发起人'] == true) {
            if ($identity['负责人'] == true) {
                CollaborativeTask::where('id', $id)->update(['is_receive' => 3,'complete_time'=>date('Y-m-d H:i:s', time())]);
            }
            if ($identity['参与人'] == true) {
                CollaborationInvitation::where('collaborative_task_id', $id)
                    ->where('receive_user', auth('api')->user()->id)
                    ->update(['status' => 2,'complete_time'=>date('Y-m-d H:i:s', time())]);
            }
            return CollaborativeTask::where('id', $id)->update(['status' => 1, 'initiate_opinion' => $opinion, 'review_time' => date('Y-m-d H:i:s', time())]);//任务完成同时更新完成时间
        } elseif ($is_agree == 0 && $identity['发起人'] == true) {
            return CollaborativeTask::where('id', $id)->update(['is_receive' => 1, 'initiate_opinion' => $opinion]);//审核不通过,
        } else {
            return false;
        }
    }

    /**
     * 发起人撤销任务
     */
    public function cancel($id, $user_id, $initiate_opinion)
    {
        return CollaborativeTask::where('id', $id)->where('initiate_id', $user_id)->update(['is_delete' => 1,'initiate_opinion'=>$initiate_opinion]);//审核不通过,
    }

    /**
     * 恢复任务
     */
    public function recoveryTask($id, $user_id)
    {
        return CollaborativeTask::where('id', $id)->where('initiate_id', $user_id)->update(['is_delete' => 0]);//审核不通过,
    }

    /**
     * 任务详情资源调用方法
     * @param $ids
     * @return array
     */
    static public function users($ids, $judge, $identy, $opinion,$time)
    {
        $data = [];
        foreach ($ids as $id) {
            if ($identy == '发起者') {
                if ($judge == 0) {
                    $status = '待审核';
                } else {
                    $status = '已完成';
                }
            } elseif ($identy == '负责人') {
                if ($judge == 0) {
                    $status = '待接收';
                } elseif ($judge == 1) {
                    $status = '进行中';
                } elseif ($judge == 2) {
                    $status = '已拒绝';
                } elseif ($judge == 3) {
                    $status = '已完成';
                }
            } else {
                $CollaborationInvitation = CollaborationInvitation::where('collaborative_task_id', $judge)
                    ->where('receive_user', $id)
                    ->first();
                $status=$CollaborationInvitation->status;
                $time=$CollaborationInvitation->complete_time;
                if ($status == 0) {
                    $status = '已拒绝';
                } elseif ($status == 1) {
                    $status = '进行中';
                } elseif ($status == 2) {
                    $status = '已完成';
                } elseif ($status == 3) {
                    $status = '待接收';
                }
            }
            $data[] = ['id' => User::find($id)->id, 'name' => User::find($id)->name, 'status' => $status, 'opinion' => $opinion,'complete_time'=>$time];
        }
        return $data;
    }
    /**
     * 我的类型
     */
    public static function my_type($collaborative_task_id)
    {
        $id=auth('api')->id();
        return CollaborationInvitation::where('collaborative_task_id',$collaborative_task_id)->where('receive_user',$id)->value('type');
    }

    /**
     * 任务状态
     * @param $ids
     * @return array
     */
    static public function s($task)
    {
        $user = auth('api')->user();
        switch ($user->id) {
            case $task->initiate_id:
                if ($task->status == 0 && $task->is_receive == 3) {
                    return ['status' => '待审核', 'identity' => '发起者'];
                } elseif ($task->is_receive == 0) {
                    return ['status' => '待接收', 'identity' => '发起者'];
                } elseif ($task->is_receive == 1) {
                    return ['status' => '进行中', 'identity' => '发起者'];
                } elseif ($task->is_receive == 2) {
                    return ['status' => '已拒绝', 'identity' => '发起者'];
                } elseif ($task->status == 1) {
                    return ['status' => '已完成', 'identity' => '发起者'];
                }
                break;
            case $task->principal_id:
                if ($task->is_receive == 0) {
                    return ['status' => '待接收', 'identity' => '负责人'];
                } elseif ($task->is_receive == 1) {
                    return ['status' => '进行中', 'identity' => '负责人'];
                } elseif ($task->is_receive == 2) {
                    return ['status' => '已拒绝', 'identity' => '负责人'];
                } elseif ($task->is_receive == 3) {
                    return ['status' => '已完成', 'identity' => '负责人'];
                }
                break;
            default:
                $status = CollaborationInvitation::where('receive_user', $user->id)
                    ->where('collaborative_task_id', $task->id)
                    ->value('status');
                if ($status == 0) {
                    return ['status' => '已拒绝', 'identity' => '参与者'];
                } elseif ($status == 1) {
                    return ['status' => '进行中', 'identity' => '参与者'];
                } elseif ($status == 2) {
                    return ['status' => '已完成', 'identity' => '参与者'];
                } elseif ($status == 3) {
                    return ['status' => '待接收', 'identity' => '参与者'];
                }

                break;
        }
    }

    static public function identity($task)
    {
        $initiate_id = $task->initiate_id; //邀请人
        $principal_id = $task->principal_id; //负责人
        $user_id = auth('api')->user()->id;
        $array = $task->invitations->pluck('receive_user')->toArray();
        $a = $initiate_id == $user_id ? true : false;
        $b = $principal_id == $user_id ? true : false;
        $c = in_array($user_id, $array) ? true : false;
        return ['发起人' => $a, '负责人' => $b, '参与人' => $c];
    }

    /**
     * 任务列表左侧5个状态
     * @param $task
     * @return string
     */
    static public function left_s($task)
    {
        if ($task->status == 1) {
            return '已完成';
        } else {
            if ($task->is_receive == 0) {
                return '待接收';
            } elseif ($task->is_receive == 1) {
                return '进行中';
            } elseif ($task->is_receive == 2) {
                return '已拒绝';
            } elseif ($task->is_receive == 3) {
                return '待审核';
            } else {
                return '';
            }
        }

    }

    /**
     * 判断用户是否可以编辑协作任务中的表单
     * @param $data
     * @return int
     */
    static public function edit_form($data)
    {
        $user_id = auth('api')->user()->id;
        if ($data[0] == 0) {//都可以编辑
            if (in_array($user_id, $data[1]) || in_array($user_id, $data[2])) {
                return 1;
            } else {
                return 0;
            }
        } elseif ($data[0] == 1) {//仅负责人和发起者可编辑
            if (in_array($user_id, $data[1])) {
                return 1;
            } else {
                return 0;
            }
        } elseif ($data[0] == 2) {//仅协助者可编辑,
            if (in_array($user_id, $data[2])) {
                return 1;
            } else {
                return 0;
            }
        } else {//都不能编辑
            return 0;
        }
    }

}