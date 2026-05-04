<?php

namespace Azuriom\Plugin\AuthSkinSlim\Support;

/**
 * Detects Minecraft slim vs default (wide) arm model from a 64×64 skin PNG.
 * Logic aligned with common client heuristics (same regions as Skin API plugin).
 */
class SkinSlimDetector
{
    public static function isSlimFromPath(string $skinPath): bool
    {
        if (! is_file($skinPath) || ! extension_loaded('gd')) {
            return false;
        }

        $skin = @imagecreatefrompng($skinPath);
        if ($skin === false) {
            return false;
        }

        $width = imagesx($skin);
        $scale = $width / 64;

        $hasTransparency = function (int $x, int $y, int $w, int $h) use ($skin, $scale): bool {
            for ($px = (int) ($x * $scale); $px < (int) (($x + $w) * $scale); $px++) {
                for ($py = (int) ($y * $scale); $py < (int) (($y + $h) * $scale); $py++) {
                    $color = imagecolorat($skin, $px, $py);
                    $alpha = ($color >> 24) & 0x7F;
                    if ($alpha === 127) {
                        return true;
                    }
                }
            }

            return false;
        };

        $isAreaColor = function (int $x, int $y, int $w, int $h, int $targetR, int $targetG, int $targetB) use ($skin, $scale): bool {
            for ($px = (int) ($x * $scale); $px < (int) (($x + $w) * $scale); $px++) {
                for ($py = (int) ($y * $scale); $py < (int) (($y + $h) * $scale); $py++) {
                    $color = imagecolorat($skin, $px, $py);
                    $r = ($color >> 16) & 0xFF;
                    $g = ($color >> 8) & 0xFF;
                    $b = $color & 0xFF;
                    if ($r !== $targetR || $g !== $targetG || $b !== $targetB) {
                        return false;
                    }
                }
            }

            return true;
        };

        $isAreaBlack = fn (int $x, int $y, int $w, int $h): bool => $isAreaColor($x, $y, $w, $h, 0, 0, 0);
        $isAreaWhite = fn (int $x, int $y, int $w, int $h): bool => $isAreaColor($x, $y, $w, $h, 255, 255, 255);

        $isSlim = (
            $hasTransparency(50, 16, 2, 4)
            || $hasTransparency(54, 20, 2, 12)
            || $hasTransparency(42, 48, 2, 4)
            || $hasTransparency(46, 52, 2, 12)
        ) || (
            $isAreaBlack(50, 16, 2, 4)
            && $isAreaBlack(54, 20, 2, 12)
            && $isAreaBlack(42, 48, 2, 4)
            && $isAreaBlack(46, 52, 2, 12)
        ) || (
            $isAreaWhite(50, 16, 2, 4)
            && $isAreaWhite(54, 20, 2, 12)
            && $isAreaWhite(42, 48, 2, 4)
            && $isAreaWhite(46, 52, 2, 12)
        );

        imagedestroy($skin);

        return $isSlim;
    }
}
