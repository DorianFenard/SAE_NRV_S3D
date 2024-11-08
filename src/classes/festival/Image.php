<?php

namespace nrv\nancy\festival;

class Image
{
    private int $id;
    private string $nom;

    public function __construct(int $id,string $nom){
        $this->nom=$nom;
        $this->id = $id;
    }
}