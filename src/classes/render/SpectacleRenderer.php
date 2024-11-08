<?php
declare(strict_types=1);

namespace nrv\nancy\render;

use nrv\nancy\festival\Spectacle;

class SpectacleRenderer implements Renderer {
    private Spectacle $spectacle;

    public function __construct(Spectacle $spectacle) {
        $this->spectacle = $spectacle;
    }
    public function render(): string {
        $html = "<div class='spectacle'>";
        $html .= "<h2>" .$this->spectacle->titre . "</h2>";

            $html .= "<p>Artistes : ";
            foreach ($this->spectacle->artistes as $artiste) {
                $html .= $artiste->nom . ", ";
            }
            $html = substr($html, 0, -2);
            $html.= "</p>";
        
        
        $html .= "<p>Description : " . $this->spectacle->description . "</p>";
        
        if (!empty($this->spectacle->images)) {
            $html .= "<div class='images'>";
            foreach ($this->spectacle->images as $image) {
                $html .= "<img src='" .$image . "' alt='Image du spectacle'>";
            }
            $html .= "</div>";
        }
        
        if ($this->spectacle->urlVideo) {
            $html .= "<p>Vidéo : <a href='" .$this->spectacle->urlVideo . "'>Voir la vidéo</a></p>";
        }
        
        $html .= "<p>Horaire prévisionnel : " . $this->spectacle->horairePrevisionnel . "</p>";
        $html .= "<p>Style : " . $this->spectacle->style . "</p>";
        
        $html .= "<p>État : " . ($this->spectacle->estAnnule ? "Annulé" : "Prévu") . "</p>";
        
        $html .= "</div>";
        return $html;
    }
}