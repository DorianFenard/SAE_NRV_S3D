<?php
declare(strict_types=1);

namespace nrv\nancy\render;

use nrv\nancy\festival\Spectacle;

class SpectacleRenderer implements Renderer
{
    private Spectacle $spectacle;

    public function __construct(Spectacle $spectacle)
    {
        $this->spectacle = $spectacle;
    }

    public function render(): string
    {
        $html = "<div class='spectacle'>";
        $html .= "<h2>" . $this->spectacle->titre . "</h2>";

        $html .= "<p>Artistes : ";
        foreach ($this->spectacle->artistes as $artiste) {
            $html .= $artiste->nom . ", ";
        }
        $html = substr($html, 0, -2);
        $html .= "</p>";


        $html .= "<p>Description : " . $this->spectacle->description . "</p>";
        $html .= "<p>Duree du spectacle en minutes : " . $this->spectacle->duree . "</p>";
        if ($this->spectacle->images !== null) {
            $html .= "<div class='images'>";
            foreach ($this->spectacle->images as $image) {
                if (is_file("./images/" . $image->nom))
                    $html .= "<img src='./images/$image->nom' alt='Image du spectacle' height='400'>";
                else
                    $html .= "<p class='pas-image'>PAS D'IMAGE DISPONIBLE</p>";
            }
            $html .= "</div>";
        }


        if ($this->spectacle->urlVideo) {
            if (strpos($this->spectacle->urlVideo, 'youtube.com') !== false || strpos($this->spectacle->urlVideo, 'youtu.be') !== false) {
                $videoId = '';
                if (preg_match('/v=([^&]+)/', $this->spectacle->urlVideo, $matches)) {
                    $videoId = $matches[1];
                }
                if ($videoId) {
                    $embedUrl = "https://www.youtube.com/embed/" . $videoId;
                    $html .= "<div class='video'>
                        <iframe width='600' height='400' src='" . $embedUrl . "' frameborder='0' allowfullscreen></iframe>
                      </div>";
                }
            }
        }
        $html .= '<div class="horaires-spectacle">';

        $html .= "<p>Horaire prévisionnel : " . $this->spectacle->horairePrevisionnel . "</p>";
        $html .= "<p>Style : " . $this->spectacle->style . "</p>";

        $html .= "<p>État : " . ($this->spectacle->estAnnule ? "Annulé" : "Prévu") . "</p>";

        $html .= "</div></div>";
        return $html;
    }
}