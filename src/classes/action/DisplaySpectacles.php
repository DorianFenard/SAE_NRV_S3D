<?php
declare(strict_types=1);
namespace nrv\nancy\action;

use iutnc\deefy\repository\NrvRepository;

class DisplaySpectacles extends Action{
    public function execute(): string
    {
        NrvRepository::getInstance();
        return "";
    }
}