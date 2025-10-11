<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class expense extends Model
{
    //

   protected $fillable = [
    'title',
    'amount', 
    'date',
    'category',
    'user_id',
    'category_id'
];
public function user()
{
    return $this->belongsTo(User::class);
}

    // single category
public function categoryRelation()
{
    return $this->belongsTo(Category::class, 'category_id');
}


// tags many-to-many
  public function tags()
    {
        return $this->belongsToMany(Tag::class, 'expense_tag', 'expense_id', 'tag_id');
    }

}
