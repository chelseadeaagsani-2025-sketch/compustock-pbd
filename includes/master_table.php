<?php
// Diperlukan: $pageTitle, $activePage, $viewName, $columns, $rows, $error.
?>
<div class="eyebrow">COMPUSTOCK / DATA MASTER</div>
<h1><?= h($pageTitle) ?></h1>
<p class="intro">Data pada halaman ini diambil langsung dari VIEW MySQL <code><?= h($viewName) ?></code>.</p>
<?php if ($error !== ''): ?>
  <div class="alert"><?= h($error) ?></div>
<?php endif; ?>
<div class="table-card">
    <div class="table-header">
        <div><strong>Daftar <?= h($pageTitle) ?></strong><small>Sumber: <?= h($viewName) ?></small></div>
        <span class="count"><?= count($rows) ?> data</span>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr>
                <?php foreach ($columns as $label): ?><th><?= h($label) ?></th><?php endforeach; ?>
            </tr></thead>
            <tbody>
                <?php if (count($rows) === 0): ?>
                    <tr><td colspan="<?= count($columns) ?>" class="empty">Belum ada data yang ditampilkan.</td></tr>
                <?php endif; ?>
                <?php foreach ($rows as $row): ?>
                    <tr>
                        <?php foreach ($columns as $key => $label): ?>
                            <td><?= formatKolom($key, $row[$key] ?? null, $activePage) ?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<div class="hint">Tahap ini fokus Read (menampilkan data dari VIEW), belum mencakup Tambah, Edit, atau Hapus.</div>
