<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\CreateUserRequest;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Requests\ChangePasswordRequest;

class UserController extends Controller
{
    public function __construct()
    {
        Gate::authorize('manage-users');
    }

    public function index(): View
    {
        $users = User::query()
            ->where('id', '!=', auth()->id())
            ->when(auth()->user()->hasRole('team admin'), function (Builder $query) {
                return $query->where('team_id', auth()->user()->team_id);
            })
            ->paginate();

        return view('users.index', compact('users'));
    }

    public function create(): View
    {
        return view('users.create');
    }

    public function store(CreateUserRequest $request): RedirectResponse
    {
        $role = Role::where('name', 'user')->first();

        User::create(array_merge(
            $request->validated(),
            [
                'role_id' => $role->id,
                'team_id' => auth()->user()->team_id,
            ]
        ));

        return redirect()->route('users.index')->with('status', 'User created.');
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(ChangePasswordRequest $request, User $user)
    {
        $user->update([
            'password' => Hash::make($request['password']),
        ]);

        return redirect()->route('users.index')->with('status', 'Password changed.');
    }
}
