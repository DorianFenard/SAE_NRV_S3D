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
    public static function getRenderer(Renderer $objRendu) : Renderer{
        if($objRendu instanceof Lieu){
            $renderer = new LieuRenderer();
        }else if($objRendu instanceof Soiree){
            $renderer = new SoireeRenderer();
        }else if($objRendu instanceof Spectacle){
            $renderer = new SpectacleRenderer();
        }else{
            $renderer = null;
        }
        return $renderer;
    }
}