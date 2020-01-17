<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CollaborationInvitation extends Model
{
    protected $table = 'collaboration_invitation';
    protected $fillable = [
        'initiate_user','receive_user','status','collaborative_task_id','difference','is_delete','company_id','complete_time','transferred_person','transfer_reason','replace_company_id','type'
    ];

    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function task()
    {
        return $this->belongsTo('App\Models\CollaborativeTask','collaborative_task_id');
    }
}
