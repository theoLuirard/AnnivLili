<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    public function users()
    {
        $query = User::query();

        if (request()->has('search') && request('search') != '') {
            $search = request('search');
            $query->where('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%")
                  ->orWhere('nickname', 'like', "%$search%");
        }

        $users = $query->paginate(10);

        return view('admin.users', ['users' => $users]);
    }

    public function showUser($id)
    {
        $user = User::findOrFail($id);
        return view('admin.user-detail', ['user' => $user]);
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'nickname' => 'nullable|string|max:255',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        if ($request->hasFile('profile_picture')) {
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }
            $validated['profile_picture'] = $request->file('profile_picture')->store('profiles', 'public');
        }

        $user->update($validated);

        return redirect()->route('admin.user.show', $user->id)->with('success', 'User updated successfully!');
    }

    public function toggleRole($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return redirect()->route('admin.user.show', $user->id)->with('error', 'You cannot change your own role.');
        }

        if ($user->hasRole('admin')) {
            $user->syncRoles(['user']);
            $message = 'User role changed to User.';
        } else {
            $user->syncRoles(['admin']);
            $message = 'User role changed to Admin.';
        }

        return redirect()->route('admin.user.show', $user->id)->with('success', $message);
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        
        if ($user->profile_picture) {
            Storage::disk('public')->delete($user->profile_picture);
        }
        
        $user->delete();

        return redirect()->route('admin.users')->with('success', 'User deleted successfully!');
    }
}
