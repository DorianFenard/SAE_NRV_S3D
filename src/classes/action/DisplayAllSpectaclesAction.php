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
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Gestion de l'ajout aux favoris
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['spectacle_id'])) {
            $spectacleId = (int) $_POST['spectacle_id'];
            $_SESSION['favorites'] = $_SESSION['favorites'] ?? [];
            if (!in_array($spectacleId, $_SESSION['favorites'], true)) {
                $_SESSION['favorites'][] = $spectacleId;
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

        // Générer les filtres
        $html = '<div class="filters">';
        $html .= '<h3>Filtrer par date</h3><ul>';
        foreach ($dates as $dateValue => $dateDisplay) {
            $html .= '<li><a href="index.php?action=program&filter=date&value=' . urlencode($dateValue) . '">' . htmlspecialchars($dateDisplay) . '</a></li>';
        }
        $html .= '</ul><h3>Filtrer par lieu</h3><ul>';
        foreach ($lieux as $lieu) {
            $html .= '<li><a href="index.php?action=program&filter=lieu&value=' . urlencode($lieu) . '">' . htmlspecialchars($lieu) . '</a></li>';
        }
        $html .= '</ul><h3>Filtrer par genre</h3><ul>';
        foreach ($genres as $genre) {
            $html .= '<li><a href="index.php?action=program&filter=genre&value=' . urlencode($genre) . '">' . htmlspecialchars($genre) . '</a></li>';
        }
        $html .= '</ul></div>';

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

        // Rendu des spectacles
        $html .= '<div class="spectacles">';
        $html .= implode('', array_map(function ($soiree) {
            $dateFormatted = strftime('%A %d %B %Y', strtotime($soiree->date));

            $lieuRenderer = RendererFactory::getRenderer($soiree->lieu)->render();
            $dateRenderer = "<h3>Date : $dateFormatted</h3>";
            $spectaclesRenderer = implode('', array_map(function ($spectacle) {

                //Vérifie si le spectacle est déjà en favoris
                $isFavorite = in_array($spectacle->id, $_SESSION['favorites'] ?? [], true);
                //Indique si déjà en favoris ou permet de l'ajouter
                $favoriteButton = $isFavorite ? '<p>Déjà en favoris</p>' :
                    '<form method="POST" action="">
                        <input type="hidden" name="spectacle_id" value="' . htmlspecialchars((string)$spectacle->id) . '">
                        <button type="submit">Ajouter aux favoris</button>
                    </form>';

                return RendererFactory::getRenderer($spectacle)->render() . $favoriteButton;
            }, $soiree->spectacles));

            return $dateRenderer . $lieuRenderer . $spectaclesRenderer;
        }, $filteredSoirees));
        $html .= '</div>';

        return $html;
    }
}