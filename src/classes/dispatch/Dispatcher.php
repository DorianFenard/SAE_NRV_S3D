<?php
declare(strict_types=1);

namespace nrv\nancy\dispatch;

use nrv\nancy\action\AddSpec2Soiree;
use nrv\nancy\action\CreerSoireeAction;
use nrv\nancy\action\CreerSpectacleAction;
use nrv\nancy\action\DefaultAction;
use nrv\nancy\action\DisplayAllSpectaclesAction;
use nrv\nancy\action\LoginAction;
use nrv\nancy\action\FavorisAction;

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
            case "creerSoiree":
                $action = new CreerSoireeAction();
                break;
            case "creerSpectacle":
                $action = new CreerSpectacleAction();
                break;
            case "addSpec2Soiree":
                $action = new AddSpec2Soiree();
                break;
            case "list":
                $action = new FavorisAction();
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