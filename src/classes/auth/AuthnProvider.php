<?php
declare(strict_types=1);

namespace nrv\nancy\auth;

use nrv\nancy\exception\AuthnException;
use nrv\nancy\repository\NrvRepository;

class AuthnProvider
{
    /**
     * @throws AuthnException
     */
    public static function signin(string $email, string $password): bool
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $pdo = NrvRepository::getInstance();
            $userPass = $pdo->getPassword($email);
            if (!password_verify($password, $userPass)) {
                throw new AuthnException("Mot de passe incorrect");
            } else {
                return true;
            }
        } else {
            throw new AuthnException("Format d'email non respecté");
        }
    }

    /**
     * @throws AuthnException
     */
    public static function register(string $email, string $password)
    {
        if (AuthnProvider::checkPasswordStrength($password, 10)) {
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $pdo = NrvRepository::getInstance();
                if (!$pdo->userAlreadyExisting($email)) {
                    $hashedPassword = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
                    $pdo->addNewUser($email, $hashedPassword);
                } else {
                    throw new AuthnException("Votre addresse mail est déjà lié a un compte");
                }
            } else {
                throw new AuthnException("Votre email est invalide");
            }
        } else {
            throw new AuthnException("Votre mot de passe ne respecte pas les critères");
        }
    }

    public static function checkPasswordStrength(string $pass, int $minimumLength): bool
    {
        $longueurMini = (strlen($pass) > $minimumLength); // longueur minimale
        $possedeDigit = preg_match("#[\d]#", $pass); // au moins un digit
        $possedeSpe = preg_match("#[\W]#", $pass); // au moins un car. spécial
        $possedeMin = preg_match("#[a-z]#", $pass); // au moins une minuscule
        $possedeMaj = preg_match("#[A-Z]#", $pass); // au moins une majuscule

        return $longueurMini && $possedeDigit && $possedeSpe && $possedeMin && $possedeMaj;
    }

    public static function getSignedInUser(): string
    {
        if (isset($_SESSION['user'])) {
            return $_SESSION['user'];
        } else {
            throw new AuthnException("Vous n'êtes pas connecté ");
        }
    }

}