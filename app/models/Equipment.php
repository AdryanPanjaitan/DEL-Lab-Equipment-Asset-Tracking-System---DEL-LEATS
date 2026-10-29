<?php

namespace app\models;

use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    protected $table = 'equipments';

    protected $fillable = [
        'category_id', 'asset_code', 'name', 'specification', 'condition',
        'status', 'daily_rental_fee', 'image_path',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function loanRequests()
    {
        return $this->hasMany(LoanRequest::class);
    }
}