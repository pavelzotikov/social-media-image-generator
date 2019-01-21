<?php
declare(strict_types=1);
namespace SocialMediaImageGenerator;

use SocialMediaImageGenerator\Types\Image;
use SocialMediaImageGenerator\Types\Text;
use SocialMediaImageGenerator\Types\AbstractType;

class Generator
{

    private $image;
    private $magnetic;

    private $layers = [];
    private $layers_with_names = [];

    private $layers_counter = 0;

    public function __construct(int $width, int $height, string $color)
    {
        $this->image = new \Imagick();
        $this->image->setResolution(300, 300);
        $this->image->newImage($width, $height, $color);

        $this->magnetic = new Magnetic($this->layers, $this->layers_with_names);
    }

    public function getLayers(): array
    {
        return $this->layers;
    }

    public function loadLayers(array $array): self
    {
        $this->layers = [];
        $this->layers_with_names = [];
        $this->layers_counter = 0;

        foreach ($array as $key => $value) {

            if (isset($value['type'])) {

                $type = $value['type'];

                $reflection_class = new \ReflectionClass($this);

                $class = (new Loader)->getFuncByString($type);
                $class_path = sprintf('%s\Types\%s', $reflection_class->getNamespaceName(), $class);

                if (\class_exists($class_path)) {
                    if (isset($value['properties'])) {

                        /** @var AbstractType $layer */
                        $layer = new $class_path($value['properties']);
                        $this->addLayer($layer);

                    }
                }
            }
        }

        return $this;
    }

    public function addLayer(AbstractType $layer): self
    {
        if ($layer->getName()) {
            $this->layers_with_names[$layer->getName()] = $this->layers_counter;
        }

        $this->layers[$this->layers_counter++] = $this->magnetic->processing($layer);

        return $this;
    }

    public function render(): string
    {
        foreach ($this->layers as $index => $item) {
            $layer = $item->getImage();
            switch (true) {
                case $layer instanceof \Imagick:
                    if ($item instanceof Image && $item->isTransform()) {
                        $layer->setImageVirtualPixelMethod(\Imagick::VIRTUALPIXELMETHOD_TRANSPARENT);
                        $layer->distortImage(\Imagick::DISTORTION_PERSPECTIVE, $item->getTransformCoordinates(), false);
                    }
                    $this->image->compositeImage($layer, \Imagick::COMPOSITE_DISSOLVE, $item->getX(), $item->getY());
                    break;
                case $layer instanceof \ImagickDraw:
                    /** @var $item Text */
                    if ($item->getUnderline()) {
                        foreach ($item->getUnderlineDraws() as $underline) {
                            $this->image->drawImage($underline);
                        }
                    }
                    $this->image->annotateImage($layer, $item->getX(), $item->getY(), 0, $item->getText());
                    break;
                case $layer === null:
                    break;
            }
        }

        // $this->image->normalizeImage();
        $this->image->unsharpMaskImage(0, 0.5, 1, 0.05);

        $this->image->setImageFormat('JPG');
        $this->image->setImageCompression(\Imagick::COMPRESSION_JPEG);
        $this->image->setImageCompressionQuality(95);

        return $this->image->getImageBlob();
    }

}