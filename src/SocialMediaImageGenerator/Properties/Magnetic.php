<?php
declare(strict_types=1);
namespace SocialMediaImageGenerator\Properties;

use SocialMediaImageGenerator\Loader;

class Magnetic extends Loader
{
    protected $to_layer;

    protected $left;
    protected $top;
    protected $right;
    protected $bottom;

    protected $vertical_center = false;
    protected $horizontal_center = false;

    public function getToLayer(): ?string
    {
        return $this->to_layer;
    }

    public function getLeft(): ?int
    {
        return $this->left;
    }

    public function getTop(): ?int
    {
        return $this->top;
    }

    public function getRight(): ?int
    {
        return $this->right;
    }

    public function getBottom(): ?int
    {
        return $this->bottom;
    }

    public function getVerticalCenter(): bool
    {
        return $this->vertical_center;
    }

    public function getHorizontalCenter(): bool
    {
        return $this->horizontal_center;
    }

    public function setToLayer(string $to_layer): self
    {
        $this->to_layer = $to_layer;

        return $this;
    }

    public function setLeft(int $left): self
    {
        $this->left = $left;

        return $this;
    }

    public function setTop(int $top): self
    {
        $this->top = $top;

        return $this;
    }

    public function setRight(int $right): self
    {
        $this->right = $right;

        return $this;
    }

    public function setBottom(int $bottom): self
    {
        $this->bottom = $bottom;

        return $this;
    }

    public function setVerticalCenter(bool $center): self
    {
        $this->vertical_center = $center;

        return $this;
    }

    public function setHorizontalCenter(bool $center): self
    {
        $this->horizontal_center = $center;

        return $this;
    }
}
