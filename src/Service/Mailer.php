<?php

namespace App\Service;

class Mailer
{
    public function sendCodeTo(User $user, int $code)
    {
        $mail = $user->getEmail();
        // le moyen d'envoyer le code avec l'email recuperer
    }
}