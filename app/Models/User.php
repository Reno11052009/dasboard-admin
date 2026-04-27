<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function hasPermission($permission)
    {
        // Fallback jika belum menjalankan migrate
        if (is_string($this->role)) {
            return $this->role === 'super_admin';
        }

        if (!$this->role || !is_array($this->role->permissions)) {
            return false;
        }
        
        $permWithSpace = str_replace('.', ' ', $permission);
        return in_array($permission, $this->role->permissions) || in_array($permWithSpace, $this->role->permissions);
    }

    public function isSuperAdmin()
    {
        // Fallback jika belum menjalankan migrate
        if (is_string($this->role)) {
            return $this->role === 'super_admin';
        }

        return $this->role && $this->role->name === 'Super Admin';
    }
}
