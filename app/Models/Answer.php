<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    protected $table = 'question_answers';
    use HasFactory;

    protected $fillable = [
        'content',
        'question_id'
    ];

    public function question()
    {
        return $this->belongsTo(Question::class, 'id', 'question_id');
    }
}
