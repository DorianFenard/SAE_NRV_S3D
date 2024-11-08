<?php
declare(strict_types=1);
namespace nrv\nancy\action;

use nrv\nancy\render\RendererFactory;
use nrv\nancy\repository\NrvRepository;
use nrv\nancy\festival\Spectacle;

class DisplayAllSpectaclesAction extends Action{
    public function execute(): string
    {

        $pdo = NrvRepository::setConfig("./config/db_config.ini");
        $pdo = NrvRepository::getInstance();
        $specs = $pdo->getAllSpectacles();
        $res = "<p>Programme complet : </p> <br>";
        foreach($specs as $spec){
            $spec = New Spe
            $renderer = RendererFactory::getRenderer($spec);
            $res .= $renderer->render();
        }
        return $res;
    }
}