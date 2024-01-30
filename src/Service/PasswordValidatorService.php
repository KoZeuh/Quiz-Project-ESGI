<?php

namespace App\Service;

class PasswordValidatorService
{
    private $minLength = 12;
    private $minLowercase = 1;
    private $minUppercase = 1;
    private $minSpecial = 1;

    public function validate(string $password): bool
    {
        if (strlen($password) < $this->minLength) {
            return false;
        }

        if (preg_match_all('/[a-z]/', $password) < $this->minLowercase) {
            return false;
        }

        if (preg_match_all('/[A-Z]/', $password) < $this->minUppercase) {
            return false;
        }

        if (preg_match_all('/[^a-zA-Z0-9]/', $password) < $this->minSpecial) {
            return false;
        }
        
        return true;
    }
}