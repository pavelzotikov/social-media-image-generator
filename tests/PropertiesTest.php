<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use SocialMediaImageGenerator\Properties\Font;
use SocialMediaImageGenerator\Properties\Blackout;
use SocialMediaImageGenerator\Properties\Magnetic;
use SocialMediaImageGenerator\Properties\Transform;
use SocialMediaImageGenerator\Properties\Underline;

class PropertiesTest extends TestCase
{

    public function testTransform(): Transform
    {
        $data = [
            'top_left' => [rand(0, 100), rand(0, 100)],
            'top_right' => [rand(100, 300), rand(100, 300)],
            'bottom_right' => [rand(100, 300), rand(100, 300)],
            'bottom_left' => [rand(0, 100), rand(100, 300)]
        ];


        $transform = new Transform($data);
        $coordinates = $transform->getCoordinates();

        $this->assertNotEmpty($coordinates);

        list($x1, $y1, $x2, $y2, $x3, $y3, $x4, $y4) = $coordinates;

        $this->assertSame($data['top_left'], [$x1, $y1]);
        $this->assertSame($data['top_right'], [$x2, $y2]);

        $this->assertSame($data['bottom_right'], [$x3, $y3]);
        $this->assertSame($data['bottom_left'], [$x4, $y4]);

        return $transform;
    }

    public function testBlackout(): Blackout
    {
        $data = [
            'color' => '#fff',
            'opacity' => .75
        ];

        $blackout = new Blackout($data);

        $this->assertSame($data['color'], $blackout->getColor());
        $this->assertSame($data['opacity'], $blackout->getOpacity());

        return $blackout;
    }

    public function testFont(): Font
    {
        $data = [
            'size' => rand(18, 56),
            'color' => '#000',
            'interline_spacing' => rand(3, 7),
            'antialias' => true
        ];

        $font = new Font($data);

        $this->assertSame($data['size'], $font->getSize());
        $this->assertSame($data['color'], $font->getColor());
        $this->assertSame($data['interline_spacing'], $font->getInterlineSpacing());
        $this->assertSame($data['antialias'], $font->getAntialias());

        return $font;
    }

    public function testMagnetic(): Magnetic
    {
        $data = [
            'to_layer' => 'background',
            'left' => rand(0, 100),
            'top' => rand(0, 100),
            'right' => rand(100, 600),
            'bottom' => rand(100, 600),
            'vertical_center' => true,
            'horizontal_center' => true
        ];

        $magnetic = new Magnetic($data);

        $this->assertSame($data['to_layer'], $magnetic->getToLayer());

        $this->assertSame($data['left'], $magnetic->getLeft());
        $this->assertSame($data['top'], $magnetic->getTop());
        $this->assertSame($data['right'], $magnetic->getRight());
        $this->assertSame($data['bottom'], $magnetic->getBottom());

        return $magnetic;
    }

    public function testUnderline(): Underline
    {
        $data = [
            'color' => '#000',
            'space' => rand(3, 6)
        ];

        $underline = new Underline($data);

        $this->assertSame($data['color'], $underline->getColor());
        $this->assertSame($data['space'], $underline->getSpace());

        return $underline;
    }

}