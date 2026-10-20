<?php

namespace App\Models;

use App\Models\Order;
use App\Services\AccountMailService;

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
        'is_sales_executive',
        'is_active',
    ];

    /** Roles a Manager is allowed to create / edit. */
    public const MANAGER_MANAGEABLE_ROLES = ['Sales Executive'];

    /** Roles an admin may assign from the Users screen. */
    public const ADMIN_MANAGEABLE_ROLES = ['Admin', 'Manager', 'Sales Executive'];

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
            'is_active' => 'boolean',
        ];
    }

    /** Full admin-panel access (Admin / Sub Admin), as opposed to a Manager. */
    public function isAdminLevel(): bool
    {
        return $this->can('admin');
    }

    /** Role names this user may assign to accounts they create or edit. */
    public function manageableRoleNames(): array
    {
        if ($this->isAdminLevel()) {
            return self::ADMIN_MANAGEABLE_ROLES;
        }

        return $this->can('manage_sales') ? self::MANAGER_MANAGEABLE_ROLES : [];
    }

    /** Whether this user may edit / deactivate / delete $other from the Users screen. */
    public function canManageUser(User $other): bool
    {
        if ($other->id === $this->id || $other->hasRole('Super Admin')) {
            return false;
        }

        return $other->getRoleNames()->diff($this->manageableRoleNames())->isEmpty();
    }

    /** Send the reset link through the editable email template + admin SMTP settings. */
    public function sendPasswordResetNotification($token): void
    {
        app(AccountMailService::class)->sendResetLink($this, $token);
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
