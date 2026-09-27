<?php

$dirs = [
    __DIR__ . '/../storage/app/public/dokumentasi',
    __DIR__ . '/../public/storage/dokumentasi',
    __DIR__ . '/../public/dokumentasi',
];

foreach ($dirs as $dir) {
    if (!file_exists($dir)) {
        mkdir($dir, 0777, true);
    }
}

$images = [
    'dummy_putus.jpg' => [
        'title' => 'KONDISI KABEL PUTUS (CUT)',
        'subtitle' => 'Kabel 24 Core Terputus Terkena Alat Berat',
        'bg' => [239, 68, 68], // Red
        'accent' => [185, 28, 28],
    ],
    'dummy_otdr.jpg' => [
        'title' => 'OTDR TRACE EVENT REPORT',
        'subtitle' => 'Jarak Event Cut: 14.820 Km | Loss: > 25 dB',
        'bg' => [14, 116, 144], // Cyan/Teal
        'accent' => [8, 145, 178],
    ],
    'dummy_splicing.jpg' => [
        'title' => 'PROSES SPLICING / PENGELASAN',
        'subtitle' => 'Fusion Splicing Core 1-24 Tube Biru & Orange',
        'bg' => [37, 99, 235], // Blue
        'accent' => [29, 78, 216],
    ],
    'dummy_closure.jpg' => [
        'title' => 'PEMASANGAN JOINT CLOSURE',
        'subtitle' => 'JC-01 Dome 24 Core Terpasang di Tiang Udara',
        'bg' => [16, 185, 129], // Green
        'accent' => [5, 150, 105],
    ],
    'dummy_finish.jpg' => [
        'title' => 'HASIL AKHIR & QC NORMALISASI',
        'subtitle' => 'OTDR End-to-End: Avg Loss 0.02 dB (Link UP)',
        'bg' => [13, 148, 136], // Teal
        'accent' => [15, 118, 110],
    ],
    'dummy_otdr_manuver.jpg' => [
        'title' => 'PENGUKURAN JALUR MANUVER CORE',
        'subtitle' => 'OTDR Redaman Core Manuver Spare 0.03 dB',
        'bg' => [124, 58, 237], // Purple
        'accent' => [109, 40, 217],
    ],
    'dummy_otb_patching.jpg' => [
        'title' => 'CROSS CONNECT OTB POP BANDUNG',
        'subtitle' => 'Patchcord SC-UPC Tray 2 Port 1-12 Terhubung',
        'bg' => [79, 70, 229], // Indigo
        'accent' => [67, 56, 202],
    ],
];

foreach ($images as $filename => $info) {
    $width = 600;
    $height = 400;
    $im = imagecreatetruecolor($width, $height);

    // Gradient background
    $darkNavy = imagecolorallocate($im, 15, 23, 42); // #0f172a
    $cardBg = imagecolorallocate($im, 30, 41, 59); // #1e293b
    $headerBg = imagecolorallocate($im, $info['bg'][0], $info['bg'][1], $info['bg'][2]);
    $white = imagecolorallocate($im, 255, 255, 255);
    $grayText = imagecolorallocate($im, 148, 163, 184); // #94a3b8
    $tealGreen = imagecolorallocate($im, 52, 211, 153);
    $cyan = imagecolorallocate($im, 56, 189, 248);
    $borderColor = imagecolorallocate($im, 51, 65, 85);

    // Fill background
    imagefilledrectangle($im, 0, 0, $width, $height, $darkNavy);

    // Header banner
    imagefilledrectangle($im, 0, 0, $width, 60, $headerBg);

    // Inner card area
    imagefilledrectangle($im, 20, 80, $width - 20, $height - 20, $cardBg);
    imagerectangle($im, 20, 80, $width - 20, $height - 20, $borderColor);

    // Draw tech grid / graph
    $gridColor = imagecolorallocate($im, 40, 53, 75);
    for ($x = 40; $x < $width - 20; $x += 40) {
        imageline($im, $x, 100, $x, $height - 40, $gridColor);
    }
    for ($y = 100; $y < $height - 20; $y += 35) {
        imageline($im, 40, $y, $width - 40, $y, $gridColor);
    }

    // Graph trace line
    $traceColor = $cyan;
    $points = [
        40, 140,
        140, 145,
        240, 148,
        340, 260, // Drop event
        360, 250,
        460, 255,
        540, 258
    ];
    for ($i = 0; $i < count($points) - 2; $i += 2) {
        imageline($im, $points[$i], $points[$i+1], $points[$i+2], $points[$i+3], $traceColor);
        imageline($im, $points[$i], $points[$i+1]+1, $points[$i+2], $points[$i+3]+1, $traceColor);
        imagefilledellipse($im, $points[$i], $points[$i+1], 6, 6, $tealGreen);
    }

    // Text labels
    imagestring($im, 5, 25, 20, "PT MEDIA SOLUSI NETWORK (MSN)", $white);
    imagestring($im, 4, 35, 95, "[ DOKUMENTASI RESMI LAPANGAN ]", $tealGreen);
    imagestring($im, 5, 35, 120, $info['title'], $white);
    imagestring($im, 3, 35, 310, $info['subtitle'], $cyan);
    imagestring($im, 2, 35, 340, "Timestamp: " . date('Y-m-d H:i:s') . " WIB | GPS Verified", $grayText);
    imagestring($im, 2, 35, 355, "Fiber Backbone Operation & Maintenance Unit", $grayText);

    // Save to all target directories
    foreach ($dirs as $dir) {
        $target = $dir . '/' . $filename;
        imagejpeg($im, $target, 90);
    }

    imagedestroy($im);
    echo "Generated {$filename}\n";
}

echo "All dummy images generated successfully.\n";
