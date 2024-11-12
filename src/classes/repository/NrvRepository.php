<?php
declare(strict_types=1);
namespace nrv\nancy\repository;

use nrv\nancy\festival\Artiste;
use nrv\nancy\festival\Image;
use PDO;
use PDOException;
use Exception;
use nrv\nancy\festival\Spectacle;
use nrv\nancy\festival\Lieu;
use nrv\nancy\festival\Soiree;

class NrvRepository {
    private \PDO $pdo;
    private static ?NrvRepository $instance = null;
    private static array $config = [];


    private function __construct(array $conf) {
        $this->pdo = new \PDO($conf['dsn'], $conf['user'], $conf['pass'],
            [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]);
    }
    public static function getInstance(){
        if (is_null(self::$instance)) {
            self::$instance = new NrvRepository(self::$config);
        }
        return self::$instance;
    }
    public static function setConfig(string $file) {
        $conf = parse_ini_file($file);
        if ($conf === false) {
            throw new \Exception("Error reading configuration file");
        }
        $driver = $conf['driver'];
        $host = $conf['host'];
        $database = $conf['dbname'];
        $dsn = "$driver:host=$host;dbname=$database";

        self::$config = [ 'dsn'=> $dsn,'user'=> $conf['username'],'pass'=> $conf['password']];
    }

    public function getAllSoiree() : array{
        $stmt = $this->pdo->prepare("Select * from soiree");
        $stmt->execute();
        $fetch = $stmt->fetchAll();
        foreach ($fetch as $soiree){
            $lieu = $this->getLieuId((int) $soiree['id_soiree']);
            $spectacles = $this->getSpectacleSoiree((int) $soiree['id_soiree']);
            $array[] = new Soiree((int) $soiree['id_soiree'], $soiree['nom_soiree'], $soiree['thematique'], $soiree['date'], $soiree['horaire_debut'], $lieu,
            $spectacles);
        }
        return $array;
    }

    public function getAllSpectacle() : array{
        $stmt = $this->pdo->prepare("Select * from spectacle");
        $stmt->execute();
        $fetch = $stmt->fetchAll();
        foreach ($fetch as $spectacle){
            $images = $this->getImagesSpectacle((int) $spectacle['id_spectacle']);
            $artistes = $this->getArtisteSpectacle((int) $spectacle['id_spectacle']);
            $array[] = new Spectacle((int) $spectacle['id_spectacle'], $spectacle['nom_spectacle'], $artistes,
                $spectacle['description'], $images, $spectacle['url_video'], $spectacle['horaire_previsionnel'],
                $spectacle['style'], boolval($spectacle['est_annule']) );
        }
        return $array;
    }

    public function getSpectacleSoiree(int $idSoiree) : array{
        $stmt = $this->pdo->prepare("Select spectacle.id_spectacle,nom_spectacle,style,description,horaire_previsionnel,url_video,est_annule from spectacle inner join soiree2spectacle on spectacle.id_spectacle = soiree2spectacle.id_spectacle where id_soiree = ? ORDER by horaire_previsionnel");
        $stmt->execute([$idSoiree]);
        $fetch = $stmt->fetchAll();
        foreach ($fetch as $spec){
            $images = $this->getImagesSpectacle((int) $spec['id_spectacle']);
            $artistes = $this->getArtisteSpectacle((int) $spec['id_spectacle']);
            $array[] = new Spectacle((int) $spec['id_spectacle'], $spec['nom_spectacle'], $artistes,
                $spec['description'], $images, $spec['url_video'], $spec['horaire_previsionnel'],
                $spec['style'], boolval($spec['est_annule']) );
        }
        return $array;
    }

    public function getLieuId(int $idsoiree) : Lieu{
        $stmt = $this->pdo->prepare("Select * from Lieu INNER JOIN soiree2lieu ON lieu.id_lieu = soiree2lieu.id_lieu where id_soiree = ?");
        $stmt->bindParam(1, $idsoiree);
        $stmt->execute();
        $lieu = $stmt->fetch();
        $images = $this->getAllImageFromLieu((int) $lieu['id_lieu']);
        return new Lieu((int) $lieu['id_lieu'], $lieu['nom_lieu'], $lieu['adresse'], (int) $lieu['places_assises'],
            (int) $lieu['places_debout'], $images);
    }

    public function getImagesSpectacle(int $idspectacle) : array{
        $stmt = $this->pdo->prepare("Select image.id_image,nom_image from spectacle2images inner join image on spectacle2images.id_image=image.id_image where id_spectacle = ? ");
        $stmt->bindParam(1, $idspectacle);
        $stmt->execute([$idspectacle]);
        $fetch = $stmt->fetchAll();
        foreach ($fetch as $image){
            $array[] = new Image((int) $image['id_image'], $image['nom_image']);
        }
        return $array;
    }

    public function getArtisteSpectacle(int $idspectacle) : array{
        $stmt = $this->pdo->prepare("SELECT artiste.id_artiste, nom_artiste FROM spectacle2artiste INNER JOIN artiste ON spectacle2artiste.id_artiste = artiste.id_artiste WHERE id_spectacle = ?");
        $stmt->bindParam(1,$idspectacle);
        $stmt->execute();
        $fetch = $stmt->fetchAll();
        foreach ($fetch as $artiste){
            $array[] = new Artiste((int) $artiste['id_artiste'], $artiste['nom_artiste']);
        }
        return $array;
    }
    public function getAllLieux() : array{
        $stmt = $this->pdo->prepare("SELECT * FROM lieu");
        $stmt->execute();
        $result = $stmt->fetchAll();
        foreach ($result as $lieu) {
            $images = $this->getAllImageFromLieu(intval($lieu['id_lieu']));
            $res[] = new Lieu(intval($lieu['id_lieu']), $lieu['nom_lieu'], $lieu['adresse'], intval($lieu['id_lieu']), intval($lieu['id_lieu']), $images);
        }
        return $res;
    }

    public function getAllImageFromLieu(int $id_lieu) : array{
        $stmt = $this->pdo->prepare("
        SELECT * 
        FROM image 
        INNER JOIN lieu2images ON image.id_image = lieu2images.id_image
        WHERE lieu2images.id_lieu = ?");
        $stmt->bindParam(1, $id_lieu, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetchAll();

        foreach ($result as $img) {
            $array[] = new Image((int) $img['id_image'], $img['nom_image']);
        }
        return $array;
    }

    public function addSoiree(String $nomSoiree, String $thematique, String $dateS, String $heureSoiree) : void{
        $stmt = $this->pdo->prepare("INSERT INTO soiree(nom_soiree, thematique, date, horaire_debut) VALUES (?,?,?,?)");
        $stmt->bindParam(1, $nomSoiree, PDO::PARAM_STR);
        $stmt->bindParam(2, $thematique, PDO::PARAM_STR);
        $stmt->bindParam(3, $dateS, PDO::PARAM_STR);
        $stmt->bindParam(4, $heureSoiree, PDO::PARAM_STR);
        echo $nomSoiree . " " . $thematique;
        $stmt->execute();
    }
    public function ajouterSpectacle(string $nom, string $description, string $url, string $horaire, string $style): int {
        $stmt = $this->pdo->prepare("
            INSERT INTO spectacle (nom_spectacle, description, url_video, horaire_previsionnel, style)
            VALUES (:nom, :description, :url, :horaire, :style)
        ");
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':url', $url);
        $stmt->bindParam(':horaire', $horaire);
        $stmt->bindParam(':style', $style);

        $stmt->execute();

        return (int) $this->pdo->lastInsertId();
    }

    public function getPassword(string $email) : string {
        $stmt = $this->pdo->prepare("SELECT password_hash FROM user WHERE email = ?");
        $stmt->bindParam(1, $email);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['password_hash'];
    }
    
    public function addSpec2Soiree(int $idSpec, int $idSoiree) : bool{
        $stmt = $this->pdo->prepare("INSERT INTO soiree2spectacle VALUES (? , ?)");
        $stmt->bindParam(1, $idSoiree);
        $stmt->bindParam(2, $idSpec);
        $succes = $stmt->execute();
        return $succes;
    }
}