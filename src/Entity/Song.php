<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;

/**
 * Song
 *
 * @ORM\Table(name="song", indexes={@ORM\Index(name="song_vinyl_FK", columns={"vinyl_id"})})
 * @ORM\Entity
 */
class Song
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title_song", type="string", length=100, nullable=false)
     */
    private $title_song;

    /**
     * @var string
     *
     * @ORM\Column(name="artist", type="string", length=100, nullable=false)
     */
    private $artist;

    /**
     * @var string|null
     *
     * @ORM\Column(name="alternate_info", type="string", length=50, nullable=true)
     */
    private $alternate_info;

    /**
     * @var string|null
     *
     * @ORM\Column(name="feat", type="string", length=50, nullable=true)
     */
    private $featuring;

    /**
     * @var string
     *
     * @ORM\Column(name="title_album", type="string", length=100, nullable=false)
     */
    private $title_album;

    /**
     * @var string
     *
     * @ORM\Column(name="side", type="string", length=2, nullable=false)
     */
    private $side;

    /**
     * @var int
     *
     * @ORM\Column(name="position", type="integer", nullable=false)
     */
    private $position;

    /**
     * @var \Vinyl
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Vinyl", inversedBy="songs")
     * @ORM\JoinColumn(name="vinyl_id", referencedColumnName="id")
     */
    private $vinyl;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitlesong(): ?string
    {
        return $this->title_song;
    }

    public function setTitlesong(string $title_song): self
    {
        $this->title_song = $title_song;

        return $this;
    }

    public function getArtist(): ?string
    {
        return $this->artist;
    }

    public function setArtist(string $artist): self
    {
        $this->artist = $artist;

        return $this;
    }

    public function getAlternateinfo(): ?string
    {
        return $this->alternate_info;
    }

    public function setAlternateinfo(?string $alternate_info): self
    {
        $this->alternate_info = $alternate_info;

        return $this;
    }

    public function getFeaturing(): ?string
    {
        return $this->featuring;
    }

    public function setFeaturing(?string $featuring): self
    {
        $this->featuring = $featuring;

        return $this;
    }

    public function getTitlealbum(): ?string
    {
        return $this->title_album;
    }

    public function setTitlealbum(string $title_album): self
    {
        $this->title_album = $title_album;

        return $this;
    }

    public function getSide(): ?string
    {
        return $this->side;
    }

    public function setSide(string $side): self
    {
        $this->side = $side;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getVinyl(): ?Vinyl
    {
        return $this->vinyl;
    }

    public function setVinyl(?Vinyl $vinyl): self
    {
        $this->vinyl = $vinyl;

        return $this;
    }
}
