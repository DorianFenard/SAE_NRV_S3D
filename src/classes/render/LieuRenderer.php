<?php
declare(strict_types=1);

namespace nrv\nancy\render;

use nrv\nancy\festival\Lieu;

class LieuRenderer implements Renderer{

    private Lieu $lieu;

    public function __construct(Lieu $lieu){
        $this->lieu = $lieu;
    }
    public function render() : string {
        $html = "<div class='lieu'>";
        $html .= "<h2>" . $this->lieu->nom . "</h2>";
        $html .= "<p>" . $this->adresse . "</p>";
        $html .= "<p>Places assises : " . $this->lieu->placesAssises . "</p>";
        $html .= "<p>Places debout : " . $this->lieu->placesDebout . "</p>";
   

        $html .= "</div>";
        return $html;
    }
    
}
