<?php
declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use nrv\nancy\render\LieuRenderer;
use nrv\nancy\festival\Lieu;
use nrv\nancy\festival\Soiree;
use nrv\nancy\dispatch\Dispatcher;
use nrv\nancy\festival\Spectacle;
use nrv\nancy\render\SoireeRenderer;



$lieu = new Lieu(1, 'La Cigale', '120, boulevard Rochechouart, 75018 Paris', 120, 130, []);

$lieu2 = Lieu::getLieuById(1);
echo LieuRenderer::render($lieu2);

$soiree = new Soiree(1, 'Soirée de lancement', 'Soirée de lancement du festival', '2021-10-01', '20:00', 1);
$dispatcher = new Dispatcher();
$dispatcher->run();
$spectacleComplet = new Spectacle(
    2,
    'Concert Rock',
    ['Artiste 1', 'Artiste 2'],
    'Un concert exceptionnel de rock',
    ['image1.jpg', 'image2.jpg'],
    'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
    '21:00',
    'Rock',
    false
);

$spectacleSimple = new Spectacle(
    3,
    'Concert Jazz',
    ['Artiste 3', 'Artiste 4'],
    'Un concert exceptionnel de jazz',
    ['image3.jpg', 'image4.jpg'],
    'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
    '22:00',
    'Jazz',
    false
);

$soiree->ajouterSpectacle($spectacleComplet);
$soiree->ajouterSpectacle($spectacleSimple);
echo SoireeRenderer::render($soiree);

