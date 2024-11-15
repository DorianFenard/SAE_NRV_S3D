<?php
declare(strict_types=1);

namespace nrv\nancy\auth;

use nrv\nancy\exception\AuthnException;
use nrv\nancy\repository\NrvRepository;

/**
 * Classe permettant de faire les différentes opérations relatives à la connection d'utilisateurs
 */
class AuthnProvider
{
    /**
     * Connecte l'utilisateur à son compte.
     * @param string $email e-mail de l'utilisateur souhaitant se conecter
     * @param string $password mot de passe de l'utilisateur souhaitant se connecter, pas encore hashé
     * @throws AuthnException erreur renvoyée si le mot de passe est incorrect ou l'e-mail est invalide.
     * @return bool true si la connection a réussi
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
     * Enregistre un nouvel utilisateur dans la BD
     * @param string $email e-mail de l'utilisateur à créer
     * @param string $password mot de passe de l'utilisateur à créer, pas encore hashé
     * @throws AuthnException erreur renvoyée si le mot de passe n'a pas le format demandé, si l'e-mail est invalide ou si l'utilisateur existe déjà
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
            throw new AuthnException("Votre mot de passe ne respecte pas les critères (min 10 caractères, 1 chiffre, un caractère spécial, une minuscule/majuscule). ");
        }
    }

    /**
     * fonction verifiant la solidité du mot de passe fournit(taille > minimumLength, au moins un nombre, un caractère spécial, une minuscule/majuscule).
     * @param string $pass mot de passe (non hashé) à vérifier
     * @param int $minimumLength valeur de la taille minimale demandée
     * @return bool true si le mot de passe respecte les conditions demandées
     */
    public static function checkPasswordStrength(string $pass, int $minimumLength): bool
    {
        $longueurMini = (strlen($pass) > $minimumLength); // longueur minimale
        $possedeDigit = preg_match("#[\d]#", $pass); // au moins un digit
        $possedeSpe = preg_match("#[\W]#", $pass); // au moins un car. spécial
        $possedeMin = preg_match("#[a-z]#", $pass); // au moins une minuscule
        $possedeMaj = preg_match("#[A-Z]#", $pass); // au moins une majuscule

        return $longueurMini && $possedeDigit && $possedeSpe && $possedeMin && $possedeMaj;
    }

    /**
     * fonction renvoyant l'e-mail de l'utilisateur actuellement connecté
     * @return string e-mail de l'utilisateur actuellement connecté
     * @throws AuthnException Erreur si personne n'est connecté actuellement
     */
    public static function getSignedInUser(): string
    {
        if (isset($_SESSION['user'])) {
            return $_SESSION['user'];
        } else {
            throw new AuthnException("Vous n'êtes pas connecté ");
        }
    }

}