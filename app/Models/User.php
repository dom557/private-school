<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $fillable = [
        'nom', 'prenom', 'CIN', 'adresse', 'telephone', 'date_naissance', 'sexe', 'email', 'password', 'role', 'filier', 'formateurs_id', 'note1', 'note2',
    ];

    public function formateurOf()
    {
        return $this->hasMany(User::class, 'formateurs_id');
    }

    public function students()
    {
        return $this->belongsTo(User::class, 'formateurs_id');
    }
}
