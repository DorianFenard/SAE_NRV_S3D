<?php
declare(strict_types=1);
namespace nrv\nancy\action;

/**
 * classe abstraiste Action servant de modèle pour toutes les actions
 */
abstract class Action{
    /**
     * @var ?string $http_method méthode HTTP utilisée (GET ou POST), dans les formulaires, cet attribut nous dit si on doit afficher le formulaire ou bien exécuter son résultat
     * @var ?string $hostname domaine de l'hôte
     * @var ?string $script_name chemin du script courant
     */
    protected ?string $http_method = null;
    protected ?string $hostname = null;
    protected ?string $script_name = null;

    /**
     * constructeur initialisant tous les attributs grâce à la variable $_SERVER
     */
    public function __construct(){

        $this->http_method = $_SERVER['REQUEST_METHOD'];
        $this->hostname = $_SERVER['HTTP_HOST'];
        $this->script_name = $_SERVER['SCRIPT_NAME'];
    }

    /**
     * Exécution de l'action générant un affichage
     * @return string affichage géneré par la méthode
     */
    abstract public function execute() : string;

}