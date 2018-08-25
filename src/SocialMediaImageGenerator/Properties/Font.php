<?php
declare(strict_types=1);
namespace SocialMediaImageGenerator\Properties;

use SocialMediaImageGenerator\Loader;

class Font extends Loader
{
    protected $size = 22;
    protected $color = '#000000';
    protected $file;
    protected $interline_spacing;
    protected $antialias = true;

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function getFile(): ?string
    {
        return $this->file;
    }

    public function getInterlineSpacing(): ?int
    {
        return $this->interline_spacing;
    }

    public function getAntialias(): ?bool
    {
        return $this->antialias;
    }

    public function setSize(int $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function setColor(string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function setFile(string $file): self
    {
        $this->file = $file;

        return $this;
    }

    public function setInterlineSpacing(int $interline_spacing): self
    {
        $this->interline_spacing = $interline_spacing;

        return $this;
    }

    public function setAntialias(bool $antialias): self
    {
        $this->antialias = $antialias;

        return $this;
    }

}