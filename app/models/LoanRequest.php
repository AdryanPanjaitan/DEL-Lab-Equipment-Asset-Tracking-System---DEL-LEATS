<?php

namespace app\models;

use Illuminate\Database\Eloquent\Model;

class LoanRequest extends Model
{
    protected $fillable = [
        'equipment_id', 'borrower_name', 'borrower_nim', 'borrower_email',
        'loan_date', 'expected_return_date', 'purpose', 'status', 'total_estimated_fee',
    ];

    protected $casts = [
        'loan_date' => 'date',
        'expected_return_date' => 'date',
    ];

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }
}