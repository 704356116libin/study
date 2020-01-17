<?php

namespace App\Http\Resources\user;

use App\Tools\FunctionTool;
use Illuminate\Http\Resources\Json\JsonResource;

class UserDetailResource extends JsonResource
{
    public static $company_id;
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'type'=>'user',
            'id'=>FunctionTool::encrypt_id($this->id),
            'name'=>$this->name,
            'email'=>$this->email,
            'email_verified'=>$this->email_verified,
            'tel'=>$this->tel,
            'tel_verified'=>$this->tel_verified,
        ];
    }
}
