<?php
declare(strict_types=1);

namespace nrv\nancy\dispatch;

use nrv\nancy\action\AddSpec2SoireeAction;
use nrv\nancy\action\AnnulerSpectacleAction;
use nrv\nancy\action\CreerSoireeAction;
use nrv\nancy\action\CreerSpectacleAction;
use nrv\nancy\action\DefaultAction;
use nrv\nancy\action\DisplayAllSpectaclesAction;
use nrv\nancy\action\DisplaySoireeAction;
use nrv\nancy\action\LoginAction;
use nrv\nancy\action\FavorisAction;
use nrv\nancy\action\LogoutAction;
use nrv\nancy\action\MenuAdmin;
use nrv\nancy\action\CreerCompte;

/**
 * classe permettant d'afficher l'action demandée.
 */
class Dispatcher
{
    /**
     * @var string action à appliquer suite à la requête
     */
    private string $action;

    /**
     * constructeur du Dispatcher, l'attribut action est instancié avec l'action envoyée dans la requête GET
     */
    public function __construct()
    {
        $this->action = $_GET['action'] ?? 'default';
    }

    /**
     * Déclenche le script demandé, le script de la page d'accueil DefaultAction est déclenché si aucune requête GET
     * n'est reçue.
     */
    public function run()
    {
        $action = null;
        switch ($this->action) {
            case "program":
                $action = new DisplayAllSpectaclesAction();
                break;
            case "login":
                $action = new LoginAction();
                break;
            case "logout":
                $action = new LogoutAction();
                break;
            case "creerSoiree":
                $action = new CreerSoireeAction();
                break;
            case "creerSpectacle":
                $action = new CreerSpectacleAction();
                break;
            case "addSpec2Soiree":
                $action = new AddSpec2SoireeAction();
                break;
            case "annulerSpec" :
                $action = new AnnulerSpectacleAction();
                break;
            case "list":
                $action = new FavorisAction();
                break;
            case "soiree":
                $action = new DisplaySoireeAction();
                break;
            case "adminpage":
                $action = new MenuAdmin();
                break;
            case "creerCompte":
                $action = new CreerCompte();
                break;
            default :
                $action = new DefaultAction();
                break;
        }
        $html = $action->execute();
        $this->renderPage($html);
    }

    /**
     * Effectue le rendu de l'action demandée dans run()
     * @param string $html corps de la page à afficher, entouré par le header en haut et le footer en bas
     */
    private function renderPage(string $html)
    {
        $loginButton = isset($_SESSION['user'])
            ? '<a href="?action=logout">SE DÉCONNECTER</a>'
            : '<a href="?action=login">SE CONNECTER</a>';
        $adminButton = isset($_SESSION['role']) && $_SESSION['role'] === 100
            ? '<a href="?action=adminpage">ADMIN</a>'
            : '';

        echo <<<HTML
                <!DOCTYPE html>
                <html lang="fr">
                <head>
                    <meta charset="UTF-8">
                    <link rel="stylesheet" href="./css/style.css">
                    <title>NRV NANCY WOW</title>
                </head>
                <body>
                    $html
                    <footer>
                        <div class="nav-footer">
                            <a href="?action=default">ACCUEIL</a>
                            <a href="?action=program">PROGRAMME</a>
                            <a href="?action=list">FAVORIS</a>
                            $loginButton
                            $adminButton
                    </footer>
                </body>
                </html>
                HTML;
    }
}