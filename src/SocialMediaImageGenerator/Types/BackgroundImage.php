<?php
declare(strict_types=1);
namespace SocialMediaImageGenerator\Types;

use SocialMediaImageGenerator\Properties\Blackout;

class BackgroundImage extends Background
{
    protected $path = '';
    protected $blackout;

    public function getImage(): \Imagick
    {
        if ($this->layer) {
            return $this->layer;
        }

        $layer = new \Imagick();
        $layer->readImage($this->getPath());

        $layer->setImageInterpolateMethod(\Imagick::INTERPOLATE_BICUBIC);

        $height = $this->getHeight();
        $width = $this->getWidth();

        if ($layer->getImageWidth() > 100 && $layer->getImageHeight() > 100) {
            $ratio = $layer->getImageWidth() / $layer->getImageHeight();

            if ($height * $ratio > $width) {
                $layer->resizeImage((int) ($height * $ratio), $height, \Imagick::FILTER_LANCZOS, 1);
                $layer->cropImage($width, $height, (int) (($layer->getImageWidth() - $width) / 2), 0);
            } else {
                $layer->resizeImage($width, (int) ($width / $ratio), \Imagick::FILTER_LANCZOS, 1);
                $layer->cropImage($width, $height, 0, (int) (($layer->getImageHeight() - $height) / 2));
            }
        } else {
            $layer->adaptiveResizeImage($width, $height, false);
            $layer->blurImage(5, 3);
        }

        if ($this->getBlackout()) {
            $blackout_draw = new \Imagick();
            $blackout_draw->newImage($width, $height, new \ImagickPixel($this->getBlackout()->getColor()));

            if (method_exists($blackout_draw, 'setImageAlpha')) {
                $blackout_draw->setImageAlpha($this->getBlackout()->getOpacity());
            } elseif (method_exists($blackout_draw, 'setImageOpacity')) {
                @$blackout_draw->setImageOpacity($this->getBlackout()->getOpacity());
            }

            $layer->compositeImage($blackout_draw, \Imagick::COMPOSITE_HARDLIGHT, 0, 0);

            $blackout_draw->clear();
            $blackout_draw->destroy();
        }

        $this->layer = $layer;

        return $layer;
    }

    public function setBlackout($blackout): self
    {
        if (is_array($blackout)) {
            $blackout = new Blackout($blackout);
        }

        $this->blackout = $blackout;

        return $this;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getBlackout(): ?Blackout
    {
        return $this->blackout;
    }
}
