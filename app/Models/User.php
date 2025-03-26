<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role'
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



   
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }
 
 
    public function hasRole($role): bool
    {
        return $this->role && $this->role->name === $role;
    }
    
 
    
     public function hasPermission($permission): bool
     {
         return $this->roles()->whereHas('permissions', function ($query) use ($permission) {
             $query->where('name', $permission);
         })->exists();
     }



     public function badges(): BelongsToMany
     {
         return $this->belongsToMany(Badge::class, 'user_badges')
             ->withTimestamps();
     }


     public function courses(): HasMany
     {
         return $this->hasMany(Course::class);
     }


     public function enrollments(): HasMany
     {
         return $this->hasMany(Enrollment::class);
     }
 
}
