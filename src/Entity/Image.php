<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;

/**
 * Image
 *
 * @ORM\Table(name="image")
 * @ORM\Entity
 */
class Image
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
     * @ORM\Column(name="path", type="string", length=255, nullable=false)
     */
    private $path;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Vinyl", mappedBy="image", cascade={"persist", "remove"})
     */
    private $vinyl;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getVinyl(): ?Vinyl
    {
        return $this->vinyl;
    }

    public function setVinyl(?Vinyl $vinyl): self
    {
        $this->vinyl = $vinyl;

        // set (or unset) the owning side of the relation if necessary
        $newImage = null === $vinyl ? null : $this;
        if ($vinyl->getImage() !== $newImage) {
            $vinyl->setImage($newImage);
        }

        return $this;
    }

}
