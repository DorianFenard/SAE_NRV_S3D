<?php
declare(strict_types=1);

namespace nrv\nancy\festival;

class Spectacle
{
    private int $id;
    private string $titre;
    private array $artistes;
    private string $description;
    private array $images;
    private ?string $urlVideo;
    private string $horairePrevisionnel;
    private string $style;
    private bool $estAnnule;

    public function __construct(int $id, string $titre, array $artistes, string $description, array $images, ?string $urlVideo, string $horairePrevisionnel, string $style, bool$estAnnule)
    {
        if(isset($url)){
            $url ="";
        }
        $this->id = $id;
        $this->titre = $titre;
        $this->artistes = $artistes;
        $this->description = $description;
        $this->images = $images;
        $this->urlVideo = $urlVideo;
        $this->horairePrevisionnel = $horairePrevisionnel;
        $this->style = $style;
        $this->estAnnule = $estAnnule;
    }

    public function annuler(): void
    {
        $this->estAnnule = true;
    }

    public function __get(string $property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        } else {
            throw new \Exception("Property '{$property}' does not exist in class " . __CLASS__);
        }
    }
}