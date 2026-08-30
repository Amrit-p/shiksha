<?php

namespace App\Models;

use App\Models\Order;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    // use HasApiTokens;

    protected $fillable = [
        'name',
        'email',
        'mobile',
        'father_name',
        'address',
        'city',
        'state',
        'pin_code',
        'password',
        'is_admin',
        'is_sales_executive'
    ];

    /**
     * Orders placed by this user (salesman / shop user on frontend).
     */
    public function placedOrders()
    {
        return $this->hasMany(Order::class, 'user_id');
    }

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

  public function get_roles()
  {
    $roles = [];
    foreach ($this->getRoleNames() as $key => $role) {
      $roles[$key] = $role;
    }

    return $roles;
  }
}
