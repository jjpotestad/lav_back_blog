<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';

    protected $fillable = [
        'name'
    ];

    /**
     * Validation messages
     *
     * @var array
     */
    public static $messages = [
        'name.required'=> 'El campo nombre es requerido',
    ];

    /**
     * Validation rules
     *
     * @var array
     */

    public static $rules = [
        'name'=> 'required|string|max:255',
    ];

    /**
     * Get the posts for the image | One To Many
     */
    public function posts()
    {
        return $this->hasMany('App\Post');
    }
}
