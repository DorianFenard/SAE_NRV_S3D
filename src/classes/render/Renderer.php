<?php
declare(strict_types=1);
namespace nrv\nancy\render;

interface Renderer{
    public function render() : string;
}