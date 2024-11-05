<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'recipe'];


    protected $casts = [
        'recipe' => 'array',
    ];
    public function getDecodedRecipeAttribute()
    {
        return json_decode($this->recipe, true);
    }

    // Define the relationship with the User model
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
