<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerReport extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function in_log()
    {
        return $this->belongsTo(AttendanceLog::class, "in_id")->with("device");
    }

    public function out_log()
    {
        return $this->belongsTo(AttendanceLog::class, "out_id")->with("device");
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'user_id', 'system_user_id')->with("branch");
    }
}
