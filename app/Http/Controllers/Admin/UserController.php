<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Staff accounts are managed from the Settings page.
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
        ];

        // Only super admins may create another super admin.
        if ($request->user()->isSuperAdmin()) {
            $rules['role'] = ['nullable', 'in:superadmin,staff'];
        }

        $data = $request->validate($rules);

        User::create($data + ['role' => $data['role'] ?? User::ROLE_STAFF]); // password auto-hashed by the cast

        $roleNote = ($data['role'] ?? User::ROLE_STAFF) === User::ROLE_SUPERADMIN
            ? ' with SUPER ADMIN access'
            : '';

        return back()->with('success', "Staff account for {$data['name']} created{$roleNote}.");
    }

    /**
     * Switch a staff member between super admin / staff — super admin only.
     */
    public function updateRole(Request $request, User $user)
    {
        abort_unless($request->user()->isSuperAdmin(), 403, 'Only super admins can change roles.');

        $data = $request->validate([
            'role' => ['required', 'in:superadmin,staff'],
        ]);

        // Never leave the system without a super admin.
        if ($user->isSuperAdmin()
            && $data['role'] === User::ROLE_STAFF
            && User::where('role', User::ROLE_SUPERADMIN)->count() <= 1) {
            return back()->with('error', 'Cannot demote the last super admin.');
        }

        $user->update(['role' => $data['role']]);

        return back()->with(
            'success',
            $data['role'] === User::ROLE_SUPERADMIN
                ? "{$user->name} is now a super admin."
                : "{$user->name} is now a regular staff member."
        );
    }

    public function destroy(Request $request, User $user)
    {
        if ($user->id === $request->user()->id) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        // Never delete the last super admin.
        if ($user->isSuperAdmin() && User::where('role', User::ROLE_SUPERADMIN)->count() <= 1) {
            return back()->with('error', 'Cannot delete the last super admin account.');
        }

        $name = $user->name;
        $user->delete();

        return back()->with('success', "Staff account {$name} removed.");
    }
}
