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
        $loginButton = isset($_SESSION['user'])
            ? '<a class="login-button" href="?action=logout">SE DÉCONNECTER</a>'
            : '<a class="login-button" href="?action=login">SE CONNECTER</a>';

        $html = '<header class="program-header"><a class="home" href="?action=default">
                        <img class="program-icon" src="./images/icone.png" alt="NRV">
                    </a> <div class="menu">
                        <a class="list-button" href="?action=list">MA LISTE</a>
                        <a class="program-button" href="?action=program">PROGRAMME</a>'.
                        $loginButton.'              
                    </div>
                    </header>
                    <div class="filters">';
        $html .= '
  <input type="checkbox" id="toggleButtonDate" class="toggle-button">
   <button>Filtrer par date</button> <input type="checkbox" id="toggleButtonLieu" class="toggle-button"> <button>Filtrer par lieu</button> <input type="checkbox" id="toggleButtonGenre" class="toggle-button"> <button>Filtrer par genre</button>  <div class ="filtersDate"><ul>';

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
            $filteredSoirees = array_filter($soirees, function ($soiree) use ($filterType, $filterValue) {
                switch ($filterType) {
                    case 'date':
                        return $soiree->date === $filterValue;
                    case 'lieu':
                        return stripos($soiree->lieu->nom, $filterValue) !== false;
                    case 'genre':
                        return array_reduce($soiree->spectacles, fn($carry, $spectacle) => $carry || stripos($spectacle->style, $filterValue) !== false, false);
                    default:
                        return true;
                }
            });
        } else {
            $filteredSoirees = $soirees;
        }

        $html .= '<div class="spectacles">';
        $html .= implode('', array_map(function ($soiree) {
            $dateFormatted = strftime('%A %d %B %Y', strtotime($soiree->date));

            $dateRenderer = "<div class ='UnSpectacle'><h3>Date : <a href='index.php?action=program&filter=date&value=" . urlencode($soiree->date) . "'>$dateFormatted</a></h3>";
            $lieuRenderer = RendererFactory::getRenderer($soiree->lieu)->render();
            $spectaclesRenderer = implode('', array_map(function ($spectacle) {
                $favorites = unserialize($_COOKIE['favorites'] ?? 'a:0:{}');
                $isFavorite = in_array($spectacle->id, $favorites ?? [], true);
                $favoriteButton = $isFavorite ? '<p>Déjà en favoris</p>' :
                    '<form method="POST" action="">
                        <input type="hidden" name="spectacle_id" value="' . htmlspecialchars((string)$spectacle->id) . '">
                        <button type="submit">Ajouter aux favoris</button>
                    </form> ';

                $genreLink = "<a href='index.php?action=program&filter=genre&value=" . urlencode($spectacle->style) . "'>" . htmlspecialchars($spectacle->style) . "</a>";
                $affichersoiree = "<a href='index.php?action=soiree&idspectacle=$spectacle->id' >Afficher les autres spectacles de la meme soirée</a>";


                return RendererFactory::getRenderer($spectacle)->render() . "<p>Genre : $genreLink</p>" . $favoriteButton . $affichersoiree ."</div>"  ;
            }, $soiree->spectacles));

            return $dateRenderer . $lieuRenderer . $spectaclesRenderer;
        }, $filteredSoirees));
        $html .= '</div>';


        return $html;
    }
}