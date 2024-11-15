<?php
declare(strict_types=1);
namespace nrv\nancy\action;

/**
 * Action déclenchée lorsque l'on veut se déconnecter,
 * détruit simplement la session afin de déconnecter tout utilisateur potentiellement connecté
 */
class LogoutAction
{
    public function execute(): string
    {
        session_destroy();
        session_start();
        header('Location: ?action=default');
        return "";
    }

}