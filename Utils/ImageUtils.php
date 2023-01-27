<?php
/**
 * Karaka
 *
 * PHP Version 8.1
 *
 * @package   phpOMS\Utils
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace phpOMS\Utils;

/**
 * Image utils class.
 *
 * This class provides static helper functionalities for images.
 *
 * @package phpOMS\Utils
 * @license OMS License 1.0
 * @link    https://jingga.app
 * @since   1.0.0
 */
final class ImageUtils
{
    /**
     * Constructor.
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }

    /**
     * Decode base64 image.
     *
     * @param string $img Encoded image
     *
     * @return string Decoded image
     *
     * @since 1.0.0
     */
    public static function decodeBase64Image(string $img) : string
    {
        $img = \str_replace('data:image/png;base64,', '', $img);
        $img = \str_replace(' ', '+', $img);

        return (string) \base64_decode($img);
    }

    /**
     * Calculate the lightness from an RGB value as integer
     *
     * @param int $rgb RGB value represented as integer
     *
     * @return float
     *
     * @since 1.0.0
     */
    public static function lightness(int $rgb) : float
    {
        $sR = ($rgb >> 16) & 0xFF;
        $sG = ($rgb >> 8) & 0xFF;
        $sB = $rgb & 0xFF;

        return self::lightnessFromRgb($sR, $sG, $sB);
    }

    /**
     * Calculate lightess from rgb values
     *
     * @param int $r Red
     * @param int $g Green
     * @param int $b Blue
     *
     * @return float
     *
     * @since 1.0.0
     */
    public static function lightnessFromRgb(int $r, int $g, int $b) : float
    {
        $vR = $r / 255.0;
        $vG = $g / 255.0;
        $vB = $b / 255.0;

        $lR = $vR <= 0.04045 ? $vR / 12.92 : \pow((($vR + 0.055) / 1.055), 2.4);
        $lG = $vG <= 0.04045 ? $vG / 12.92 : \pow((($vG + 0.055) / 1.055), 2.4);
        $lB = $vB <= 0.04045 ? $vB / 12.92 : \pow((($vB + 0.055) / 1.055), 2.4);

        $y     = 0.2126 * $lR + 0.7152 * $lG + 0.0722 * $lB;
        $lStar = $y <= 216.0 / 24389.0 ? $y * 24389.0 / 27.0 : \pow($y, (1 / 3)) * 116.0 - 16.0;

        return $lStar / 100.0;
    }

    /**
     * Resize image file
     *
     * @param string $srcPath Source path
     * @param string $dstPath Destination path
     * @param int    $width   New width
     * @param int    $height  New image width
     * @param bool   $crop    Crop image
     *
     * @return void
     * @since 1.0.0
     */
    public static function resize(string $srcPath, string $dstPath, int $width, int $height, bool $crop = false) : void
    {
        if (!\is_file($srcPath)) {
            return;
        }

        /** @var array $imageDim */
        $imageDim = \getimagesize($srcPath);

        if ((($imageDim[0] ?? -1) >= $width && ($imageDim[1] ?? -1) >= $height)
            || ($imageDim[0] === 0 || $imageDim[1] === 0)
        ) {
            return;
        }

        $ratio = $imageDim[0] / $imageDim[1];
        if ($crop) {
            if ($imageDim[0] > $imageDim[1]) {
                $imageDim[0] = (int) \ceil($imageDim[0] - ($imageDim[0] * \abs($ratio - $width / $height)));
            } else {
                $imageDim[1] = (int) \ceil($imageDim[1] - ($imageDim[1] * \abs($ratio - $width / $height)));
            }
        } else {
            if ($width / $height > $ratio) {
                $width = (int) ($height * $ratio);
            } else {
                $height = (int) ($width / $ratio);
            }
        }

        $src = null;
        if (\stripos($srcPath, '.jpg') !== false || \stripos($srcPath, '.jpeg') !== false) {
            $src = \imagecreatefromjpeg($srcPath);
        } elseif (\stripos($srcPath, '.png') !== false) {
            $src = \imagecreatefrompng($srcPath);
        } elseif (\stripos($srcPath, '.gif') !== false) {
            $src = \imagecreatefromgif($srcPath);
        }

        $dst = \imagecreatetruecolor($width, $height);

        if ($src === null || $src === false || $dst === null || $dst === false) {
            throw new \InvalidArgumentException();
        }

        \imagecopyresampled($dst, $src, 0, 0, 0, 0, $width, $height, $imageDim[0], $imageDim[1]);

        if (\stripos($srcPath, '.jpg') || \stripos($srcPath, '.jpeg')) {
            \imagejpeg($dst, $dstPath);
        } elseif (\stripos($srcPath, '.png')) {
            \imagepng($dst, $dstPath);
        } elseif (\stripos($srcPath, '.gif')) {
            \imagegif($dst, $dstPath);
        }

        \imagedestroy($src);
        \imagedestroy($dst);
    }

    /**
     * Get difference between two images
     *
     * @param string $img1 Path to first image
     * @param string $img2 Path to second image
     * @param string $out  Output path for difference image (empty = no difference image is created)
     * @param int    $diff Difference image type (0 = only show differences of img2, 1 = make differences red/green colored)
     *
     * @return int Amount of pixel differences
     *
     */
    public static function difference(string $img1, string $img2, string $out = '', int $diff = 0) : int
    {
        $src1 = null;
        if (\stripos($img1, '.jpg') !== false || \stripos($img1, '.jpeg') !== false) {
            $src1 = \imagecreatefromjpeg($img1);
        } elseif (\stripos($img1, '.png') !== false) {
            $src1 = \imagecreatefrompng($img1);
        } elseif (\stripos($img1, '.gif') !== false) {
            $src1 = \imagecreatefromgif($img1);
        }

        $src2 = null;
        if (\stripos($img2, '.jpg') !== false || \stripos($img2, '.jpeg') !== false) {
            $src2 = \imagecreatefromjpeg($img2);
        } elseif (\stripos($img2, '.png') !== false) {
            $src2 = \imagecreatefrompng($img2);
        } elseif (\stripos($img2, '.gif') !== false) {
            $src2 = \imagecreatefromgif($img2);
        }

        if ($src1 === null || $src2 === null
            || $src1 === false || $src2 === false
        ) {
            return 0;
        }

        $imageDim1 = [\imagesx($src1), \imagesy($src1)];
        $imageDim2 = [\imagesx($src2), \imagesy($src2)];

        $newDim = [\max($imageDim1[0], $imageDim2[0]), \max($imageDim1[1], $imageDim2[1])];

        $diff = empty($out) ? -1 : $out;

        if ($diff !== -1) {
            $dst = $diff === 0
                ? \imagecreatetruecolor($newDim[0], $newDim[1])
                : \imagecrop($src2, ['x' => 0, 'y' => 0, 'width' => $imageDim2[0], 'height' => $imageDim2[1]]);

            if ($dst === false) {
                return 0;
            }

            $alpha = \imagecolorallocatealpha($dst, 255, 255, 255, 127);
            if ($alpha === false) {
                return 0;
            }

            if ($diff === 0) {
                \imagefill($dst, 0, 0, $alpha);
            }

            $red   = \imagecolorallocate($dst, 255, 0, 0);
            $green = \imagecolorallocate($dst, 0, 255, 0);

            if ($red === false || $green === false) {
                return 0;
            }
        }

        $difference = 0;

        for ($i = 0; $i < $newDim[0]; ++$i) {
            for ($j = 0; $j < $newDim[1]; ++$j) {
                if ($i >= $imageDim1[0] || $j >= $imageDim1[1]) {
                    if ($diff === 0) {
                        \imagesetpixel($dst, $i, $j, $green);
                    } elseif ($diff === 1) {
                        if ($i >= $imageDim2[0] || $j >= $imageDim2[1]) {
                            \imagesetpixel($dst, $i, $j, $green);
                        } else {
                            $color2 = \imagecolorat($src2, $i, $j);
                            \imagesetpixel($dst, $i, $j, $color2);
                        }
                    }

                    ++$difference;
                    continue;
                }

                if ($i >= $imageDim2[0] || $j >= $imageDim2[1]) {
                    if ($diff === 0) {
                        \imagesetpixel($dst, $i, $j, $red);
                    } elseif ($diff === 1) {
                        if ($i >= $imageDim1[0] || $j >= $imageDim1[1]) {
                            \imagesetpixel($dst, $i, $j, $red);
                        } else {
                            $color1 = \imagecolorat($src1, $i, $j);
                            \imagesetpixel($dst, $i, $j, $color1);
                        }
                    }

                    ++$difference;
                    continue;
                }

                $color1 = \imagecolorat($src1, $i, $j);
                $color2 = \imagecolorat($src2, $i, $j);

                if ($color1 !== $color2 && $color1 !== false && $color2 !== null) {
                    ++$difference;

                    if ($diff === 0) {
                        \imagesetpixel($dst, $i, $j, $color2);
                    } elseif ($diff === 1) {
                        \imagesetpixel($dst, $i, $j, $green);
                    }
                }
            }
        }

        if ($diff !== -1) {
            if (\stripos($out, '.jpg') || \stripos($out, '.jpeg')) {
                \imagejpeg($dst, $out);
            } elseif (\stripos($out, '.png')) {
                \imagesavealpha($dst, true);
                \imagepng($dst, $out);
            } elseif (\stripos($out, '.gif')) {
                \imagegif($dst, $out);
            }

            \imagedestroy($src1);
            \imagedestroy($src2);
            \imagedestroy($dst);
        }

        return $difference;
    }
}
