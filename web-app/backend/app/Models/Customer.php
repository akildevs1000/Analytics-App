<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $appends = ["profile_picture_name"];


    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function branch()
    {
        return $this->belongsTo(CompanyBranch::class);
    }

    public function logs()
    {
        return $this->hasMany(AttendanceLog::class, "UserID", "system_user_id");
    }

    public function recent_log()
    {
        return $this->hasOne(AttendanceLog::class, "UserID", "system_user_id")->orderBy('created_at', 'desc');
    }

    public function getProfilePictureAttribute($value)
    {
        if (!$value) return null;
        return asset('customer/profile_picture/' . $value);
    }

    public function getProfilePictureNameAttribute()
    {
        return explode("customer/profile_picture/", $this->profile_picture)[1] ?? null;
    }

    public function getTypeAttribute($value)
    {
        return strtoupper($value);
    }
}
