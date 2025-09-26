<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class expense extends Model
{
    //

    protected $fillable = ['title', 'amount', 'date', 'category', 'user_id'];

public function user()
{
    return $this->belongsTo(User::class);
}

}
