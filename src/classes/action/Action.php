<?php
declare(strict_types=1);
namespace nrv\nancy\action;

abstract class Action{

    public abstract function execute() : string;
}