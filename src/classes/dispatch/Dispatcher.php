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

class Dispatcher
{
    private string $action;

    public function __construct()
    {
        $this->action = $_GET['action'] ?? 'default';
    }

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

    private function renderPage(string $html)
    {
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
                </body>
                </html>
                HTML;
    }
}