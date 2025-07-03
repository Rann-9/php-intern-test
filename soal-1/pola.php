<?php

// Ukuran pola (baris dan kolom)
$s = 7;

for ($i = 0; $i < $s; $i++) {
    for ($j = 0; $j < $s; $j++) {
        // Kondisi untuk membentuk garis diagonal utama dan diagonal sekunder
        if ($i == $j || $i + $j == $s - 1) {
            echo "X ";
        } else {
            echo "O ";
        }
    }
    echo PHP_EOL;
}