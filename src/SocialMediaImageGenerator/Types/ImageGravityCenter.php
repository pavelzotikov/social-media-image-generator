<?php
declare(strict_types=1);
namespace SocialMediaImageGenerator\Types;

use SocialMediaImageGenerator\ImagickResourceLimiter;

class ImageGravityCenter extends Image
{
    public function getImage(bool $no_resize = true): \Imagick
    {
        if ($this->layer) {
            return $this->layer;
        }

        $layer = parent::getImage($no_resize);

        $this->x = ($this->width - $layer->getImageWidth()) / 2;
        $this->y = ($this->height - $layer->getImageHeight()) / 2;

        if ($this->fill) {
            $layer_colorize = new \Imagick();
            ImagickResourceLimiter::applyLimits($layer_colorize);

            $layer_colorize->newImage($layer->getImageWidth(), $layer->getImageHeight(), $this->fill);
            $layer_colorize->compositeImage($layer, \Imagick::COMPOSITE_COPYOPACITY, 0, 0);
            $layer = $layer_colorize;

            unset($layer_colorize);
        }

        $this->layer = $layer;

        return $layer;
    }
}
