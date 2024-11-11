<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AdminUserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return inertia('Users/Users', ['users' => $users]);
    }

    public function create()
    {
        return inertia('Users/Create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'role' => 'required|in:admin,driver',
        ]);

        $password = Str::random(8);
        $validatedData['password'] = Hash::make($password);

        $user = User::create($validatedData);

        Mail::raw("Twoje tymczasowe hasło to: $password", function ($message) use ($user) {
            $message->to($user->email)
                ->subject('Twoje nowe konto');
        });

        return redirect()->route('users.index')->with('success', 'Użytkownik został dodany pomyślnie.');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return inertia('Users/Edit', ['user' => $user]);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'role' => 'required|in:admin,driver',
        ]);

        $user->update($validatedData);

        return redirect()->route('users.index')->with('success', 'Użytkownik został zaktualizowany.');
    }

    public function destroy($id)
    {
        User::findOrFail($id)->delete();
        return redirect()->route('admin.users.index')->with('success', 'Użytkownik został usunięty.');
    }
}
