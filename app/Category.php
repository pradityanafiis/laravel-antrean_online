<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';

    protected $primaryKey = 'id';

    protected $fillable = [
        'name'
    ];

    public function merchants()
    {
        return $this->hasMany(Merchant::class, 'category_id', 'id');
    }
}
