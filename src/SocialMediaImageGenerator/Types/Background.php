<?php
declare(strict_types=1);
namespace SocialMediaImageGenerator\Types;

class Background extends AbstractType
{
    protected $color = '#FFFFFF';

    public function getImage(): \Imagick
    {
        if ($this->layer) {
            return $this->layer;
        }

        $layer = new \Imagick();
        $layer->newImage($this->getWidth(), $this->getHeight(), $this->getColor());

        $this->layer = $layer;

        return $layer;
    }

    public function setWidth(int $width): self
    {
        $this->width = $width;

        return $this;
    }

    public function setHeight(int $height): self
    {
        $this->height = $height;

        return $this;
    }

    public function setColor(string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function getColor(): string
    {
        return $this->color;
    }
}
