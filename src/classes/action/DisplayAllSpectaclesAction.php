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
        $string ='<nav class ="filtre" >
                <ul class ="buttonfiltre">
                <li><a href="index.php?action=program&trier=lieu"><button >Lieu</button></a></li>
                <li><a href="index.php?action=program&trier=date"><button >Date</button></a></li>
                <li><button>Genre</button></li>
                </ul>
            </nav>';
        if($this->http_method === "GET"){
           $bd =NrvRepository::getInstance();
           $resSoiree = $bd->getAllSoiree();
           $resSpectacle = [];
            foreach ($resSoiree as $soiree){
                $resSpectacle = array_merge($resSpectacle, $soiree->spectacles);
            }
           if(isset($_GET['trier'])){
               $criteres = $_GET['trier'];
               switch ($criteres){
                   case "lieu":
                       usort($resSoiree, function ($a, $b) {
                           return strcmp($a->lieu, $b->lieu);
                       });
                       break;
               }
           }
           foreach ($resSoiree as $soiree){
               $lieu = $soiree->lieu;
               foreach ($resSpectacle as $spectacle){
                   $string .=RendererFactory::getRenderer($lieu)->render();
                   $string .= RendererFactory::getRenderer($spectacle)->render();
               }
           }
        }
        return $string;
    }
}