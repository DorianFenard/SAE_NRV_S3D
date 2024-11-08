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
 public function __get($name){
     return $this->$name;
 }
}