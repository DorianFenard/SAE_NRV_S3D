<?php
declare(strict_types=1);

namespace nrv\nancy\render;

use nrv\nancy\festival\Soiree;         
use nrv\nancy\render\SpectacleRenderer; 

class SoireeRenderer {
    public static function render(Soiree $soiree): string {
        $html = "<div class='soiree'>";
        $html .= "<h2>Nom de la soirée : " . htmlspecialchars($soiree->nom) . "</h2>";
        $html .= "<p>Thème : " . htmlspecialchars($soiree->thematique) . "</p>";
        $html .= "<p>Date de la soirée : " . htmlspecialchars($soiree->date) . "</p>";
        $html .= "<p>Horaire de début : " . htmlspecialchars($soiree->horaireDebut) . "</p>";
        $html .= "<p>Lieu de la soirée : " . htmlspecialchars((string) $soiree->lieuId) . "</p>";
        
        $html .= "<p>Spectacles :</p>";
        $html .= "<ul>";
        foreach($soiree->spectacles as $spectacle) {
            $html .= "<li>" . SpectacleRenderer::render($spectacle) . "</li>";
        }
        $html .= "</ul>";
        
        $html .= "</div>";
        return $html;
    }
}