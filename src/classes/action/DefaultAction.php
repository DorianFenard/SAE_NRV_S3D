<?php
declare(strict_types=1);

namespace nrv\nancy\action;

use nrv\nancy\render\RendererFactory;

class DefaultAction extends Action
{
    public function execute(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $listeLieuSpectacles = DisplayAllSpectaclesAction::getListLieuSpectacle();

        $loginButton = isset($_SESSION['user'])
            ? '<a class="login-button" href="?action=logout">SE DÉCONNECTER</a>'
            : '<a class="login-button" href="?action=login">SE CONNECTER</a>';
        $adminButton = isset($_SESSION['role']) && $_SESSION['role'] === 100
            ? '<a class="admin-button" href="?action=adminpage">ADMIN</a>'
            : '';

        $res = '
            <header class="home-header">
                <a class="home" href="?action=default">
                    <img class="home-icon" src="./images/icone.png" alt="NRV">
                </a>
                <div class="menu">
                    <a class="list-button" href="?action=list">MA LISTE</a>
                    <a class="program-button" href="?action=program">PROGRAMME</a>'.
                    $adminButton.
                    $loginButton.'
                 
                </div>
            </header>
            <div class="display-home">
                <h2 class="home-sous-titre">À vos agendas, rêveurs et noctambules ! Le Festival NRV débarque à Nancy dès le 10 décembre pour vous 
                emmener dans un tourbillon de spectacles déjantés et de folies artistiques !</h2>';
                foreach ($listeLieuSpectacles as $spectacle){
                    $res.=RendererFactory::getRenderer($spectacle['spectacle'])->render();
                }
        return $res.'</div>';
    }
}