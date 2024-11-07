<?php
declare(strict_types=1);
namespace nrv\nancy\action;

class DefaultAction extends Action{
    public function execute() : string
    {
        $res = "coucou c'est moi tchoupi";
        return $res;
    }
}