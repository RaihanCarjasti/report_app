<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Task;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        "name",
        "path",
        "file_url",
        "type",
        "status",
        "comment",
        "task_id"
    ];
    
    public function task(){
        return $this->belongsTo(Task::class);
    }
}
