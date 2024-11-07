<?php
declare(strict_types=1);

namespace nrv\nancy\render;

use nrv\nancy\festival\Spectacle;

class SpectacleRenderer {
    public static function render(Spectacle $spectacle): string {
        $html = "<div class='spectacle'>";
        $html .= "<h2>" . htmlspecialchars($spectacle->titre) . "</h2>";     
        
        $html .= "<p>Artistes : " . implode(", ", $spectacle->artistes) . "</p>";
        
        $html .= "<p>Description : " . htmlspecialchars($spectacle->description) . "</p>";
        
        if (!empty($spectacle->images)) {
            $html .= "<div class='images'>";
            foreach ($spectacle->images as $image) {
                $html .= "<img src='" . htmlspecialchars($image) . "' alt='Image du spectacle'>";
            }
            $html .= "</div>";
        }
        
        if ($spectacle->urlVideo) {
            $html .= "<p>Vidéo : <a href='" . htmlspecialchars($spectacle->urlVideo) . "'>Voir la vidéo</a></p>";
        }
        
        $html .= "<p>Horaire prévisionnel : " . htmlspecialchars($spectacle->horairePrevisionnel) . "</p>";
        $html .= "<p>Style : " . htmlspecialchars($spectacle->style) . "</p>";
        
        $html .= "<p>État : " . ($spectacle->estAnnule ? "Annulé" : "Prévu") . "</p>";
        
        $html .= "</div>";
        return $html;
    }
}