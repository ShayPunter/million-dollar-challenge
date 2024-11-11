<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = ['name'];

    protected $appends = ['amount'];

    protected static function boot()
    {
        parent::boot();

        // This will delete all associated revenues when a project is deleted
        static::deleting(function($project) {
            $project->revenues()->delete();
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function revenues()
    {
        return $this->hasMany(Revenue::class);
    }

    public function getAmountAttribute() {  // Change the method to this
        return $this->revenues()->sum('amount');
    }
}
