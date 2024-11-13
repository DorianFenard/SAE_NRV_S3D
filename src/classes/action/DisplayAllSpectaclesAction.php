<?php
declare(strict_types=1);

namespace nrv\nancy\action;

use nrv\nancy\repository\NrvRepository;
use nrv\nancy\render\RendererFactory;

class DisplayAllSpectaclesAction extends Action
{
    public function execute(): string
    {
        setlocale(LC_TIME, 'fr_FR.UTF-8', 'fr_FR', 'fr');

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['spectacle_id'])) {
            $spectacleId = (int) $_POST['spectacle_id'];

            $favorites = isset($_COOKIE['favorites']) ? unserialize($_COOKIE['favorites']) : [];

            if (!in_array($spectacleId, $favorites, true)) {
                $favorites[] = $spectacleId;
                setcookie('favorites', serialize($favorites), time() + (60*60*24 * 30), "/");
            }
        }

        $listeLieuSpectacles = $this::getListLieuSpectacle();
        $loginButton = isset($_SESSION['user'])
            ? '<a class="login-button" href="?action=logout">SE DÉCONNECTER</a>'
            : '<a class="login-button" href="?action=login">SE CONNECTER</a>';
        $adminButton = isset($_SESSION['role']) && $_SESSION['role'] === 100
            ? '<a class="admin-button" href="?action=adminpage">ADMIN</a>'
            : '';
        $html = '<header class="program-header">
                    <a class="home" href="?action=default">
                        <img class="program-icon" src="./images/icone.png" alt="NRV">
                    </a> 
                    <div class="menu">
                        <a class="list-button" href="?action=">ACCUEIL</a>
                        <a class="list-button" href="?action=list">MA LISTE</a>
                        <a class="program-button" href="?action=program">PROGRAMME</a>'.
                         $adminButton.
                        $loginButton .'              
                    </div>
                    </header>';
        $html .= '
                <div class="filters">
                    <input type="checkbox" id="toggleButtonDate" class="toggle-button">
                        <button>Filtrer par date</button> 
                    <input type="checkbox" id="toggleButtonLieu" class="toggle-button"> 
                        <button>Filtrer par lieu</button> 
                    <input type="checkbox" id="toggleButtonGenre" class="toggle-button"> 
                        <button>Filtrer par genre</button>  
                <div class ="filtersDate">
                    <ul>';

        foreach ($dates as $dateValue => $dateDisplay) {
            $html .= '<li><a href="index.php?action=program&filter=date&value=' . urlencode($dateValue) . '">' . '<button class="filtersbutton">'. $dateValue .'</button>'. '</a></li>';
        }
        $html .= '</ul></div><div class ="filtersLieu"><ul>';
        foreach ($lieux as $lieu) {
            $html .= '<li><a href="index.php?action=program&filter=lieu&value=' . urlencode($lieu) . '">' .'<button class="filtersbutton">' . htmlspecialchars($lieu).'</button>' . '</a></li>';
        }
        $html .= '</ul></div><div class ="filtersGenre"><ul>';
        foreach ($genres as $genre) {
            $html .= '<li><a href="index.php?action=program&filter=genre&value=' . urlencode($genre) . '">' .'<button class="filtersbutton">'. htmlspecialchars($genre).'</button>' . '</a></li>';
        }
        $html .= '</ul></div></div>';

        // Application des filtres
        $filterType = $_GET['filter'] ?? null;
        $filterValue = $_GET['value'] ?? null;

        if ($filterType && $filterValue) {

                switch ($filterType) {
                    case 'date':
                        unset($listeLieuSpectacles);
                        foreach ($soirees as $soiree) {
                            $lieu = $soiree->lieu;
                            $date = $soiree->date;
                            if($date === $filterValue){
                                // Ajoute chaque couple "lieu-spectacle" à la liste
                                foreach ($soiree->spectacles as $spectacle) {
                                    $listeLieuSpectacles[] = [
                                        'lieu' => $lieu,
                                        'spectacle' => $spectacle,
                                        'date' => $date
                                    ];

                                }
                            }
                        }
                        break;
                    case 'lieu':
                        unset($listeLieuSpectacles);
                        foreach ($soirees as $soiree) {
                            $lieu = $soiree->lieu;
                            $date = $soiree->date;
                            if($lieu->nom===$filterValue){
                                // Ajoute chaque couple "lieu-spectacle" à la liste
                                foreach ($soiree->spectacles as $spectacle) {
                                    $listeLieuSpectacles[] = [
                                        'lieu' => $lieu,
                                        'spectacle' => $spectacle,
                                        'date' => $date
                                    ];
                                }
                            }
                        }
                        break;
                    case 'genre':
                        unset($listeLieuSpectacles);
                        foreach ($soirees as $soiree) {
                            $lieu = $soiree->lieu;
                            $date = $soiree->date;
                            // Ajoute chaque couple "lieu-spectacle" à la liste
                            foreach ($soiree->spectacles as $spectacle) {
                                if($spectacle->style == $filterValue){
                                    $listeLieuSpectacles[] = [
                                        'lieu' => $lieu,
                                        'spectacle' => $spectacle,
                                        'date' => $date
                                    ];
                                }

                            }
                        }
                        break;
                    default:
                        break;
                };
        }

        $html .= '<div class="spectacles">';
        foreach ($listeLieuSpectacles as $spectacle){
            $html.='<h2> Date : '.strftime("%A %d %B %Y",strtotime($spectacle['date'])) .'</h2>';
            $html.=RendererFactory::getRenderer($spectacle['lieu'])->render();
            $html.=RendererFactory::getRenderer($spectacle['spectacle'])->render();

            $favoris = unserialize($_COOKIE['favorites'] ?? 'a:0:{}');

            $isFavorite = in_array($spectacle['spectacle']->id, $favoris ?? [], true);
            //Indique si déjà en favoris ou permet de l'ajouter
            $favoriteButton = $isFavorite ? '<p>Déjà en favoris</p>' :
                '<form method="POST" action="">
                        <input type="hidden" name="spectacle_id" value="' . htmlspecialchars((string)$spectacle['spectacle']->id) . '">
                        <button type="submit">Ajouter aux favoris</button>
                    </form>';
            $html .=$favoriteButton;
        }
        $html .= '</div>';


        return $html;
    }

    /**
     * @return array
     */
    public static function getListLieuSpectacle(): array
    {
        $repo = NrvRepository::getInstance();
        $soirees = $repo->getAllSoiree();

        $dates = [];
        $lieux = [];
        $genres = [];
        foreach ($soirees as $soiree) {
            $dates[$soiree->date] = strftime('%A %d %B %Y', strtotime($soiree->date));
            $lieux[$soiree->lieu->nom] = $soiree->lieu->nom;
            foreach ($soiree->spectacles as $spectacle) {
                $genres[$spectacle->style] = $spectacle->style;
            }
        }

        $listeLieuSpectacles = [];

        foreach ($soirees as $soiree) {
            $lieu = $soiree->lieu;
            $date = $soiree->date;
            // Ajoute chaque couple "lieu-spectacle" à la liste
            foreach ($soiree->spectacles as $spectacle) {
                $listeLieuSpectacles[] = [
                    'lieu' => $lieu,
                    'spectacle' => $spectacle,
                    'date' => $date
                ];
            }
        }
        return $listeLieuSpectacles;
    }
}