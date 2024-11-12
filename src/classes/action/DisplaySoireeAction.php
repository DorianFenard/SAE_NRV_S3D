<?php

namespace nrv\nancy\action;

use nrv\nancy\render\RendererFactory;
use nrv\nancy\repository\NrvRepository;

class DisplaySoireeAction extends Action{

    public function execute(): string
    {
        $string ="";
        if(isset($_GET['idspectacle'])){
            $idspectacle = $_GET['idspectacle'];
            $bd = NrvRepository::getInstance();
            $soiree = $bd->getSoireeSpectacle($idspectacle);
            $string = RendererFactory::getRenderer($soiree)->render();
        }
        return $string;
    }
}