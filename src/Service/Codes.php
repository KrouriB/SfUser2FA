<?php

namespace App\Service;

class Codes
{
    public function generate()
    {
        return rand(10000, 99999);
    }
}