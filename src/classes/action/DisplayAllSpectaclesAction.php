<?php
declare(strict_types=1);
namespace nrv\nancy\action;

use nrv\nancy\festival\Artiste;
use nrv\nancy\festival\Image;
use nrv\nancy\festival\Soiree;
use nrv\nancy\festival\Spectacle;
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
               $id = $soiree['id_soiree'];
               $nom_soiree = $soiree['nom_soiree'];
               $thematique = $soiree['thematique'];
               $date = $soiree['date'];
               $lieuid = $bd->getLieuId(intval($soiree['id_soiree']));
               $horaire =$soiree['horaire_debut'];
               $objSoiree = new Soiree(intval($id),$nom_soiree,$thematique,$date,$horaire,intval($lieuid));
               $resspectacle = $bd->getSpectacleSoiree(intval($soiree['id_soiree']));
               foreach ($resspectacle as $spectacle){
                   $id_spectacle = $spectacle['id_spectacle'];
                   $nom_spectacle = $spectacle['nom_spectacle'];
                   $description = $spectacle['description'];
                   $horaire_prev =$spectacle['horaire_previsionnel'];
                   $style = $spectacle['style'];
                   $url = $spectacle['url_video'];
                   $images = [];
                   $resImages = $bd->getImagesSpectacle(intval($id_spectacle));
                   foreach ($resImages as $images){
                       $objImages = new Image(intval($images['id_image']),$images['nom_image']);
                        $images[] = $objImages;
                   }
                   $artistes = [];
                   $resArtistes = $bd->getArtisteSpectacle(intval($spectacle['id_spectacle']));

                   foreach ($resArtistes as $artiste){
                       $objArtiste = new Artiste(intval($artiste['id_artiste']),$artiste['nom_artiste']);
                       $artistes[] = $objArtiste;
                   }

                   $objSpectacle = new Spectacle(intval($id_spectacle),$nom_spectacle,$artistes,$description,$images,$url,$horaire_prev,$style,false);

                   $objSoiree->ajouterSpectacle($objSpectacle);
               }
               $soireerender = new SoireeRenderer($objSoiree);
               $string .= $soireerender->render();
           }
        }
        return $string;
    }
}