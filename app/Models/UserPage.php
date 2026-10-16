<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPage extends Model
{
    use HasFactory;

    protected $fillable = ['slug', 'title', 'description'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
