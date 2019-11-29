<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\ExclusionPolicy;

/**
 * Vinyl
 *
 * @ORM\Table(name="vinyl", uniqueConstraints={@ORM\UniqueConstraint(name="UNIQ_E2E531D3DA5256D", columns={"image_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\VinylRepository")
 */
class Vinyl
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
     * @ORM\Column(name="artist", type="string", length=100, nullable=false)
     */
    private $artist;

    /**
     * @var string
     *
     * @ORM\Column(name="title_album", type="string", length=100, nullable=false)
     */
    private $title_album;

    /**
     * @var string
     *
     * @ORM\Column(name="label", type="string", length=50, nullable=false)
     */
    private $label;

    /**
     * @var string
     *
     * @ORM\Column(name="country", type="string", length=50, nullable=false)
     */
    private $country;

    /**
     * @var string
     *
     * @ORM\Column(name="cat_nb", type="string", length=50, nullable=false)
     */
    private $cat_nb;

    /**
     * @var string
     *
     * @ORM\Column(name="year_original", type="string", length=4, nullable=false)
     */
    private $year_original;

    /**
     * @var string
     *
     * @ORM\Column(name="year_edition", type="string", length=4, nullable=false)
     */
    private $year_edition;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Song", cascade={"persist", "remove"}, mappedBy="vinyl")
     */
    private $songs;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Image", inversedBy="vinyl", cascade={"persist", "remove"})
     */
    private $image;

    public function __construct()
    {
        $this->songs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getTitlealbum(): ?string
    {
        return $this->title_album;
    }

    public function setTitlealbum(string $title_album): self
    {
        $this->title_album = $title_album;

        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getCatnb(): ?string
    {
        return $this->cat_nb;
    }

    public function setCatnb(string $cat_nb): self
    {
        $this->cat_nb = $cat_nb;

        return $this;
    }

    public function getYearoriginal(): ?string
    {
        return $this->year_original;
    }

    public function setYearoriginal(string $year_original): self
    {
        $this->year_original = $year_original;

        return $this;
    }

    public function getYearedition(): ?string
    {
        return $this->year_edition;
    }

    public function setYearedition(string $year_edition): self
    {
        $this->year_edition = $year_edition;

        return $this;
    }

    /**
     * @return Collection|Songs[]
     */
    public function getSongs(): Collection
    {
        return $this->songs;
    }

    public function addSongs(Song $song): self
    {
        if (!$this->songs->contains($song)) {
            $this->songs[] = $song;
            $song->setVinyl($this);
        }

        return $this;
    }

    public function removeSong(Song $song): self
    {
        if ($this->songs->contains($song)) {
            $this->song->removeElement($song);
            // set the owning side to null (unless already changed)
            if ($song->getVinyl() === $this) {
                $song->setVinyl(null);
            }
        }

        return $this;
    }

    public function getImage(): ?Image
    {
        return $this->image;
    }

    public function setImage(?Image $image): self
    {
        $this->image = $image;

        return $this;
    }


}
