<?php
declare(strict_types=1);

namespace nrv\nancy\festival;

use nrv\nancy\festival\Spectacle;

class Soiree {
    private int $id;
    private string $nom;
    private string $thematique;
    private string $date;
    private string $horaireDebut;
    private Lieu $lieu;
    private array $spectacles; 

    public function __construct(int $id,string $nom, string $thematique, string $date, string $horaireDebut, Lieu $lieu, array $spectacles) {
        $this->id = $id;
        $this->nom = $nom;
        $this->thematique = $thematique;
        $this->date = $date;
        $this->horaireDebut = $horaireDebut;
        $this->lieu = $lieu;
        $this->spectacles = $spectacles;
    }

    public function __get(string $property) {
        if (property_exists($this, $property)) {
            return $this->$property;
        } else {
            throw new \Exception("Property '{$property}' does not exist in class " . __CLASS__);
        }
    }


    public function ajouterSpectacle(Spectacle $spectacle) : void {
        $this->spectacles[] = $spectacle;
    }
}