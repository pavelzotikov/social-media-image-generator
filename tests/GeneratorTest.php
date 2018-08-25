<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use SocialMediaImageGenerator\Generator;
use SocialMediaImageGenerator\Types\Text;

class GeneratorTest extends TestCase
{

    private $width = 800;
    private $height = 600;

    private $color = '#fff';

    /**
     * @return Generator
     */
    public function testConstruct(): Generator
    {
        $generator = new Generator($this->width, $this->height, $this->color);

        $this->assertNotEmpty($generator);

        return $generator;
    }

    /**
     * @depends testConstruct
     *
     * @param Generator $generator
     *
     * @return Generator
     */
    public function testLoadLayers(Generator $generator): Generator
    {
        $layers = [
            [
                'type' => 'background_image',
                'properties' => [
                    'name' => 'background',
                    'path' => 'https://leonardo.osnova.io/667c5413-8191-0f34-3a36-e7405c3aa76d/',
                    'blackout' => [
                        'color' => '#000000',
                        'opacity' => .75
                    ],
                    'width' => 800,
                    'height' => 600,
                ]
            ],
            [
                'type' => 'text',
                'properties' => [
                    'text' => 'Russian journalists killed in CAR \'were researching military firm\'',
                    'alignment' => Text::ALIGN_LEFT,
                    'max_width' => 680,
                    'max_number_of_lines' => 3,
                    'font' => [
                        'size' => 53,
                        'color' => '#fff',
                        'interline_spacing' => 3
                    ],
                    'magnetic' => [
                        'to_layer' => 'background',
                        'left' => 60,
                        'vertical_center' => true
                    ],
                ]
            ]
        ];

        $generator->loadLayers($layers);

        $this->assertNotEmpty($generator->getLayers());

        $this->assertSame(count($layers), count($generator->getLayers()));

        return $generator;
    }

    /**
     * @depends testLoadLayers
     *
     * @param Generator $generator
     */
    public function testRender(Generator $generator): void
    {
        $render = $generator->render();

        $this->assertNotEmpty($render);

        $image = imagecreatefromstring($render);

        $this->assertSame($this->width, imagesx($image));
        $this->assertSame($this->height, imagesy($image));
    }

}