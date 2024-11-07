<?php
declare(strict_types=1);

namespace nrv\nancy\render;

class LieuRenderer {
    public static function render($lieu) {
        $html = "<div class='lieu'>";
        $html .= "<h2>" . $lieu->nom . "</h2>";     
        $html .= "<p>" . $lieu->adresse . "</p>";   
        $html .= "<p>Places assises : " . $lieu->placesAssises . "</p>";
        $html .= "<p>Places debout : " . $lieu->placesDebout . "</p>";
   

        $html .= "</div>";
        return $html;
    }
    
}
