<?php
declare(strict_types=1);
namespace SocialMediaImageGenerator;

use SocialMediaImageGenerator\Types\AbstractType;
use SocialMediaImageGenerator\Properties\Magnetic as MagneticProperties;

class Magnetic
{
    private $im;

    private $layers;
    private $layers_with_names;

    /** @var AbstractType */
    private $to_layer;

    /**
     * @throws \ImagickException
     */
    public function __construct(array &$layers, array &$layers_with_names)
    {
        $this->im = new \Imagick();
        ImagickResourceLimiter::applyLimits($this->im);

        $this->layers = &$layers;
        $this->layers_with_names = &$layers_with_names;
    }

    public function processing(AbstractType $layer): AbstractType
    {
        if (!($layer->getMagnetic() instanceof MagneticProperties)) {
            return $layer;
        }

        $to_layer_name = $layer->getMagnetic()->getToLayer();

        if (!isset($this->layers_with_names[$to_layer_name])) {
            return $layer;
        }

        /** @var AbstractType $to_layer */
        $this->to_layer = $this->layers[$this->layers_with_names[$to_layer_name]];

        if ($layer->getMagnetic()->getHorizontalCenter()) {
            $this->horizontalCenter($layer);
        } else {
            if ($layer->getMagnetic()->getLeft() !== null) {
                $this->left($layer);
            } elseif ($layer->getMagnetic()->getRight() !== null) {
                $this->right($layer);
            }
        }

        if ($layer->getMagnetic()->getVerticalCenter()) {
            $this->verticalCenter($layer);
        } else {
            if ($layer->getMagnetic()->getTop() !== null) {
                $this->top($layer);
            } elseif ($layer->getMagnetic()->getBottom() !== null) {
                $this->bottom($layer);
            }
        }

        return $layer;
    }

    private function horizontalCenter(AbstractType &$layer)
    {
        if ($this->to_layer->getImage() instanceof \Imagick) {
            if ($layer->getImage() instanceof \Imagick) {
                $layer->setX(
                    $this->to_layer->getX()
                    + (int) ($this->to_layer->getImage()->getImageWidth() / 2)
                    - (int) ($layer->getImage()->getImageWidth() / 2)
                );
            } else {

                /*$im = new \Imagick();
                ImagickResourceLimiter::applyLimits($im);
                $layer_info = $im->queryFontMetrics($layer->$this->getImage(), $layer->getText());*/

                $layer->setX(
                    (int) ($this->to_layer->getX() + $this->to_layer->getImage()->getImageWidth() / 2)
                );

                /*if ($layer->getAlignment() === \Imagick::ALIGN_LEFT) {
                    $layer->setX($layer->getX() - (int) ($layer_info['textWidth'] / 2));
                } elseif ($layer->getAlignment() === \Imagick::ALIGN_RIGHT) {
                    $layer->setX($layer->getX() + (int) ($layer_info['textWidth'] / 2));
                }*/
            }
        } elseif ($this->to_layer->getImage() instanceof \ImagickDraw) {
            if ($layer->getImage() instanceof \Imagick) {

                /*$im = new \Imagick();
                ImagickResourceLimiter::applyLimits($im);
                $to_layer_info = $im->queryFontMetrics($this->to_layer->getImage(), $this->to_layer->getText());*/

                $layer->setX(
                    $this->to_layer->getX()
                    - (int) ($layer->getImage()->getImageWidth() / 2)
                );

            /*if ($this->to_layer->getAlignment() === \Imagick::ALIGN_LEFT) {
                $layer->setX($layer->getX() + (int) ($to_layer_info['textWidth'] / 2));
            } elseif ($this->to_layer->getAlignment() === \Imagick::ALIGN_RIGHT) {
                $layer->setX($layer->getX() - (int) ($to_layer_info['textWidth'] / 2));
            }*/
            } else {
                $layer->setX(
                    $this->to_layer->getX()
                );
            }
        }

        $layer->setX($layer->getX() + (int) ($layer->getMagnetic()->getLeft() ?: $layer->getMagnetic()->getRight()));

        unset($layer);
    }

    private function left(AbstractType &$layer)
    {
        $layer->setX(
            $this->to_layer->getX() + $layer->getMagnetic()->getLeft()
        );

        /*if ($layer_image instanceof \ImagickDraw) {

            $im = new \Imagick();
            ImagickResourceLimiter::applyLimits($im);
            $layer_info = $im->queryFontMetrics($layer_image, $layer->getText());

            if ($layer->getAlignment() === \Imagick::ALIGN_CENTER) {
                $layer->setX($layer->getX() + (int) ($layer_info['textWidth'] / 2));
            } elseif ($layer->getAlignment() === \Imagick::ALIGN_RIGHT) {
                $layer->setX($layer->getX() + (int) ($layer_info['textWidth']));
            }

        }*/

        unset($layer);
    }

    private function right(AbstractType &$layer)
    {
        if ($this->to_layer->getImage() instanceof \Imagick) {
            $layer->setX(
                $this->to_layer->getX() + $this->to_layer->getImage()->getImageWidth() + (int) $layer->getMagnetic()->getRight()
            );

        /*if ($layer_image instanceof \ImagickDraw) {

            $im = new \Imagick();
            ImagickResourceLimiter::applyLimits($im);
            $layer_info = $im->queryFontMetrics($layer_image, $layer->getText());

            if ($layer->getAlignment() === \Imagick::ALIGN_CENTER) {
                $layer->setX($layer->getX() + (int) ($layer_info['textWidth'] / 2));
            } elseif ($layer->getAlignment() === \Imagick::ALIGN_RIGHT) {
                $layer->setX($layer->getX() + (int) ($layer_info['textWidth']));
            }

        }*/
        } elseif ($this->to_layer->getImage() instanceof \ImagickDraw) {
            $to_layer_info = $this->im->queryFontMetrics($this->to_layer->getImage(), $this->to_layer->getText());

            $layer->setX(
                $this->to_layer->getX() + (int) $to_layer_info['textWidth'] + (int) $layer->getMagnetic()->getRight()
            );

            /*if ($layer_image instanceof \ImagickDraw) {

                $layer_info = $im->queryFontMetrics($layer_image, $layer->getText());

                if ($layer->getAlignment() === \Imagick::ALIGN_CENTER) {
                    $layer->setX($layer->getX() + (int) ($layer_info['textWidth'] / 2));
                } elseif ($layer->getAlignment() === \Imagick::ALIGN_RIGHT) {
                    $layer->setX($layer->getX() + (int) ($layer_info['textWidth']));
                }

            }

            if ($to_layer->getAlignment() === \Imagick::ALIGN_CENTER) {
                $layer->setX($layer->getX() - (int) ($to_layer_info['textWidth'] / 2));
            } elseif ($to_layer->getAlignment() === \Imagick::ALIGN_RIGHT) {
                $layer->setX($layer->getX() - (int) ($to_layer_info['textWidth']));
            }*/
        }

        unset($layer);
    }

    /**
     * @throws \ImagickException
     */
    private function verticalCenter(AbstractType &$layer)
    {
        if ($this->to_layer->getImage()instanceof \Imagick) {
            if ($layer->getImage() instanceof \Imagick) {
                $layer->setY((int) (
                    $this->to_layer->getY() + $this->to_layer->getImage()->getImageHeight() / 2
                    - $layer->getImage()->getImageHeight() / 2
                ));
            } else {
                $im = new \Imagick();
                ImagickResourceLimiter::applyLimits($im);

                $layer_info = $im->queryFontMetrics($layer->getImage(), $layer->getText());

                $layer->setY((int) (
                    $this->to_layer->getY()
                    + (int) ($this->to_layer->getImage()->getImageHeight() / 2)
                    - (int) ($layer->getCurrentNumberOfLines() * ($layer_info['ascender'] - $layer_info['descender']) / 2)
                    + (int) ($layer_info['ascender'])
                ));
            }
        } elseif ($this->to_layer->getImage() instanceof \ImagickDraw) {
            if ($layer->getImage() instanceof \Imagick) {
                $im = new \Imagick();
                ImagickResourceLimiter::applyLimits($im);

                $to_layer_info = $im->queryFontMetrics($this->to_layer->getImage(), $this->to_layer->getText());

                $layer->setY((int) (
                    $this->to_layer->getY()
                    + (int) (($this->to_layer->getCurrentNumberOfLines() * ($to_layer_info['ascender'] - $to_layer_info['descender']) + $to_layer_info['descender']) / 2)
                    - (int) ($layer->getImage()->getImageHeight() / 2)
                    - (int) ($to_layer_info['ascender'])
                ));
            } else {
                $im = new \Imagick();
                ImagickResourceLimiter::applyLimits($im);

                $layer_info = $im->queryFontMetrics($layer->getImage(), $layer->getText());
                $to_layer_info = $im->queryFontMetrics($this->to_layer->getImage(), $this->to_layer->getText());

                $layer->setY((int) (
                    $this->to_layer->getY()
                    + (int) (($this->to_layer->getCurrentNumberOfLines() * ($to_layer_info['ascender'] - $to_layer_info['descender']) + $to_layer_info['descender']) / 2)
                    - (int) (($layer->getCurrentNumberOfLines() * ($layer_info['ascender'] - $layer_info['descender']) + $layer_info['descender']) / 2)
                ));
            }
        }

        $layer->setY($layer->getY() + (int) ($layer->getMagnetic()->getTop() ?: $layer->getMagnetic()->getBottom()));

        unset($layer);
    }

    private function top(AbstractType &$layer)
    {
        if ($this->to_layer->getImage() instanceof \Imagick) {
            if ($layer->getImage() instanceof \Imagick) {
                $layer->setY((int) (
                    $this->to_layer->getY() + (int) $layer->getMagnetic()->getTop()
                ));
            } elseif ($layer->getImage() instanceof \ImagickDraw) {
                $im = new \Imagick();
                ImagickResourceLimiter::applyLimits($im);

                $layer_info = $im->queryFontMetrics($layer->getImage(), $layer->getText());

                $layer->setY((int) (
                    $this->to_layer->getY() + (int) $layer_info['ascender'] + (int) $layer->getMagnetic()->getTop()
                ));
            }
        } elseif ($this->to_layer->getImage() instanceof \ImagickDraw) {
            if ($layer->getImage() instanceof \Imagick) {
                $im = new \Imagick();
                ImagickResourceLimiter::applyLimits($im);

                $to_layer_info = $im->queryFontMetrics($this->to_layer->getImage(), $this->to_layer->getText());

                $layer->setY((int) (
                    $this->to_layer->getY()
                    - (int) $to_layer_info['ascender']
                    + (int) $layer->getMagnetic()->getTop()
                ));
            } elseif ($layer->getImage() instanceof \ImagickDraw) {
                $layer->setY((int) (
                    $this->to_layer->getY() + (int) $layer->getMagnetic()->getTop()
                ));
            }
        }

        unset($layer);
    }

    private function bottom(AbstractType &$layer)
    {
        if ($this->to_layer->getImage() instanceof \Imagick) {
            if ($layer->getImage() instanceof \Imagick) {
                $layer->setY((int) (
                    $this->to_layer->getY() + (int) $this->to_layer->getImage()->getImageHeight() + (int) $layer->getMagnetic()->getBottom()
                ));
            } elseif ($layer->getImage() instanceof \ImagickDraw) {
                $im = new \Imagick();
                ImagickResourceLimiter::applyLimits($im);

                $layer_info = $im->queryFontMetrics($layer->getImage(), $layer->getText());

                $layer->setY((int) (
                    $this->to_layer->getY()
                    + (int) $this->to_layer->getImage()->getImageHeight()
                    + (int) $layer_info['ascender']
                    + (int) $layer->getMagnetic()->getBottom()
                ));
            }
        } elseif ($this->to_layer->getImage() instanceof \ImagickDraw) {
            if ($layer->getImage() instanceof \Imagick) {
                $im = new \Imagick();
                ImagickResourceLimiter::applyLimits($im);

                $to_layer_info = $im->queryFontMetrics($this->to_layer->getImage(), $this->to_layer->getText());

                $layer->setY((int) (
                    $this->to_layer->getY()
                    + (int) ($this->to_layer->getCurrentNumberOfLines() * ($to_layer_info['ascender'] - $to_layer_info['descender']) + $to_layer_info['descender'])
                    + (int) $layer->getMagnetic()->getBottom()
                    - (int) $to_layer_info['ascender']
                ));
            } elseif ($layer->getImage() instanceof \ImagickDraw) {
                $im = new \Imagick();
                ImagickResourceLimiter::applyLimits($im);

                $to_layer_info = $im->queryFontMetrics($this->to_layer->getImage(), $this->to_layer->getText());

                $layer->setY((int) (
                    $this->to_layer->getY()
                    + (int) ($this->to_layer->getCurrentNumberOfLines() * ($to_layer_info['ascender'] - $to_layer_info['descender']) + $to_layer_info['descender'])
                    + (int) $layer->getMagnetic()->getBottom()
                ));
            }
        }

        unset($layer);
    }

    public function clear(): void
    {
        if ($this->im instanceof \Imagick) {
            $this->im->clear();
            $this->im->destroy();
        }

        $this->im = null;
        $this->layers = null;
        $this->layers_with_names = null;
        $this->to_layer = null;
    }
}
