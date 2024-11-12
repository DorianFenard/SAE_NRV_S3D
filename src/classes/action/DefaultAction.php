<?php
declare(strict_types=1);

namespace nrv\nancy\action;

class DefaultAction extends Action
{
    public function execute(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $loginButton = isset($_SESSION['user'])
            ? '<a class="login-button" href="?action=logout">SE DÉCONNECTER</a>'
            : '<a class="login-button" href="?action=login">SE CONNECTER</a>';

        $res = <<<HTML
            <header class="home-header">
                <a class="home" href="?action=default">
                    <img class="home-icon" src="./images/icone.png" alt="NRV">
                </a>
                <div class="menu">
                    <a class="list-button" href="?action=list">MA LISTE</a>
                    <a class="program-button" href="?action=program">PROGRAMME</a>
                    <a class="admin-button" href="?action=adminpage">ADMIN</a>
                    $loginButton
                </div>
            </header>
        HTML;

        return $res;
    }
}