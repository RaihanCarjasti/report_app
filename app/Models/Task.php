<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Document;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        "name",
        "passed"
    ];

    public function documents(){
        return $this->hasMany(Document::class,"task_id");
    }
}
