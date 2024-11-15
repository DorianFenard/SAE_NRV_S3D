<?php
declare(strict_types=1);

namespace nrv\nancy\auth;

use nrv\nancy\exception\AuthnException;
use nrv\nancy\repository\NrvRepository;
use nrv\nancy\exception\AuthzException;

/**
 * Classe relative à la gestion des droits pour les différents utilisateurs du site
 */
class Authz
{

    /**
     * vérifie si le rôle de l'utilisateur actuellement connecté correspond à celui passé en paramètre,
     * si ce n'est pas le cas, la méthode renvoie une erreur.
     * @param int $roleAttendu rôle demandé à l'utilisateur
     * @throws AuthzException Erreur si l'utilisateur ne correspond pas au rôle demandé
     * @throws AuthnException Erreur si personne n'est connecté actuellement renvoyée par getSignedInUser()
     */
    public static function checkRole(int $roleAttendu)
    {
        $user = AuthnProvider::getSignedInUser();
        $pdo = NrvRepository::getInstance();

        $roleUser = $pdo->getRoleFromUser($user);
        if ($roleUser < $roleAttendu) {
            throw new AuthzException("Vous n'avez pas le niveau d'autorisation requise");
        }
    }

    /**
     * @throws AuthnException
     * @throws AuthzException
     */
    public static function checkOwner(string $autreUtil)
    {

        try {
            Authz::checkRole(100);
        } catch (AuthzException $e) {
            if ($autreUtil != AuthnProvider::getSignedInUser()) {
                throw new AuthzException($e->getMessage());
            }
        }
    }

    public static function getOwner(Playlist $pl): string
    {
        return NrvRepository::getInstance()->getOwner($pl);
    }
}