<?php

namespace App\Service;

class HashedPassword
{
    public function hashedPassword(string $password)
    {
        return password_hash($password);
    }
}