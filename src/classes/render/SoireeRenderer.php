<?php
declare(strict_types=1);

namespace nrv\nancy\render;

use nrv\nancy\festival\Soiree;         
use nrv\nancy\render\SpectacleRenderer; 

class SoireeRenderer implements Renderer {
    private Soiree $soiree;
    public function __construct(Soiree $soiree) {
        $this->soiree = $soiree;
    }
    public function render(): string {
        $html = "<div class='soiree'>";
        $html .= "<h2>Nom de la soirée : " . htmlspecialchars($this->soiree->nom) . "</h2>";
        $html .= "<p>Thème : " . htmlspecialchars($this->soiree->thematique) . "</p>";
        $html .= "<p>Date de la soirée : " . htmlspecialchars($this->soiree->date) . "</p>";
        $html .= "<p>Horaire de début : " . htmlspecialchars($this->soiree->horaireDebut) . "</p>";
        $html .= "<p>Lieu de la soirée : " . htmlspecialchars((string) $this->soiree->lieuId) . "</p>";
        
        $html .= "<p>Spectacles :</p>";
        $html .= "<ul>";
        foreach($this->soiree->spectacles as $spectacle) {
            $html .= "<li>" . SpectacleRenderer::render($spectacle) . "</li>";
        }
        $html .= "</ul>";
        
        $html .= "</div>";
        return $html;
    }
}