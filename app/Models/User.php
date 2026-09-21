<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Mail\ResetPasswordMail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\URL;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
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
        'role',
    ];

    public const ROLE_SUPERADMIN = 'superadmin';
    public const ROLE_STAFF = 'staff';

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

    /**
     * Super admins can delete leads and manage staff roles.
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === self::ROLE_SUPERADMIN;
    }

    /**
     * Send the beautiful branded HTML reset e-mail instead
     * of the default plain Laravel notification.
     */
    public function sendPasswordResetNotification($token): void
    {
        \Mail::to($this->email)->send(new ResetPasswordMail($this, $token, static::resetUrl($token, $this->email)));
    }

    /**
     * Signed-ish reset URL valid for 60 minutes.
     */
    public static function resetUrl(string $token, ?string $email = null): string
    {
        $email = $email ?? request()->input('email');

        return URL::temporarySignedRoute('password.reset', now()->addMinutes(60), [
            'token' => $token,
            'email' => $email,
        ]);
    }
}
