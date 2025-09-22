<?php

namespace App\Http\Controllers\Admin;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends BaseAdminController
{
    /**
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 15);
        $search = $request->input('search');
        $role = $request->input('role');
        
        $users = User::when($search, function($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
            })
            ->when($role, function($query) use ($role) {
                $query->where('role', $role);
            })
            ->withCount(['artworks', 'comments'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return $this->success(UserResource::collection($users));
    }

    /**
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', 'in:admin,moderator,user'],
            'avatar' => ['nullable', 'string'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'avatar' => $validated['avatar'] ?? null,
        ]);

        return $this->success(new UserResource($user), 'Пользователь успешно создан', 201);
    }

    /**
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(User $user)
    {
        $user->loadCount(['artworks', 'comments']);
        return $this->success(new UserResource($user));
    }

    /**
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => [
                'sometimes',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role' => ['sometimes', 'string', 'in:admin,moderator,user'],
            'avatar' => ['nullable', 'string'],
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return $this->success(new UserResource($user), 'Пользователь успешно обновлен');
    }

    /**
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return $this->error('Вы не можете удалить свой собственный аккаунт', 403);
        }

        $user->delete();

        return $this->deleted();
    }

    /**
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleBlock(User $user)
    {
        if ($user->id === auth()->id()) {
            return $this->error('Вы не можете заблокировать свой собственный аккаунт', 403);
        }

        $user->update([
            'is_blocked' => !$user->is_blocked,
            'blocked_at' => $user->is_blocked ? null : now(),
        ]);

        $message = $user->is_blocked ? 'Пользователь заблокирован' : 'Пользователь разблокирован';
        
        return $this->success([
            'is_blocked' => $user->is_blocked,
            'blocked_at' => $user->blocked_at,
        ], $message);
    }
}
