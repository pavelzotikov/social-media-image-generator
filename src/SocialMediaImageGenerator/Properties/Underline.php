<?php
declare(strict_types=1);
namespace SocialMediaImageGenerator\Properties;

use SocialMediaImageGenerator\Loader;

class Underline extends Loader
{
    protected $color = '#000000';
    protected $space = 3;

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function getSpace(): ?int
    {
        return $this->space;
    }

    public function setColor(string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function setSpace(int $space): self
    {
        $this->space = $space;

        return $this;
    }

}