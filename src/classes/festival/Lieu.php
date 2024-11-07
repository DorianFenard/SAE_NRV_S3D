<?php
declare(strict_types=1);

namespace nrv\nancy\festival;

class Lieu {
    private int $id;
    private string $nom;
    private string $adresse;
    private int $placesAssises;
    private int $placesDebout;
    private array $images; 

    public function __construct(int $id, string $nom, string $adresse, int $placesAssises, int $placesDebout, array $images = []) {
        $this->id = $id;
        $this->nom = $nom;
        $this->adresse = $adresse;
        $this->placesAssises = $placesAssises;
        $this->placesDebout = $placesDebout;
        $this->images = $images;
    }

    public function __get(string $property) {
        if (property_exists($this, $property)) {
            return $this->$property;
        } else {
            throw new \Exception("Property '{$property}' does not exist in class " . __CLASS__);
        }
    }

    public static function getLieuById(int $id) : Lieu {
        $lieu = new Lieu(1, 'La Cigale', '120, boulevard Rochechouart, 75018 Paris', 120, 130, []);
        return $lieu;
    }
}