<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'title', 'content','user_id','category_id','image'
    ];

    /**
     * Validation messages
     *
     * @var array
     */
    public static $messages = [
        'title.required'=> 'El campo title es requerido',
        'content.required'=> 'El campo content es requerido',
    ];

    /**
     * Validation rules
     *
     * @var array
     */

    public static $rules = [
        'title' => 'required|string|max:255',
        'content' => 'required|string|max:500',
        'category_id' => 'required',
    ];

    /**
     * Get the user for the post | Many To One
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    /**
     * Get the category for the post | Many To One
     */
    public function category()
    {
        return $this->belongsTo('App\Category');
    }
}
