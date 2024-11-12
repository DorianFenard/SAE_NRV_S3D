<?php
declare(strict_types=1);

namespace nrv\nancy\action;

use nrv\nancy\repository\NrvRepository;
use nrv\nancy\render\RendererFactory;

class FavorisAction extends Action
{
    public function execute(): string
    {

        $favoriteIds = $_SESSION['favorites'] ?? [];

        $html = '<h2>Mes Spectacles Favoris</h2>';

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