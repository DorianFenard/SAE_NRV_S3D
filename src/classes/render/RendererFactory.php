<?php
declare(strict_types=1);
namespace nrv\nancy\render;

use nrv\nancy\render\LieuRenderer;
use nrv\nancy\render\SoireeRenderer;
use nrv\nancy\render\SpectacleRenderer;

use nrv\nancy\festival\Lieu;
use nrv\nancy\festival\Soiree;
use nrv\nancy\festival\Spectacle;

use nrv\nancy\render\Renderer;

class RendererFactory{
    public static function getRenderer(mixed $objRendu) : Renderer{
        if($objRendu instanceof Lieu){
            $renderer = new LieuRenderer($objRendu);
        }else if($objRendu instanceof Soiree){
            $renderer = new SoireeRenderer($objRendu);
        }else if($objRendu instanceof Spectacle){
            $renderer = new SpectacleRenderer($objRendu);
        }else{
            $renderer = null;
        }
        return $renderer;
    }
}