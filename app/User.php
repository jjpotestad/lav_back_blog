<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;


class User extends Authenticatable
{

    use HasRoles;

    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','surname','description', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    /**
     * Validation messages
     *
     * @var array
     */
    public static $messages = [
        'name.required'=> 'El campo nombre es requerido',
        'surname.required'=> 'El campo apellido es requerido',
        'email.required'=> 'El campo email es requerido',
        'email.email'=> 'El campo email no es valido',
        'email.unique'=> 'Ya existe el email en el sistema',
        'password.required'=> 'El campo password es requerido',
        'password.min'=> 'El password debe tener al menos 8 caracteres',
    ];

    /**
     * Register validation rules
     *
     * @var array
     */

    public static $create_rules = [
        'name'=> 'required|string|max:255',
        'surname'=> 'required|string|max:255',
        'description'=> 'string|max:500',
        'email'=> 'required|string|email|unique:users',
        'password'=> 'required|min:3',        
    ];

    /**
     * Update validation rules
     *
     * @var array
     */

    public static $update_rules = [
        'name'=> 'required|string|max:255',
        'surname'=> 'required|string|max:255',
        'description'=> 'string|max:500',
        'password'=> 'min:3',      
    ];

    /**
     * Login validation rules
     *
     * @var array
     */

    public static $login_rules = [
        'email'=> 'required|string|email',
        'password'=> 'required|min:3',        
    ];

    /**
     * Get the posts for the image | One To Many
     */
    public function posts()
    {
        return $this->hasMany('App\Post');
    }


}
