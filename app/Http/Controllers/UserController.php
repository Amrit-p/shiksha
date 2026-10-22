<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AccountMailService;
use Auth;
use DataTables;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

/**
 * Users screen. Admins manage Admin / Manager / Sales Executive accounts;
 * Managers only see and manage Sales Executives (see User::manageableRoleNames()).
 */
class UserController extends Controller
{
  /**
   * Show the users dashboard.
   *
   * @return \Illuminate\Contracts\Support\Renderable
   */
  public function index() //: View
  {
    return view('admin.users');
  }

  /**
   * Show User List
   *
   * @param Request $request
   * @return mixed
   */
  public function getUserList(Request $request): mixed
  {
    $actor = Auth::user();
    $query = User::query()->with('roles');

    if (! $actor->isAdminLevel()) {
      $query->role(User::MANAGER_MANAGEABLE_ROLES);
    }

    $data = $query->get();

    return Datatables::of($data)
      ->addIndexColumn()
      ->addColumn('roles', function ($data) {
        $badge = implode(' , ', $data->getRoleNames()->toArray());
        if ($data->is_active === false) {
          $badge .= ' <span class="badge badge-danger">Inactive</span>';
        }
        return $badge;
      })

      ->addColumn('permissions', function ($data) {
        $badges = '';
        foreach ($data->getAllPermissions() as $permission) {
          $badges .= '<span class="badge badge-dark m-1">' . e($permission->name) . '</span>';
        }

        return $badges;
      })

      ->addColumn('action', function ($data) use ($actor) {
        if (! $actor->canManageUser($data)) {
          return '';
        }

        $csrf = csrf_field();
        $toggleLabel = $data->is_active === false ? 'Activate' : 'Deactivate';
        $toggleIcon = $data->is_active === false ? 'ik-check-circle text-green' : 'ik-slash text-orange';

        return '<div class="table-actions">
                    <a href="' . url('user/' . $data->id) . '" title="Edit"><i class="ik ik-edit-2 f-16 mr-15 text-green"></i></a>
                    <form method="POST" action="' . route('users.toggle_active', $data->id) . '" class="d-inline" onsubmit="return confirm(\'' . $toggleLabel . ' this user?\')">' . $csrf . '
                        <button type="submit" class="btn btn-link p-0 mr-15" title="' . $toggleLabel . '"><i class="ik ' . $toggleIcon . ' f-16"></i></button>
                    </form>
                    <form method="POST" action="' . route('users.resend_invite', $data->id) . '" class="d-inline">' . $csrf . '
                        <button type="submit" class="btn btn-link p-0 mr-15" title="Resend set-password email"><i class="ik ik-mail f-16 text-blue"></i></button>
                    </form>
                    <a href="' . url('user/delete/' . $data->id) . '" title="Delete" onclick="return confirm(\'Delete this user?\')"><i class="ik ik-trash-2 f-16 text-red"></i></a>
                </div>';
      })
      ->rawColumns(['roles', 'permissions', 'action'])
      ->make(true);
  }

  /**
   * User Create
   *
   * @return mixed
   */
  public function create(): mixed
  {
    $roles = $this->assignableRoles();

    return view('admin.create-user', compact('roles'));
  }

  /**
   * Store User. No password is typed: the user gets an email link to set their own.
   *
   * @param Request $request
   * @return \Illuminate\Http\RedirectResponse
   */
  public function store(Request $request, AccountMailService $mailer): RedirectResponse
  {
    $roles = $this->assignableRoles();

    $validator = Validator::make($request->all(), [
      'name' => 'required|string|max:255',
      'email' => 'required|email|max:255|unique:users,email',
      'mobile' => 'required|string|max:50',
      'role' => ['required', 'integer', 'in:' . $roles->keys()->implode(',')],
    ]);
    if ($validator->fails()) {
      return redirect()->back()->withErrors($validator)->withInput();
    }

    $roleName = $roles[(int) $request->role];

    $user = User::create([
      'name' => $request->name,
      'email' => $request->email,
      'mobile' => $request->mobile,
      'father_name' => $request->father_name,
      'city' => $request->city,
      'address' => $request->address,
      'state' => $request->state,
      'pin_code' => $request->pin_code,
      // Unusable until the user sets their own password from the invite email.
      'password' => Str::random(40),
      'is_sales_executive' => $roleName === 'Sales Executive' ? 1 : 0,
      'is_admin' => in_array($roleName, ['Admin', 'Manager'], true) ? 1 : 0,
      'is_active' => true,
    ]);
    $user->syncRoles([$roleName]);

    if ($mailer->sendInvite($user)) {
      return redirect('users')->with('success', "User created. A set-password email has been sent to {$user->email}.");
    }

    return redirect('users')->with('error', 'User created, but the set-password email could not be sent. Check SMTP settings, then use "Resend" on the users list.');
  }

  /**
   * Edit User
   *
   * @param int $id
   * @return mixed
   */
  public function edit($id): mixed
  {
    $user = User::with('roles', 'permissions')->find($id);
    if (! $user || ! Auth::user()->canManageUser($user)) {
      return redirect('users')->with('error', 'You cannot edit this user.');
    }

    $user_role = $user->roles->first();
    $roles = $this->assignableRoles();

    return view('admin.user-edit', compact('user', 'user_role', 'roles'));
  }

  /**
   * Update User
   *
   * @param Request $request
   * @return \Illuminate\Http\RedirectResponse
   */
  public function update(Request $request): RedirectResponse
  {
    $roles = $this->assignableRoles();

    $validator = Validator::make($request->all(), [
      'id' => 'required|integer',
      'name' => 'required|string|max:255',
      'email' => 'required|email|unique:users,email,' . $request->id,
      'role' => ['required', 'integer', 'in:' . $roles->keys()->implode(',')],
      'password' => 'nullable|confirmed|min:8',
    ]);
    if ($validator->fails()) {
      return redirect()->back()->withInput()->with('error', $validator->messages()->first());
    }

    $user = User::find($request->id);
    if (! $user || ! Auth::user()->canManageUser($user)) {
      return redirect('users')->with('error', 'You cannot edit this user.');
    }

    $roleName = $roles[(int) $request->role];
    $payload = [
      'name' => $request->name,
      'email' => $request->email,
      'mobile' => $request->mobile ?? $user->mobile,
      'father_name' => $request->father_name,
      'city' => $request->city,
      'address' => $request->address,
      'state' => $request->state,
      'pin_code' => $request->pin_code,
      'is_sales_executive' => $roleName === 'Sales Executive' ? 1 : 0,
    ];
    if ($request->filled('password')) {
      $payload['password'] = $request->password;
    }

    $user->update($payload);
    $user->syncRoles([$roleName]);

    return redirect()->back()->with('success', 'User information updated succesfully!');
  }

  public function toggleActive($id): RedirectResponse
  {
    $user = User::find($id);
    if (! $user || ! Auth::user()->canManageUser($user)) {
      return redirect('users')->with('error', 'You cannot change this user.');
    }

    $user->update(['is_active' => $user->is_active === false]);

    return redirect('users')->with('success', $user->is_active ? 'User activated.' : 'User deactivated. They can no longer sign in.');
  }

  public function resendInvite($id, AccountMailService $mailer): RedirectResponse
  {
    $user = User::find($id);
    if (! $user || ! Auth::user()->canManageUser($user)) {
      return redirect('users')->with('error', 'You cannot change this user.');
    }

    return $mailer->sendInvite($user)
      ? redirect('users')->with('success', "Set-password email sent to {$user->email}.")
      : redirect('users')->with('error', 'Email could not be sent. Check SMTP settings.');
  }

  /**
   * Delete User
   *
   * @param int $id
   * @return \Illuminate\Http\RedirectResponse
   */
  public function delete($id): RedirectResponse
  {
    $user = User::find($id);
    if (! $user) {
      return redirect('users')->with('error', 'User not found');
    }
    if (! Auth::user()->canManageUser($user)) {
      return redirect('users')->with('error', 'You cannot delete this user.');
    }

    $user->delete();

    return redirect('users')->with('success', 'User removed!');
  }

  public function clearCache(): View
  {
    Artisan::call('cache:clear');

    return view('admin.clear-cache');
  }

  /** Roles the current user may assign, as [id => name]. */
  protected function assignableRoles()
  {
    return Role::whereIn('name', Auth::user()->manageableRoleNames())->pluck('name', 'id');
  }
}
