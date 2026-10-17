<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    protected $fillable = ['name', 'student_id', 'balance', 'is_active'];

    public function transactions(): HasMany
    {
        return $this->hasMany(StudentTransaction::class);
    }
}
