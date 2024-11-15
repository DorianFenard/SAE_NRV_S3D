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
    /**
     * @var PDO pdo qui sera utilisé pour les requêtes SQL
     * @var ?NrvRepository instance unique de la classe. (static)
     * @var array éléments de configuration pour se connecter à la BD
     */
    private \PDO $pdo;
    private static ?NrvRepository $instance = null;
    private static array $config = [];

    /**
     * constructeur du repository
     * @param array $conf avec les attributs 'dsn', 'user', 'pass' pour configurer la connexion à la BD
     */
    private function __construct(array $conf) {
        $this->pdo = new \PDO($conf['dsn'], $conf['user'], $conf['pass'],
            [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]);
        $this->pdo->exec("SET NAMES 'utf8'");
    }

    /**
     * Renvoie l'instance de NrvRepository à utiliser car la classe est un singleton
     * @return NrvRepository instance à lire
     */
    public static function getInstance(): NrvRepository {
        if (is_null(self::$instance)) {
            self::$instance = new NrvRepository(self::$config);
        }
        return self::$instance;
    }

    /**
     * Chargement du fichier de config de la BD appelé au lancement du programme dans index.php
     * @param string $file chemin du fichier à charger (config/dbconfig.ini dans l'index)
     * @throws Exception lorsque le fichier est mal lu
     */
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

    /**
     * renvoie toutes les soirées de la base
     * @return array<Soiree> tableau d'objets Soiree.
     */
    public function getAllSoiree() : array{
        $stmt = $this->pdo->prepare("Select * from soiree");
        $stmt->execute();
        $fetch = $stmt->fetchAll();
        foreach ($fetch as $soiree){
            $lieu = $this->getLieuId((int) $soiree['id_soiree']);
            $spectacles = $this->getSpectacleSoiree((int) $soiree['id_soiree']);
            if(!$spectacles){
                $spectacles = [];
            }
            $array[] = new Soiree((int) $soiree['id_soiree'], $soiree['nom_soiree'], $soiree['thematique'], $soiree['date'], $soiree['horaire_debut'], $lieu,
            $spectacles,intval($soiree['tarif']));
        }
        return $array;
    }

    /**
     * renvoie tous les spectacles de la base
     * @return array<Spectacle> tableau d'objets Spectacle
     */
    public function getAllSpectacle() : array{
        $stmt = $this->pdo->prepare("Select * from spectacle");
        $stmt->execute();
        $fetch = $stmt->fetchAll();
        foreach ($fetch as $spectacle){
            $images = $this->getImagesSpectacle((int) $spectacle['id_spectacle']);
            $artistes = $this->getArtisteSpectacle((int) $spectacle['id_spectacle']);
            $array[] = new Spectacle((int) $spectacle['id_spectacle'], $spectacle['nom_spectacle'], $artistes,
                $spectacle['description'], $images, $spectacle['url_video'], $spectacle['horaire_previsionnel'],
                $spectacle['style'], boolval($spectacle['est_annule']),intval($spectacle['duree']) );
        }
        return $array;
    }

    /**
     * renvoies tous les spectacles liés à une soirée passée en paramètre
     * @param int $idSoiree id de la soirée dont on cherche les spectacles
     * @return array<Spectacle> tableau d'objets Spectacle
     */
    public function getSpectacleSoiree(int $idSoiree) : array{
        $stmt = $this->pdo->prepare("Select spectacle.id_spectacle,nom_spectacle,style,description,horaire_previsionnel,url_video,est_annule,duree from spectacle inner join soiree2spectacle on spectacle.id_spectacle = soiree2spectacle.id_spectacle where id_soiree = ? ORDER by horaire_previsionnel");
        $stmt->execute([$idSoiree]);
        $fetch = $stmt->fetchAll();
        $array = [];
        foreach ($fetch as $spec){
            $images = $this->getImagesSpectacle((int) $spec['id_spectacle']);
            $artistes = $this->getArtisteSpectacle((int) $spec['id_spectacle']);
            $array[] = new Spectacle((int) $spec['id_spectacle'], $spec['nom_spectacle'], $artistes,
                $spec['description'], $images, $spec['url_video'], $spec['horaire_previsionnel'],
                $spec['style'], boolval($spec['est_annule']),intval($spec['duree']));
        }
        return $array;
    }

    /**
     * Renvoie la soirée liée à un spectacle passé en paramètre
     * @param int $idspectacle id du spectacle dont on cherche la soirée
     * @return Soiree la soirée à laquelle est lié le spectacle
     */
    public function getSoireeSpectacle(int $idspectacle){
        $stmt = $this->pdo->prepare("Select soiree.id_soiree,nom_soiree,thematique,date,horaire_debut,tarif from soiree2spectacle inner join soiree on soiree2spectacle.id_soiree = soiree.id_soiree where id_spectacle = ? ;");
        $stmt->execute([$idspectacle]);
        $fetch = $stmt->fetch();
        $lieu = $this->getLieuId((intval($fetch['id_soiree'])));
        $spectacles = $this->getSpectacleSoiree(intval($fetch['id_soiree']));
        $soiree = new Soiree(intval($fetch['id_soiree']),$fetch['nom_soiree'],$fetch['thematique'],$fetch['date'],$fetch['horaire_debut'],$lieu,$spectacles,intval($fetch['tarif']));
        return $soiree;
    }

    /**
     * Renvoie le lieu lié à une soirée passée en paramètre
     * @param int $idsoiree id de la soirée dont on cherche l'endroit où elle se déroule
     * @return Lieu lieu où la soirée se déroule
     */
    public function getLieuId(int $idsoiree) : Lieu{
        $stmt = $this->pdo->prepare("Select * from lieu INNER JOIN soiree2lieu ON lieu.id_lieu = soiree2lieu.id_lieu where id_soiree = ?");
        $stmt->bindParam(1, $idsoiree);
        $stmt->execute();
        $lieu = $stmt->fetch();
        if(!$lieu){
            $idlieu = 0;
            $nom = "";
            $adresse ="";
            $nbplacesassises = 0;
            $nbplacesdebout =0;
        }else{
            $idlieu = (int) $lieu['id_lieu'];
            $nom = $lieu['nom_lieu'];
            $adresse = $lieu['adresse'];
            $nbplacesassises = (int) $lieu['places_assises'];
            $nbplacesdebout =(int) $lieu['places_debout'];

        }
        $images = $this->getAllImageFromLieu($idlieu);
        if(!$images){
            $images = [];
        }
        return new Lieu($idlieu, $nom,$adresse,$nbplacesassises ,
            $nbplacesdebout , $images);
    }

    /**
     * renvoie les images liées à un spectacle dans un tableau
     * @param int $idspectacle spectacle dont on cherche les images
     * @return array<Image> tableau contenant les images à renvoyer
     */
    public function getImagesSpectacle(int $idspectacle) : array{
        $stmt = $this->pdo->prepare("Select image.id_image,nom_image from spectacle2images inner join image on spectacle2images.id_image=image.id_image where id_spectacle = ? ");
        $stmt->bindParam(1, $idspectacle);
        $stmt->execute([$idspectacle]);
        $fetch = $stmt->fetchAll();
        $array = [];
        foreach ($fetch as $image){
            $array[] = new Image((int) $image['id_image'], $image['nom_image']);
        }
        return $array;
    }

    /**
     * renvoie les artistes liés à un spectacle passé en paramètre
     * @param int $idspectacle spectacle dont on cherche les artistes
     * @return array<Artiste> tableau des artistes performant au spectacle demandé
     */
    public function getArtisteSpectacle(int $idspectacle) : array{
        $stmt = $this->pdo->prepare("SELECT artiste.id_artiste, nom_artiste FROM spectacle2artiste INNER JOIN artiste ON spectacle2artiste.id_artiste = artiste.id_artiste WHERE id_spectacle = ?");
        $stmt->bindParam(1,$idspectacle);
        $stmt->execute();
        $fetch = $stmt->fetchAll();
        $array = [];
        foreach ($fetch as $artiste){
            $array[] = new Artiste((int) $artiste['id_artiste'], $artiste['nom_artiste']);
        }
        return $array;
    }

    /**
     * renvoie tous les lieux existants
     * @return array<Lieu> tableau d'objets Lieu
     */
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

    /**
     * renvoie toutes les images liées à un lieu passé en paramètre
     * @param int $id_lieu lieu dont on cherche des images
     * @return array<Image> images liées au lieu demandé
     */
    public function getAllImageFromLieu(int $id_lieu) : array{
        $stmt = $this->pdo->prepare("
        SELECT * 
        FROM image 
        INNER JOIN lieu2images ON image.id_image = lieu2images.id_image
        WHERE lieu2images.id_lieu = ?");
        $stmt->bindParam(1, $id_lieu, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetchAll();
        $array = [];
        foreach ($result as $img) {
            $array[] = new Image((int) $img['id_image'], $img['nom_image']);
        }
        return $array;
    }

    /**
     * ajoute une soirée à la base
     * @param String $nomSoiree
     * @param String $thematique
     * @param String $dateS
     * @param String $heureSoiree
     */
    public function addSoiree(String $nomSoiree, String $thematique, String $dateS, String $heureSoiree) : void{
        $stmt = $this->pdo->prepare("INSERT INTO soiree(nom_soiree, thematique, date, horaire_debut) VALUES (?,?,?,?)");
        $stmt->bindParam(1, $nomSoiree, PDO::PARAM_STR);
        $stmt->bindParam(2, $thematique, PDO::PARAM_STR);
        $stmt->bindParam(3, $dateS, PDO::PARAM_STR);
        $stmt->bindParam(4, $heureSoiree, PDO::PARAM_STR);
        echo $nomSoiree . " " . $thematique;
        $stmt->execute();
    }

    /**
     * ajoute un spectacle à la base
     * @param string $nom
     * @param string $description
     * @param int $duree en minutes
     * @param string $url
     * @param string $horaire
     * @param string $style
     * @return int id du spectacle inseré
     */
    public function ajouterSpectacle(string $nom, string $description, int $duree, string $url, string $horaire, string $style): int {
        $stmt = $this->pdo->prepare("
            INSERT INTO spectacle (nom_spectacle, description, duree ,url_video, horaire_previsionnel, style)
            VALUES (:nom, :description, :duree, :url, :horaire, :style)
        ");
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':url', $url);
        $stmt->bindParam(':horaire', $horaire);
        $stmt->bindParam(':style', $style);
        $stmt->bindParam(':duree', $duree);

        $stmt->execute();

        return (int) $this->pdo->lastInsertId();
    }

    /**
     * renvoie le mot de passe hashé lié au mail passé en paramètre
     * @param string $email e-mail de l'utilisateur dont on cherche le mot de passe
     * @return string mot de passe hashé de l'utilisateur correspondant à l'e-mail
     */
    public function getPassword(string $email) : string {
        $stmt = $this->pdo->prepare("SELECT password_hash FROM user WHERE email = ?");
        $stmt->bindParam(1, $email);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['password_hash'];
    }

    /**
     * ajoute une référence entre une soirée et un spectacle
     * @param int $idSpec id du spectacle à lier
     * @param int $idSoiree id de la soirée à lier
     * @return bool true si l'opération a réussi
     */
    public function addSpec2Soiree(int $idSpec, int $idSoiree) : bool{
        $stmt = $this->pdo->prepare("INSERT INTO soiree2spectacle VALUES (? , ?)");
        $stmt->bindParam(1, $idSoiree);
        $stmt->bindParam(2, $idSpec);
        $succes = $stmt->execute();
        return $succes;
    }

    /**
     * change le statut du spectacle annulé / maintenu
     * @param int $idspec id du spectacle dont on va modifier le statut
     * @param bool $nouvBool nouveau statut du spectacle (true si annulé)
     * @return bool true si l'opération a réussi
     */
    public function changerAnnulation(int $idspec, bool $nouvBool) : bool{
        $stmt = $this->pdo->prepare("UPDATE spectacle SET est_annule = ? WHERE id_spectacle = ?");
        $stmt->bindParam(1, $nouvBool);
        $stmt->bindParam(2, $idspec);
        $succes = $stmt->execute();
        return $succes;
    }

    /**
     * renvoie le rôle d'un utilisateur
     * @param string $email e-mail de l'utilisateur dont on cherche le rôle
     * @return int rôle de l'utilisateur (100 si admin)
     */
    public function getRoleByUser(string $email) : int {
        $stmt = $this->pdo->prepare("SELECT role FROM user WHERE email = ?");
        $stmt->bindParam(1, $email);
        $stmt->execute();
        $result = $stmt->fetch();
        return (int) $result['role'];
    }

    /**
     * vérifie si un utilisateur existe déjà
     * @param string $email e-mail de l'utilisateur à vérifier
     * @return bool true si l'utilisateur existe déjà
     */
    public function userAlreadyExisting(string $email) : bool{
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as exist FROM user WHERE email = ?");
        $stmt->bindParam(1, $email);
        $stmt->execute();
        $res = $stmt->fetch();
        return ((int) $res['exist']) > 0 ;
    }

    /**
     * ajoute un nouvel utilisateur dans la base
     * @param string $email e-mail de l'utilisateur à ajouter
     * @param string $pass mot de passe de l'utilisateur (déjà hashé) à ajouter
     */
    public function addNewUser(string $email, string $pass) {
        $stmt = $this->pdo->prepare("INSERT INTO user(password_hash, email, role) VALUES (?, ?, 50)");
        $stmt->bindParam(1, $pass);
        $stmt->bindParam(2, $email);
        $stmt->execute();
    }
}