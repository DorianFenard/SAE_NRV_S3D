<?php
declare(strict_types=1);

namespace nrv\nancy\action;

use nrv\nancy\repository\NrvRepository;
use nrv\nancy\render\RendererFactory;

class DisplayAllSpectaclesAction extends Action
{
    public function execute(): string
    {
        // Configurer la localisation pour les dates en français


        // Obtenir l'instance de NrvRepository et récupérer toutes les soirées
        $repo = NrvRepository::getInstance();
        $soirees = $repo->getAllSoiree();

        // Extraire toutes les dates, lieux, et genres uniques
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

        // Afficher les filtres sous forme de liens
        $output = '<div class="filters">';
        $output .= '<h3>Filtrer par date</h3><ul>';
        foreach ($dates as $dateValue => $dateDisplay) {
            $output .= '<li><a href="index.php?action=program&filter=date&value=' . urlencode($dateValue) . '">' . htmlspecialchars($dateDisplay) . '</a></li>';
        }
        $output .= '</ul><h3>Filtrer par lieu</h3><ul>';
        foreach ($lieux as $lieu) {
            $output .= '<li><a href="index.php?action=program&filter=lieu&value=' . urlencode($lieu) . '">' . htmlspecialchars($lieu) . '</a></li>';
        }
        $output .= '</ul><h3>Filtrer par genre</h3><ul>';
        foreach ($genres as $genre) {
            $output .= '<li><a href="index.php?action=program&filter=genre&value=' . urlencode($genre) . '">' . htmlspecialchars($genre) . '</a></li>';
        }
        $output .= '</ul></div>';

        // Récupérer les filtres de la requête GET
        $filterType = $_GET['filter'] ?? null;
        $filterValue = $_GET['value'] ?? null;

        // Appliquer les filtres si un filtre est sélectionné
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
            $filteredSoirees = $soirees; // Aucun filtre sélectionné, afficher toutes les soirées
        }

        // Générer le rendu de chaque soirée et spectacle filtrés
        $output .= '<div class="spectacles">';
        $output .= implode('', array_map(function ($soiree) {
            // Formater la date de la soirée en français
            $dateFormatted = strftime('%A %d %B %Y', strtotime($soiree->date));

            // Rendu du lieu et des spectacles de la soirée
            $lieuRenderer = RendererFactory::getRenderer($soiree->lieu)->render();
            $dateRenderer = "<h3>Date : $dateFormatted</h3>";
            $spectaclesRenderer = implode('', array_map(
                fn($spectacle) => RendererFactory::getRenderer($spectacle)->render(),
                $soiree->spectacles
            ));

            return $dateRenderer . $lieuRenderer . $spectaclesRenderer;
        }, $filteredSoirees));
        $output .= '</div>';

        return $output;
    }
}