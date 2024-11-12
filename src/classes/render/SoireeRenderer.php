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
        $html .= "<h2>Nom de la soirée : " . $this->soiree->nom . "</h2>";
        $html .= "<p>Thème : " . $this->soiree->thematique . "</p>";
        $html .= "<p>Date de la soirée : " . $this->soiree->date . "</p>";
        $html .= "<p>Horaire de début : " . $this->soiree->horaireDebut . "</p>";
        $html .= "<p>Lieu de la soirée : " .  $this->soiree->lieu->nom . "</p>";
        
        $html .= "<p>Spectacles :</p>";
        $html .= "<div class='spectacles'>";
        foreach($this->soiree->spectacles as $spectacle) {
            $render = RendererFactory::getRenderer($spectacle);
            $html .=  $render->render() ;
        }
        $html .= "</div>";
        
        $html .= "</div>";
        return $html;
    }
}