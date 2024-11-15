<?php

namespace nrv\nancy\action;

use nrv\nancy\render\RendererFactory;
use nrv\nancy\repository\NrvRepository;

/**
 * Action déclenchée lorsque l'on veut afficher les détails d'une soirée,
 */
class DisplaySoireeAction extends Action{

    public function execute(): string
    {
        $loginButton = isset($_SESSION['user'])
            ? '<a class="login-button" href="?action=logout">SE DÉCONNECTER</a>'
            : '<a class="login-button" href="?action=login">SE CONNECTER</a>';
        $adminButton = isset($_SESSION['role']) && $_SESSION['role'] >= 50
            ? '<a class="admin-button" href="?action=adminpage">ADMIN</a>'
            : '';

        $string = '<header class="program-header">
                        <a class="home" href="?action=default">
                            <img class="home-icon" src="./images/icone.png" alt="NRV">
                        </a>
                       <div class="menu">
                        <a class="list-button" href="?action=">ACCUEIL</a>
                        <a class="list-button" href="?action=list">MA LISTE</a>
                        <a class="program-button" href="?action=program">PROGRAMME</a>' .
            $adminButton . $loginButton . '              
                    </div> </header>';

        if(isset($_GET['idspectacle'])){
            $idspectacle = $_GET['idspectacle'];
            $bd = NrvRepository::getInstance();
            $soiree = $bd->getSoireeSpectacle($idspectacle);
            $string .= "<div class =soiree_spectacle>";
            $string .= RendererFactory::getRenderer($soiree)->render();
            $string .= "</div>";
        }
        return $string;
    }
}