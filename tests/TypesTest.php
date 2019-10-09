<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use SocialMediaImageGenerator\Properties\Blackout;
use SocialMediaImageGenerator\Properties\Font;
use SocialMediaImageGenerator\Properties\Magnetic;
use SocialMediaImageGenerator\Properties\Transform;
use SocialMediaImageGenerator\Properties\Underline;
use SocialMediaImageGenerator\Types\Background;
use SocialMediaImageGenerator\Types\BackgroundImage;
use SocialMediaImageGenerator\Types\Image;
use SocialMediaImageGenerator\Types\ImageGravityCenter;
use SocialMediaImageGenerator\Types\Text;

class TypesTest extends TestCase
{
    private $width = 1200;
    private $height = 630;

    /**
     * @return Background
     */
    public function testBackground(): Background
    {
        $data = [
            'name' => 'background',
            'color' => '#fff',
            'width' => $this->width,
            'height' => $this->height,
        ];

        $background = new Background($data);

        $this->assertSame($data['name'], $background->getName());
        $this->assertSame($data['color'], $background->getColor());
        $this->assertSame($data['width'], $background->getWidth());
        $this->assertSame($data['height'], $background->getHeight());

        $image = $background->getImage();

        $this->assertNotEmpty($image);
        $this->assertInstanceOf(\Imagick::class, $image);

        return $background;
    }

    /**
     * @depends PropertiesTest::testBlackout
     *
     * @param Blackout $blackout
     *
     * @return BackgroundImage
     */
    public function testBackgroundImage(Blackout $blackout): BackgroundImage
    {
        $data = [
            'name' => 'background',
            'path' => 'https://leonardo.osnova.io/667c5413-8191-0f34-3a36-e7405c3aa76d/',
            'width' => $this->width,
            'height' => $this->height,
            'blackout' => $blackout
        ];

        $background_image = new BackgroundImage($data);

        $this->assertSame($data['name'], $background_image->getName());
        $this->assertSame($data['path'], $background_image->getPath());
        $this->assertSame($data['width'], $background_image->getWidth());
        $this->assertSame($data['height'], $background_image->getHeight());
        $this->assertSame($data['blackout'], $background_image->getBlackout());

        $image = $background_image->getImage();

        $this->assertNotEmpty($image);
        $this->assertInstanceOf(\Imagick::class, $image);

        $this->assertInstanceOf(Blackout::class, $background_image->getBlackout());

        return $background_image;
    }

    /**
     * @depends PropertiesTest::testTransform
     * @depends PropertiesTest::testMagnetic
     *
     * @param Transform $transform
     * @param Magnetic $magnetic
     *
     * @return Image
     */
    public function testImage(Transform $transform, Magnetic $magnetic): Image
    {
        $data = [
            'name' => 'image',
            'path' => 'https://leonardo.osnova.io/667c5413-8191-0f34-3a36-e7405c3aa76d/',
            'width' => $this->width,
            'height' => $this->height,
            'round_corners' => rand(2, 10),
            'fill' => '#fff',
            'transform' => $transform,
            'magnetic' => $magnetic
        ];

        $image_class = new Image($data);
        $this->assertInstanceOf(Transform::class, $image_class->getTransform());

        $transform_data = [
            'top_left' => [rand(0, 100), rand(0, 100)],
            'top_right' => [rand(100, 300), rand(100, 300)],
            'bottom_right' => [rand(100, 300), rand(100, 300)],
            'bottom_left' => [rand(0, 100), rand(100, 300)]
        ];

        $image_class->setTransform($transform_data);
        $this->assertInstanceOf(Transform::class, $image_class->getTransform());

        $this->assertSame($data['name'], $image_class->getName());
        $this->assertSame($data['path'], $image_class->getPath());
        $this->assertSame($data['width'], $image_class->getWidth());
        $this->assertSame($data['height'], $image_class->getHeight());
        $this->assertSame($data['round_corners'], $image_class->getRoundCorners());
        $this->assertSame($data['fill'], $image_class->getFill());

        $image = $image_class->getImage();

        $this->assertNotEmpty($image);
        $this->assertInstanceOf(\Imagick::class, $image);

        $this->assertInstanceOf(Magnetic::class, $image_class->getMagnetic());

        if ($image_class->isTransform()) {
            $image_class->setTransform($transform->setAutoWidth(false)->setAutoHeight(true));
            $this->assertNotEmpty($image_class->getTransformCoordinates());

            $image_class->setTransform($transform->setAutoWidth(true)->setAutoHeight(false));
            $this->assertNotEmpty($image_class->getTransformCoordinates());
        }

        return $image_class;
    }

    /**
     * @depends testImage
     *
     * @param Image $image
     *
     * @return ImageGravityCenter
     */
    public function testImageGravityCenter(Image $image): ImageGravityCenter
    {
        $data = [
            'name' => $image->getName(),
            'path' => $image->getPath(),
            'width' => $image->getWidth(),
            'height' => $image->getHeight(),
            'fill' => $image->getFill()
        ];

        $image_gc = new ImageGravityCenter($data);

        $this->assertSame($data['name'], $image_gc->getName());
        $this->assertSame($data['path'], $image_gc->getPath());
        $this->assertSame($data['width'], $image_gc->getWidth());
        $this->assertSame($data['height'], $image_gc->getHeight());
        $this->assertSame($data['fill'], $image_gc->getFill());

        $image_r = $image_gc->getImage();

        $this->assertNotEmpty($image_r);
        $this->assertInstanceOf(\Imagick::class, $image_r);

        return $image_gc;
    }

    /**
     * @depends PropertiesTest::testFont
     * @depends PropertiesTest::testUnderline
     *
     * @param Font $font
     * @param Underline $underline
     *
     * @return Text
     */
    public function testText(Font $font, Underline $underline): Text
    {
        $data = [
            'name' => 'text',
            'text' => 'Russian journalists killed in CAR \'were researching military firm\'',
            'font' => $font,
            'align' => Text::ALIGN_LEFT,
            'underline' => $underline,
            'max_number_of_lines' => 5
        ];

        $text = new Text($data);

        $this->assertSame($data['name'], $text->getName());
        $this->assertSame($data['text'], $text->getText());
        $this->assertSame($data['align'], $text->getAlignment());
        $this->assertSame($data['max_number_of_lines'], $text->getMaxNumberOfLines());

        $draw = $text->getImage();

        $this->assertNotEmpty($draw);
        $this->assertInstanceOf(\ImagickDraw::class, $draw);

        $this->assertInstanceOf(Font::class, $text->getFont());
        $this->assertInstanceOf(Underline::class, $text->getUnderline());

        $underline_data = [
            'color' => '#000',
            'space' => rand(3, 6)
        ];

        $text->setUnderline($underline_data);
        $this->assertInstanceOf(Underline::class, $text->getUnderline());

        $this->assertNotEmpty($text->getUnderlineDraws());

        return $text;
    }
}
