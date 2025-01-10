<?php
declare(strict_types=1);
namespace SocialMediaImageGenerator;

class ImagickResourceLimiter
{
    /**
     * @throws \ImagickException
     */
    public static function applyLimits(\Imagick $im): void
    {
        $im->setResourceLimit(\Imagick::RESOURCETYPE_MEMORY, 256);
        $im->setResourceLimit(\Imagick::RESOURCETYPE_MAP, 256);
        $im->setResourceLimit(\Imagick::RESOURCETYPE_AREA, 1512);
        $im->setResourceLimit(\Imagick::RESOURCETYPE_FILE, 768);
        $im->setResourceLimit(\Imagick::RESOURCETYPE_DISK, -1);
    }
}
