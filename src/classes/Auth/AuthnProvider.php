<?php
declare(strict_types=1);
namespace nrv\nancy\auth;

use nrv\nancy\repository\NrvRepository;

class AuthnProvider{
    public static function signin(string $email, string $password) : bool{
        $connexion = false;
        if(filter_var($email, FILTER_VALIDATE_EMAIL)){
            $pdo = NrvRepository::getInstance();
            $userPass = $pdo->getPassword($email);
            if(password_verify($password, $userPass)){
                $connexion = true;
            }
        }
        return $connexion;
    }

}