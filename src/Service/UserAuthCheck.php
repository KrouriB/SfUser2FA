<?php

namespace App\Service;

use App\Entity\User;


class UserAuthCheck
{
    public function userListCheck(string $email, ?array $listUser)
    {
        // $email = filter_input()
        $found = false;

        foreach($listUser as $user)
        {
            if($user->getEmail() == $email)
            {
                $found = $user;
            }
        }

        if($found != false)
        {
            return $found;
        }
        else
        {
            return false;
        }
    }

    public function passwordCheck(User $user, string $password)
    {
        if($user->getPassword() == $password)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function codeCheck(int $codePost, int $codeSend)
    {
        if($codePost == $codeSend)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}