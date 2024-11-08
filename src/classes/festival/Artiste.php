<?php

namespace nrv\nancy\festival;

class Artiste
{
 private int $id;
 private string $nom;

 public function __construct(int $id,string $nom){
     $this->id = $id;
     $this->nom = $nom;
 }
    public function __get(string $property) {
        if (property_exists($this, $property)) {
            return $this->$property;
        } else {
            throw new \Exception("Property '{$property}' does not exist in class " . __CLASS__);
        }
    }
}