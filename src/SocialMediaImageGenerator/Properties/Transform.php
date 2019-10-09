<?php
declare(strict_types=1);
namespace SocialMediaImageGenerator\Properties;

use SocialMediaImageGenerator\Loader;

class Transform extends Loader
{
    // top left
    protected $x1;
    protected $y1;

    // top right
    protected $x2;
    protected $y2;

    // bottom right
    protected $x3;
    protected $y3;

    // bottom left
    protected $x4;
    protected $y4;

    protected $auto_width = false;
    protected $auto_height = false;

    public function setTopLeft(int $x, int $y): self
    {
        $this->x1 = $x;
        $this->y1 = $y;

        return $this;
    }

    public function setTopRight(int $x, int $y): self
    {
        $this->x2 = $x;
        $this->y2 = $y;

        return $this;
    }

    public function setBottomRight(int $x, int $y): self
    {
        $this->x3 = $x;
        $this->y3 = $y;

        return $this;
    }

    public function setBottomLeft(int $x, int $y): self
    {
        $this->x4 = $x;
        $this->y4 = $y;

        return $this;
    }

    public function setAutoWidth(bool $value)
    {
        $this->auto_width = $value;

        return $this;
    }

    public function setAutoHeight(bool $value)
    {
        $this->auto_height = $value;

        return $this;
    }

    public function getAutoWidth()
    {
        if ($this->auto_height) {
            return false;
        }

        return $this->auto_width;
    }

    public function getAutoHeight()
    {
        if ($this->auto_width) {
            return false;
        }

        return $this->auto_height;
    }

    public function getCoordinates(): array
    {
        return [
            $this->x1,
            $this->y1,
            $this->x2,
            $this->y2,
            $this->x3,
            $this->y3,
            $this->x4,
            $this->y4
        ];
    }
}
