<?php
declare(strict_types=1);
namespace nrv\nancy\repository;

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

    public function getAllSoiree(){
        $stmt = $this->pdo->prepare("Select * from soiree");
        $stmt->execute();
        $fetch = $stmt->fetchAll();
        return $fetch;
    }

    public function getAllSpectacle(){
        $stmt = $this->pdo->prepare("Select * from spectacle");
        $stmt->execute();
        $fetch = $stmt->fetchAll();
        return $fetch;
    }

    public function getSpectacleSoiree(int $idSoiree){
        $stmt = $this->pdo->prepare("Select spectacle.id_spectacle,nom_spectacle,style,description,horaire_previsionnel,url_video from spectacle inner join soiree2spectacle on spectacle.id_spectacle = soiree2spectacle.id_spectacle where id_soiree = ? ORDER by horaire_previsionnel");
        $stmt->execute([$idSoiree]);
        $fetch = $stmt->fetchAll();
        return $fetch;
    }

    public function getLieuId(int $idsoiree){
        $stmt = $this->pdo->prepare("Select id_lieu from soiree2lieu where id_soiree = ?");
        $stmt->execute([$idsoiree]);
        $fetch = $stmt->fetch();
        if($fetch){
            return $fetch;
        }
        return 0;
    }

    public function getImagesSpectacle(int $idspectacle){
        $stmt = $this->pdo->prepare("Select image.id_image,nom_image from spectacle2images inner join image on spectacle2images.id_image=image.id_image where id_spectacle = ? ");
        $stmt->execute([$idspectacle]);
        $fetch = $stmt->fetchAll();
        return $fetch;
    }

    public function getArtisteSpectacle(int $idspectacle)
    {
        $stmt = $this->pdo->prepare("Select artiste.id_artiste,nom_artiste from spectacle2artiste inner join artiste on spectacle2artiste.id_artiste=artiste.id_artiste where id_spectacle = ?");;
        $stmt->execute([$idspectacle]);
        $fetch = $stmt->fetchAll();
        return $fetch;
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

        foreach ($result as $ligne) {
            $res[] = $ligne['nom_image'];
        }
        if(empty($result)){
            $res[] = "Y'a rien";
        }
        return $res;
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



}