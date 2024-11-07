<?php
declare(strict_types=1);
namespace nrv\nancy\action;

class DefaultAction extends Action{
    public function execute() : string
    {
        $res = <<<HTML
        <p>Bienvenue sur NRV</p>
        HTML;
        return $res;
    }
}