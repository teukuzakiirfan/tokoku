<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    private function rules(?User $user = null): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'alpha_dash', 'max:50', Rule::unique('users', 'username')->ignore($user?->id)],
            'password' => [$user ? 'nullable' : 'required', 'string', 'min:4'],
            'role' => ['required', Rule::in(['admin', 'kasir'])],
            'status' => ['required', Rule::in(['aktif', 'nonaktif'])],
            'telepon' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user?->id)],
        ];
    }

    public function index()
    {
        $users = User::withCount('transaksi')->orderByDesc('role')->orderBy('name')->get();

        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.form', ['user' => null]);
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());
        User::create($data);

        return redirect()->route('users.index')->with('ok', 'Akun berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        return view('users.form', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate($this->rules($user));

        if (empty($data['password'])) {
            unset($data['password']);
        }

        // Admin tidak bisa menurunkan / menonaktifkan akunnya sendiri
        if ($user->id === $request->user()->id) {
            $data['role'] = $user->role;
            $data['status'] = $user->status;
        }

        $user->update($data);

        return redirect()->route('users.index')->with('ok', 'Akun diperbarui.');
    }

    public function destroy(Request $request, User $user)
    {
        if ($user->id === $request->user()->id) {
            return back()->withErrors(['hapus' => 'Anda tidak bisa menghapus akun yang sedang dipakai.']);
        }

        $user->delete();

        return redirect()->route('users.index')->with('ok', 'Akun dihapus. Riwayat transaksinya tetap tersimpan.');
    }
}
