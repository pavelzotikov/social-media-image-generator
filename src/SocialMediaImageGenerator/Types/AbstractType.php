<?php
declare(strict_types=1);
namespace SocialMediaImageGenerator\Types;

use SocialMediaImageGenerator\Loader;
use SocialMediaImageGenerator\Properties\Magnetic as MagneticProperties;

abstract class AbstractType extends Loader
{
    protected $x = 0;
    protected $y = 0;

    protected $name;

    protected $text;

    protected $width;
    protected $height;

    /** @var MagneticProperties */
    protected $magnetic;
    
    protected $layer;

    public function getImage()
    {
        return null;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function setMagnetic($magnetic): self
    {
        if (is_array($magnetic)) {
            $magnetic = new MagneticProperties($magnetic);
        }

        $this->magnetic = $magnetic;

        return $this;
    }

    public function setX(int $x): self
    {
        $this->x = $x;

        return $this;
    }

    public function setY(int $y): self
    {
        $this->y = $y;

        return $this;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getName(): string
    {
        return $this->name ?: uniqid();
    }

    public function getMagnetic(): ?MagneticProperties
    {
        return $this->magnetic;
    }

    public function getX(): int
    {
        return $this->x;
    }

    public function getY(): int
    {
        return $this->y;
    }

    public function getText(): string
    {
        return $this->text;
    }
}
