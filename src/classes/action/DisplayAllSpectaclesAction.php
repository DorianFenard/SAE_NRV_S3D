<?php
declare(strict_types=1);
namespace nrv\nancy\action;

use nrv\nancy\repository\NrvRepository;

class DisplayAllSpectaclesAction extends Action{
    public function execute(): string
    {
        if($this->http_method === "GET"){
            $res = <<<HTML
            
            
            HTML;

        }
        NrvRepository::getInstance();
        return "";
    }
}