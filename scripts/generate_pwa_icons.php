<?php

declare(strict_types=1);

$publicDir = dirname(__DIR__) . '/public/icons';

if (! is_dir($publicDir) && ! mkdir($publicDir, 0777, true) && ! is_dir($publicDir)) {
    fwrite(STDERR, "Unable to create icon directory.\n");
    exit(1);
}

generateIcon($publicDir . '/icon-192.png', 192, false);
generateIcon($publicDir . '/icon-512.png', 512, false);
generateIcon($publicDir . '/maskable-512.png', 512, true);
generateIcon($publicDir . '/apple-touch-icon.png', 180, false);

function generateIcon(string $path, int $size, bool $maskable): void
{
    $image = imagecreatetruecolor($size, $size);
    imagealphablending($image, true);
    imagesavealpha($image, true);
    imageantialias($image, true);

    $bg = color($image, '#FBBF24');
    $card = color($image, '#FFFBEB');
    $outer = color($image, '#F59E0B');
    $inner = color($image, '#FDE68A');
    $lineDark = color($image, '#92400E');
    $lineMid = color($image, '#B45309');
    $needle = color($image, '#7C2D12');

    imagefilledrectangle($image, 0, 0, $size, $size, $bg);

    $outerRadius = (int) round($size * 0.265625);
    $outerInset = (int) round($size * 0.078125);
    roundedRectangle($image, $outerInset, $outerInset, $size - $outerInset, $size - $outerInset, $outerRadius, $card);

    $center = (int) round($size / 2);
    $ballRadius = (int) round($size * ($maskable ? 0.205 : 0.2578));
    $innerRadius = (int) round($ballRadius * 0.77);

    imagefilledellipse($image, $center, $center, $ballRadius * 2, $ballRadius * 2, $outer);
    imagefilledellipse($image, $center, $center, $innerRadius * 2, $innerRadius * 2, $inner);

    imagesetthickness($image, max(6, (int) round($size * 0.039)));
    imagearc($image, $center, $center - (int) round($size * 0.024), $ballRadius + (int) round($size * 0.15), $ballRadius + (int) round($size * 0.12), 205, 18, $lineDark);
    imagearc($image, $center, $center + (int) round($size * 0.024), $ballRadius + (int) round($size * 0.15), $ballRadius + (int) round($size * 0.12), 25, 198, $lineMid);
    imagearc($image, $center, $center, $ballRadius + (int) round($size * 0.1), $ballRadius - (int) round($size * 0.02), 205, 340, $lineDark);
    imagearc($image, $center, $center, $ballRadius + (int) round($size * 0.1), $ballRadius - (int) round($size * 0.02), 20, 160, $lineDark);

    imagesetthickness($image, max(5, (int) round($size * 0.031)));
    imagearc($image, $center, $center - (int) round($size * 0.11), (int) round($ballRadius * 1.55), (int) round($ballRadius * 0.52), 12, 168, $lineDark);
    imagearc($image, $center, $center + (int) round($size * 0.11), (int) round($ballRadius * 1.55), (int) round($ballRadius * 0.52), 192, 348, $lineDark);

    drawNeedle(
        $image,
        (int) round($center - $ballRadius * 0.42),
        (int) round($center - $ballRadius * 0.82),
        (int) round($center - $ballRadius * 0.78),
        (int) round($center - $ballRadius * 1.38),
        max(5, (int) round($size * 0.031)),
        $needle
    );

    drawNeedle(
        $image,
        (int) round($center + $ballRadius * 0.28),
        (int) round($center + $ballRadius * 0.62),
        (int) round($center + $ballRadius * 0.68),
        (int) round($center + $ballRadius * 1.3),
        max(5, (int) round($size * 0.031)),
        $needle
    );

    imagepng($image, $path);
}

function drawNeedle(GdImage $image, int $x1, int $y1, int $x2, int $y2, int $thickness, int $color): void
{
    imagesetthickness($image, $thickness);
    imageline($image, $x1, $y1, $x2, $y2, $color);
    imagefilledellipse($image, $x1, $y1, $thickness + 6, $thickness + 6, $color);
    imagefilledellipse($image, $x2, $y2, $thickness, $thickness, $color);
}

function roundedRectangle(GdImage $image, int $x1, int $y1, int $x2, int $y2, int $radius, int $color): void
{
    imagefilledrectangle($image, $x1 + $radius, $y1, $x2 - $radius, $y2, $color);
    imagefilledrectangle($image, $x1, $y1 + $radius, $x2, $y2 - $radius, $color);

    imagefilledellipse($image, $x1 + $radius, $y1 + $radius, $radius * 2, $radius * 2, $color);
    imagefilledellipse($image, $x2 - $radius, $y1 + $radius, $radius * 2, $radius * 2, $color);
    imagefilledellipse($image, $x1 + $radius, $y2 - $radius, $radius * 2, $radius * 2, $color);
    imagefilledellipse($image, $x2 - $radius, $y2 - $radius, $radius * 2, $radius * 2, $color);
}

function color(GdImage $image, string $hex): int
{
    $hex = ltrim($hex, '#');

    return imagecolorallocate(
        $image,
        hexdec(substr($hex, 0, 2)),
        hexdec(substr($hex, 2, 2)),
        hexdec(substr($hex, 4, 2))
    );
}
