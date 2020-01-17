<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PstExportTemplate extends Model
{
    protected $fillable=[
        'name', 'type_id', 'company_id', 'is_show', 'header', 'footer', 'text', 'parameter', 'per','description'
    ];
    protected $table='pst_export_template';
    /**
     * 导出模板与类型的一对多
     */
    public function exportType()
    {
        return $this->belongsTo(PstExportType::class,'type_id');
    }
}
