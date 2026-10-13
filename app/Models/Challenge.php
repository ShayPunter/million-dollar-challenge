<?php

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Challenge extends Model
{
    use HasFactory;

    protected $fillable = ['target_date', 'unique_id'];

    public function revenues()
    {
        return $this->hasMany(Revenue::class);
    }

    public function getTotalRevenueAttribute()
    {
        return $this->revenues()->sum('amount');
    }
}
