<?php
declare(strict_types=1);

namespace nrv\nancy\action;

use nrv\nancy\repository\NrvRepository;
use nrv\nancy\render\RendererFactory;

class FavorisAction extends Action
{
    public function execute(): string
    {
        $favoriteIds = isset($_COOKIE['favorites']) ? unserialize($_COOKIE['favorites']) : [];
        $loginButton = isset($_SESSION['user'])
            ? '<a class="login-button" href="?action=logout">SE DÉCONNECTER</a>'
            : '<a class="login-button" href="?action=login">SE CONNECTER</a>';
        $html = '<header class="program-header"><a class="home" href="?action=default">
                        <img class="program-icon" src="./images/icone.png" alt="NRV">
                    </a> <div class="menu">
                        <a class="list-button" href="?action=list">MA LISTE</a>
                        <a class="program-button" href="?action=program">PROGRAMME</a>'.
                        $loginButton.'              
                    </div>
                    </header><h2>Mes Spectacles Favoris</h2>';

        if (empty($favoriteIds)) {
            $html .= '<p>Aucun spectacle en favoris pour le moment.</p>';
            return $html;
        }

        $repo = NrvRepository::getInstance();
        $allSpectacles = $repo->getAllSpectacle();

        $favoriteSpectacles = array_filter($allSpectacles, fn($spectacle) => in_array($spectacle->id, $favoriteIds, true));

        $html .= '<div class="spectacles-favoris">';
        foreach ($favoriteSpectacles as $spectacle) {
            $html .= RendererFactory::getRenderer($spectacle)->render();
        }
        $html .= '</div>';

        return $html;
    }
}