<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('roles');

        if ($request->role) {
            $query->role($request->role);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $users = $query->latest()->paginate(15);
        $roles = Role::all();

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'role'     => 'required|exists:roles,name',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole($request->role);

        AuditLogService::log(
            'tambah_user',
            'User',
            $user->id,
            null,
            ['name' => $user->name, 'email' => $user->email, 'role' => $request->role]
        );

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    public function show(User $user)
    {
        $user->load('roles');
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        $adminCount = User::role('admin')->count();
        return view('admin.users.edit', compact('user', 'roles', 'adminCount'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:8|confirmed',
            'role'     => 'required|exists:roles,name',
        ]);

        $roleNames = is_array($request->role) ? $request->role : [$request->role];
        $containsAdmin = in_array('admin', $roleNames);

        // Proteksi 1: Admin yang sedang login tidak boleh mengubah role akunnya sendiri menjadi selain admin
        if ($user->id === auth()->id() && !$containsAdmin) {
            return back()->withInput()->with('error', 'Anda tidak dapat mengubah role akun sendiri menjadi selain admin.');
        }

        // Proteksi 2: Role admin terakhir di sistem tidak boleh dicabut atau diganti
        if ($user->hasRole('admin') && !$containsAdmin && User::role('admin')->count() <= 1) {
            return back()->withInput()->with('error', 'Tidak dapat mencabut atau mengubah role admin terakhir di sistem.');
        }

        $dataLama = [
            'name'  => $user->name,
            'email' => $user->email,
            'role'  => $user->getRoleNames()->first(),
        ];

        $user->update([
            'name'  => $request->name,
            'email' => $request->email,
        ]);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        // Update role
        $user->syncRoles([$request->role]);

        AuditLogService::log(
            'ubah_role',
            'User',
            $user->id,
            $dataLama,
            ['name' => $user->name, 'email' => $user->email, 'role' => $request->role]
        );

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak dapat menghapus akun sendiri.');
        }

        // Proteksi: Akun admin terakhir di sistem tidak boleh dihapus
        if ($user->hasRole('admin') && User::role('admin')->count() <= 1) {
            return back()->with('error', 'Tidak dapat menghapus akun admin terakhir di sistem.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil dihapus.');
    }

    public function assignRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|exists:roles,name',
        ]);

        $roleNames = is_array($request->role) ? $request->role : [$request->role];
        $containsAdmin = in_array('admin', $roleNames);

        // Proteksi 1: Admin yang sedang login tidak boleh mengubah role akunnya sendiri menjadi selain admin
        if ($user->id === auth()->id() && !$containsAdmin) {
            return back()->with('error', 'Anda tidak dapat mengubah role akun sendiri menjadi selain admin.');
        }

        // Proteksi 2: Role admin terakhir di sistem tidak boleh dicabut atau diganti
        if ($user->hasRole('admin') && !$containsAdmin && User::role('admin')->count() <= 1) {
            return back()->with('error', 'Tidak dapat mencabut atau mengubah role admin terakhir di sistem.');
        }

        $roleLama = $user->getRoleNames()->first();
        $user->syncRoles([$request->role]);

        AuditLogService::log(
            'ubah_role',
            'User',
            $user->id,
            ['role' => $roleLama],
            ['role' => $request->role]
        );

        return back()->with('success', 'Role user berhasil diubah.');
    }
}
