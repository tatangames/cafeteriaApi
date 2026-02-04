<?php

namespace App\Models;

use App\Notifications\ResetPasswordAdministrador;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Auth\Passwords\CanResetPassword as ResetPasswordTrait;

class Administrador extends Authenticatable
{
    use HasApiTokens, Notifiable, ResetPasswordTrait;
    protected $table = 'administradores'; // Asegúrate de tener el nombre correcto de la tabla

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordAdministrador($token));
    }
}
