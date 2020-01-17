<?php
namespace App\Interfaces;

use Illuminate\Http\Request;

interface InvocieInterface{
    public function getInvoiceList($user_id);
}