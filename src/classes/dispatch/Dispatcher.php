<?php
declare(strict_types=1);
namespace nrv\nancy\dispatch;

use nrv\nancy\action\DefaultAction;
use nrv\nancy\action\DisplaySpectacles;
class Dispatcher{
    private string $action;

    public function __construct(){
        $this->action = $_GET['action'] ?? 'default';
    }

    public function run(){
        $action = null;
        switch ($this->action){
            case "display-spectacle":
                $action = new DisplaySpectacles();
                break;
            default :
                $action = new DefaultAction();
                break;
        }
        $html = $action->execute();
        $this->renderPage($html);
    }

    private function renderPage(string $html){
        echo <<<HTML
                <!DOCTYPE html>
                <html lang="fr">
                <head>
                    <meta charset="UTF-8">
                    <title>NRV NANCY WOW</title>
                </head>
                <body>
                    $html
                </body>
                </html>
                HTML;
    }
}