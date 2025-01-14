<?php
declare(strict_types=1);
namespace SocialMediaImageGenerator\Types;

use SocialMediaImageGenerator\ImagickResourceLimiter;
use SocialMediaImageGenerator\Properties\Transform;

class Image extends AbstractType
{
    protected $path = '';
    protected $fill = '';

    /** @var Transform */
    protected $transform;

    protected $round_corners = 0;

    /**
     * @throws \ImagickDrawException
     * @throws \ImagickException
     */
    public function getImage(bool $no_resize = false): \Imagick
    {
        if ($this->layer) {
            return $this->layer;
        }

        $layer = new \Imagick();
        ImagickResourceLimiter::applyLimits($layer);

        $layer->readImage($this->getPath());
        $layer->setImageResolution(300, 300);
        $layer->resampleImage(300, 300, \Imagick::FILTER_LANCZOS, 0);

        if (!$no_resize && $this->getWidth() && $this->getHeight()) {
            $layer->adaptiveResizeImage($this->getWidth(), $this->getHeight(), false);
        }

        if ($this->getRoundCorners()) {
            $mask = new \Imagick();
            ImagickResourceLimiter::applyLimits($mask);

            $mask->newImage($this->getWidth(), $this->getHeight(), new \ImagickPixel('transparent'));

            $shape = new \ImagickDraw();
            $shape->setFillColor(new \ImagickPixel('black'));
            $shape->roundRectangle(0, 0, $this->getWidth() - 1, $this->getHeight() - 1, $this->getRoundCorners(), $this->getRoundCorners());

            $mask->drawImage($shape);

            $layer->compositeImage($mask, \Imagick::COMPOSITE_COPYOPACITY, 0, 0);

            $mask->clear();
            $mask->destroy();

            $shape->clear();
            $shape->destroy();
        }

        if ($this->getFill()) {
            $layer_colorize = new \Imagick();
            ImagickResourceLimiter::applyLimits($layer_colorize);

            $layer_colorize->newImage($layer->getImageWidth(), $layer->getImageHeight(), $this->getFill());
            $layer_colorize->compositeImage($layer, \Imagick::COMPOSITE_COPYOPACITY, 0, 0);

            $layer->clear();
            $layer->destroy();

            $layer = $layer_colorize;
        }

        $this->layer = $layer;

        return $layer;
    }

    public function setFill(string $fill): self
    {
        $this->fill = $fill;

        return $this;
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

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function setRoundCorners(int $value): self
    {
        $this->round_corners = $value;

        return $this;
    }

    public function getWidth(): ?int
    {
        return $this->width;
    }

    public function getHeight(): ?int
    {
        return $this->height;
    }

    public function getFill(): ?string
    {
        return $this->fill;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getRoundCorners(): int
    {
        return $this->round_corners;
    }

    public function setTransform($transform): self
    {
        if (is_array($transform)) {
            $transform = new Transform($transform);
        }

        $this->transform = $transform;

        return $this;
    }

    public function getTransform(): Transform
    {
        return $this->transform;
    }

    public function getTransformCoordinates(): array
    {
        list($x1, $y1, $x2, $y2, $x3, $y3, $x4, $y4) = $this->transform->getCoordinates();

        /** @var \Imagick $layer */
        $layer = $this->layer;

        if ($this->transform->getAutoWidth()) {
            $height = sqrt(pow($x4 - $x1, 2) + pow($y4 - $y1, 2));
            $width = ($layer->getImageWidth() * $height) / $layer->getImageHeight();

            $top_line_width = sqrt(pow($x2 - $x1, 2) + pow($y2 - $y1, 2));
            $bottom_line_width = sqrt(pow($x3 - $x4, 2) + pow($y3 - $y4, 2));

            $top_koef = $width / $top_line_width;
            $bottom_koef = $width / $bottom_line_width;

            $x2 = (int) (($x2 - $x1) * $top_koef) + $x1;
            $y2 = (int) (($y2 - $y1) * $top_koef) + $y1;

            $x3 = (int) (($x3 - $x4) * $bottom_koef) + $x4;
            $y3 = (int) (($y3 - $y4) * $bottom_koef) + $y4;
        }

        if ($this->transform->getAutoHeight()) {
            $width = sqrt(pow($x2 - $x1, 2) + pow($y2 - $y1, 2));
            $height = ($layer->getImageHeight() * $width) / $layer->getImageWidth();

            $left_line_width = sqrt(pow($x4 - $x1, 2) + pow($y4 - $y1, 2));
            $right_line_width = sqrt(pow($x3 - $x2, 2) + pow($y3 - $y2, 2));

            $left_koef = $height / $left_line_width;
            $right_koef = $height / $right_line_width;

            $x3 = (int) (($x3 - $x2) * $right_koef) + $x2;
            $y3 = (int) (($y3 - $y2) * $right_koef) + $y2;

            $x4 = (int) (($x4 - $x1) * $left_koef) + $x1;
            $y4 = (int) (($y4 - $y1) * $left_koef) + $y1;
        }

        return [
            0, 0, $x1, $y1, // top left
            0, $layer->getImageHeight(), $x4, $y4, // bottom left
            $layer->getImageWidth(), $layer->getImageHeight(), $x3, $y3, // bottom right
            $layer->getImageWidth(), 0, $x2, $y2 // top right
        ];
    }

    public function isTransform(): bool
    {
        return $this->transform instanceof Transform;
    }
}
