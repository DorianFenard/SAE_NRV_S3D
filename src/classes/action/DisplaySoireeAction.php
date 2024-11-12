<?php

namespace nrv\nancy\action;

use nrv\nancy\render\RendererFactory;
use nrv\nancy\repository\NrvRepository;

class DisplaySoireeAction extends Action{

    public function execute(): string
    {
        $loginButton = isset($_SESSION['user'])
            ? '<a class="login-button" href="?action=logout">SE DÉCONNECTER</a>'
            : '<a class="login-button" href="?action=login">SE CONNECTER</a>';

        $string = '<header class="program-header"><a class="home" href="?action=default">
                        <img class="program-icon" src="./images/icone.png" alt="NRV">
                    </a> <div class="menu">
                        <a class="list-button" href="?action=list">MA LISTE</a>
                        <a class="program-button" href="?action=program">PROGRAMME</a>'.
            $loginButton.'              
                    </div> </header>';
        if(isset($_GET['idspectacle'])){
            $idspectacle = $_GET['idspectacle'];
            $bd = NrvRepository::getInstance();
            $soiree = $bd->getSoireeSpectacle($idspectacle);

            $string .= RendererFactory::getRenderer($soiree)->render();
        }
        return $string;
    }
}