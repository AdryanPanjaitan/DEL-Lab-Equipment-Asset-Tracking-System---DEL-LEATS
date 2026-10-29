<?php

namespace app\models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'code'];

    public function equipments()
    {
        return $this->hasMany(Equipment::class);
    }
}