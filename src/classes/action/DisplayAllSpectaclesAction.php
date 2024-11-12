<?php
declare(strict_types=1);

namespace nrv\nancy\action;

use nrv\nancy\repository\NrvRepository;
use nrv\nancy\render\RendererFactory;

class DisplayAllSpectaclesAction extends Action
{
    public function execute(): string
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

        $output .= '<div class="spectacles">';
        $output .= implode('', array_map(function ($soiree) {
            $dateFormatted = strftime('%A %d %B %Y', strtotime($soiree->date));

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