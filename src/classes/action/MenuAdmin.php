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
        <div class="admin-menu">
            <div class="admin-action">
                <h1 class="admin-menu-header">Action administrateur : </h1>
                <ul class="admin-action-list">
                    <li class="admin-element"><a class="admin-link" href="index.php?action=creerSpectacle" >Créer un spectacle</li>
                    <li class="admin-element"><a class="admin-link" href="index.php?action=creerSoiree" >Créer une soirée</li>
                    <li class="admin-element"><a class="admin-link" href="index.php?action=addSpec2Soiree" >Associer un spectacle à une soirée</li>
                    <li class="admin-element"><a class="admin-link" href="index.php?action=annulerSpec" >Annuler un spectacle</li>
                    <li class="admin-element"><a class="admin-link" href="index.php?action=creerCompte" >Créer un compte staff</li>
                </ul>
            </div>
        </div>
        HTML;
            return $res;
        } else {
            header('Location: ?action=default');
            return "";
        }
    }
}