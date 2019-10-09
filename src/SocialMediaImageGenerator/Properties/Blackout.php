<?php
declare(strict_types=1);
namespace SocialMediaImageGenerator\Properties;

use SocialMediaImageGenerator\Loader;

class Blackout extends Loader
{
    protected $color = '#000000';
    protected $opacity = .5;

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function getOpacity(): ?float
    {
        return $this->opacity;
    }

    public function setColor(string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function setOpacity(float $opacity): self
    {
        $this->opacity = $opacity;

        return $this;
    }
}
