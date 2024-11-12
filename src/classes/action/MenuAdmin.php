<?php
declare(strict_types=1);
namespace nrv\nancy\action;

class MenuAdmin extends Action{

    public function execute(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['role']) && $_SESSION['role'] === 100) {

            $res = <<<HTML
        <ul>
            <li><a href="" >Créer un spectacle</li>
            <li><a href="" >Créer une soirée</li>
            <li><a href="" >Associer un spectacle à une soirée</li>
            <li><a href="" >Annuler un spectacle</li>
            <li><a href="" >Créer un compte staff</li>
        </ul>
        HTML;
            return $res;
        } else {
            header('Location: ?action=default');
            return "";
        }
    }
}