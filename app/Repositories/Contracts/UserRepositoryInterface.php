<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use Illuminate\Http\Request;

interface UserRepositoryInterface
{
    public function register(Request $request);

    public function login(Request $request);

    public function getUsers();

    public function getUser($userId);

    public function createUserVerificationLink(User $user);

    public function confirmUser($userId, $hash);
}
