<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SnapAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'type', // should be 'snap'
        'name',
        'number',
        // add other fields as needed
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
