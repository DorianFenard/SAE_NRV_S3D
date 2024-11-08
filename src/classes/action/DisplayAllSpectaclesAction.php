<?php
declare(strict_types=1);
namespace nrv\nancy\action;

use nrv\nancy\repository\NrvRepository;

class DisplayAllSpectaclesAction extends Action{
    public function execute(): string
    {
        if($this->http_method === "GET"){
           $bd =NrvRepository::getInstance();
           $res = $bd->getAllSoiree();
           foreach ($res as $soiree){
               var_dump($soiree);
               $resspectacle = $bd->getSpectacleSoiree(intval($soiree['id_soiree']));
               foreach ($resspectacle as $spectacle){
                   var_dump($spectacle);
               }
           }
        }
        return "";
    }
}