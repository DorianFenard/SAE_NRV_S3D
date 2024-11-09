<?php
declare(strict_types=1);
namespace nrv\nancy\action;

use nrv\nancy\festival\Artiste;
use nrv\nancy\festival\Image;
use nrv\nancy\festival\Soiree;
use nrv\nancy\festival\Spectacle;
use nrv\nancy\render\Renderer;
use nrv\nancy\render\RendererFactory;
use nrv\nancy\render\SoireeRenderer;
use nrv\nancy\render\SpectacleRenderer;
use nrv\nancy\repository\NrvRepository;

class DisplayAllSpectaclesAction extends Action{
    public function execute(): string
    {
        $string ="";
        if($this->http_method === "GET"){

           $bd =NrvRepository::getInstance();
           $resSoiree = $bd->getAllSoiree();
           foreach ($resSoiree as $soiree){
               $string .= RendererFactory::getRenderer($soiree)->render();
           }
        }
        return $string;
    }
}