<?php

namespace App\Support;

trait NormalisasiKolom
{
    public function kolomKriteria(string $namaKriteria): string
    {
        $hasil = strtolower(str_replace([' ', '/', '(', ')', '&'], ['_', '_', '', '', ''], $namaKriteria));
        $hasil = preg_replace('/_+/', '_', $hasil);
        return trim($hasil, '_');
    }
}
