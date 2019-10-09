<?php
declare(strict_types=1);
namespace SocialMediaImageGenerator\Types;

use SocialMediaImageGenerator\Properties\Font as FontProperties;
use SocialMediaImageGenerator\Properties\Underline as UnderlineProperties;

class Text extends AbstractType
{
    const ALIGN_LEFT = 1;
    const ALIGN_CENTER = 2;
    const ALIGN_RIGHT = 3;

    protected $font;

    protected $underline;
    protected $underline_draws = [];

    protected $max_number_of_lines;

    protected $current_number_of_lines = 1;

    protected $align = self::ALIGN_LEFT;

    public function getImage(): \ImagickDraw
    {
        if ($this->layer) {
            return $this->layer;
        }

        $layer = new \ImagickDraw();
        if ($this->getFont()) {
            if ($this->getFont()->getFile() !== null) {
                $layer->setFont($this->getFont()->getFile());
            }
            if ($this->getFont()->getSize() !== null) {
                $layer->setFontSize($this->getFont()->getSize());
            }
            if ($this->getFont()->getColor() !== null) {
                $layer->setFillColor(new \ImagickPixel($this->getFont()->getColor()));
            }
            if ($this->getFont()->getInterlineSpacing() !== null) {
                $layer->setTextInterlineSpacing($this->getFont()->getInterlineSpacing());
            }
            if ($this->getFont()->getAntialias() !== null) {
                $layer->setTextAntialias($this->getFont()->getAntialias());
            }
        }

        if ($this->getAlignment()) {
            $layer->setTextAlignment($this->getAlignment());
        }

        if ($this->getMaxWidth()) {
            $this->setText(
                $this->wordwrap($this->getText(), $this->getMaxWidth(), $layer, $this->current_number_of_lines)
            );

            if ($this->getMaxNumberOfLines()) {
                if ($this->current_number_of_lines > $this->getMaxNumberOfLines()) {
                    $this->setText(
                        $this->sliceTextByLines($this->getText(), $this->getMaxNumberOfLines())
                    );
                    $this->current_number_of_lines = $this->getMaxNumberOfLines();
                }
            }
        }

        $this->layer = $layer;

        return $layer;
    }

    private function sliceTextByLines(string $text, int $lines, string $symbol = '...'): string
    {
        $text_arr = explode("\n", $text);
        $slice_text_arr = array_slice($text_arr, 0, $lines);
        return implode("\n", $slice_text_arr) . $symbol;
    }

    private function getMetricsForEachOfLine(string $text, \ImagickDraw $draw): array
    {
        $im = new \Imagick();

        $info = [];
        foreach (explode("\n", $text) as $string) {
            $info[] = $im->queryFontMetrics($draw, $string);
        }

        unset($im);
        return $info;
    }

    private function wordwrap(string $text, int $width, \ImagickDraw $draw, int &$number_of_lines = 1): string
    {
        $im = new \Imagick();

        $final_text = "";
        $words = explode(' ', $text);

        if ($words) {
            foreach ($words as $word) {
                $string = "$final_text $word";

                $info = $im->queryFontMetrics($draw, $string);

                if ($info['originX'] > $width) {
                    $number_of_lines++;
                    $final_text .= ($final_text ? "\n" : "") . $word;
                } else {
                    $final_text .= ($final_text ? " " : "") . $word;
                }
            }
        }

        unset($im);
        return $final_text;
    }

    public function setFont($font): self
    {
        if (is_array($font)) {
            $font = new FontProperties($font);
        }

        $this->font = $font;

        return $this;
    }

    public function setMaxWidth(int $width): self
    {
        $this->width = $width;

        return $this;
    }

    public function setMaxNumberOfLines(int $lines): self
    {
        $this->max_number_of_lines = $lines;

        return $this;
    }

    public function setAlignment(int $align): self
    {
        $this->align = $align;

        return $this;
    }

    public function setUnderline($underline): self
    {
        if (is_array($underline)) {
            $underline = new UnderlineProperties($underline);
        }

        $this->underline = $underline;

        return $this;
    }

    public function getMaxWidth(): ?int
    {
        return $this->width;
    }

    public function getMaxNumberOfLines(): ?int
    {
        return $this->max_number_of_lines;
    }

    public function getFont(): ?FontProperties
    {
        return $this->font;
    }

    public function getUnderline(): ?UnderlineProperties
    {
        return $this->underline;
    }

    public function getUnderlineDraws(): array
    {
        if (!$this->underline_draws) {
            $metrics = $this->getMetricsForEachOfLine($this->getText(), $this->layer);
            foreach ($metrics as $line_number => $line_info) {
                $underline_draw = new \ImagickDraw();

                if ($this->getUnderline()->getColor() !== null) {
                    $underline_draw->setFillColor(
                        new \ImagickPixel(
                            $this->getUnderline()->getColor()
                        )
                    );
                }

                $line_indent = ($line_info['ascender'] - $line_info['descender']);

                if ($this->getFont() && $this->getFont()->getInterlineSpacing()) {
                    $line_indent += $this->getFont()->getInterlineSpacing();
                }

                $underline_draw->line(
                    $this->getX(),
                    $this->getY() + $this->getUnderline()->getSpace() + $line_number * $line_indent,
                    $this->getX() + (int) $line_info['textWidth'],
                    $this->getY() + $this->getUnderline()->getSpace() + $line_number * $line_indent
                );

                $this->underline_draws[] = $underline_draw;
            }
        }

        return $this->underline_draws;
    }

    public function getAlignment(): int
    {
        return $this->align;
    }

    public function getCurrentNumberOfLines(): int
    {
        return $this->current_number_of_lines;
    }
}
