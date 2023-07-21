<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    public $timestamps = false;
    protected $fillable = ['name'];
    use HasFactory;

    public function cocktails() {
        return $this->belongsToMany(Cocktail::class);
    }

}
