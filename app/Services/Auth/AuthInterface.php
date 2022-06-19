<?php

namespace App\Services\Auth;

use App\Models\User;

interface AuthInterface
{
    public function createOrUpdate($socialUser): User;

    public function findUser($socialUser): User|null;
}