<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nom',
        'email',
        'password',
        'telephone',
        'role',
        'avatar',
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

    public function isOwner(): bool {
        return $this->role === 'owner';
    }

    public function isTenant(): bool {
        return $this->role === 'tenant';
    }

    public function updateProfile(array $data): bool {
        return $this->update($data);
    }

    public function properties() {
        return $this->hasMany(Property::class, 'user_id');
    }

    public function favorites() {
        return $this->hasMany(Favorite::class, 'user_id');
    }

    public function messages() {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function reservations() {
        return $this->hasMany(reservation::class, 'tenant_id');
    }
}