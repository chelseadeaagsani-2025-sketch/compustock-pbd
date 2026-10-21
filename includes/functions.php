<?php
function h($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function formatKolom(string $key, $value, string $halaman): string
{
    if ($value === null) {
        return '<span class="muted">—</span>';
    }

    if ($key === 'status') {
        $isActive = $halaman === 'vendor'
            ? strtoupper((string) $value) === 'A'
            : (string) $value === '1';
        return '<span class="badge ' . ($isActive ? 'aktif' : 'nonaktif') . '">'
             . ($isActive ? 'Aktif' : 'Tidak aktif') . '</span>';
    }
    if ($key === 'harga') {
        return 'Rp ' . h(number_format((float) $value, 0, ',', '.'));
    }
    if ($key === 'persen') {
        return h(number_format((float) $value, 0, ',', '.')) . '%';
    }
    if ($key === 'badan_hukum') {
        $map = ['P' => 'PT', 'C' => 'CV'];
        return h($map[$value] ?? $value);
    }

    return h($value);
}
