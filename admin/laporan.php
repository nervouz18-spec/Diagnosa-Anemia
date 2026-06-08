<?php
session_start();
include '../config.php';
if (!isset($_SESSION['admin'])) { header('Location: login.php'); exit; }

// Filter
$q     = trim($_GET['q'] ?? '');
$from  = $_GET['from'] ?? '';
$to    = $_GET['to'] ?? '';

$where = []; $params = []; $types = '';
if ($q !== '')   { $where[] = "(nama_pasien LIKE ? OR gejala_dipilih LIKE ? OR hasil_dianosa LIKE ?)"; $like = "%$q%"; $params[]=$like; $params[]=$like; $params[]=$like; $types.='sss'; }
if ($from !== ''){ $where[] = "tanggal >= ?"; $params[] = $from.' 00:00:00'; $types.='s'; }
if ($to !== '')  { $where[] = "tanggal <= ?"; $params[] = $to.' 23:59:59'; $types.='s'; }
$where_sql = $where ? 'WHERE '.implode(' AND ', $where) : '';

// Export CSV
if (isset($_GET['export'])) {
    $sql = "SELECT id, tanggal, nama_pasien, umur, jenis_kelamin, gejala_dipilih, hasil_dianosa FROM riwayat $where_sql ORDER BY tanggal DESC";
    if ($params) { $stmt = $conn->prepare($sql); $stmt->bind_param($types, ...$params); $stmt->execute(); $res = $stmt->get_result(); }
    else { $res = $conn->query($sql); }
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="laporan_diagnosa_'.date('Ymd_His').'.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['ID','Tanggal','Nama Pasien','Umur','JK','Gejala Dipilih','Hasil Diagnosa']);
    while ($r = $res->fetch_assoc()) {
        $g = json_decode($r['gejala_dipilih'] ?? '[]', true) ?: [];
        $h = json_decode($r['hasil_dianosa'] ?? '[]', true) ?: [];
        $g_text = implode(',', $g);
        $h_text = count($h) === 0 ? 'Tidak terdeteksi' : implode(' | ', array_map(fn($x)=>($x['kode']??'').' '.($x['nama']??''), $h));
        fputcsv($out, [$r['id'], $r['tanggal'], $r['nama_pasien'], $r['umur'], $r['jenis_kelamin'], $g_text, $h_text]);
    }
    fclose($out); exit;
}

// Hapus
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    $conn->query("DELETE FROM riwayat WHERE id=$id");
    header("Location: laporan.php?msg=".urlencode("Riwayat dihapus.")."&t=success"); exit;
}

$msg = $_GET['msg'] ?? ''; $msg_type = $_GET['t'] ?? 'info';

// Pagination
$page = max(1, intval($_GET['page'] ?? 1));
$per  = 15;
$off  = ($page-1) * $per;

$count_sql = "SELECT COUNT(*) c FROM riwayat $where_sql";
if ($params) { $cs = $conn->prepare($count_sql); $cs->bind_param($types, ...$params); $cs->execute(); $total = (int)$cs->get_result()->fetch_assoc()['c']; }
else { $total = (int)$conn->query($count_sql)->fetch_assoc()['c']; }
$pages = max(1, (int)ceil($total / $per));

$sql = "SELECT * FROM riwayat $where_sql ORDER BY tanggal DESC LIMIT $per OFFSET $off";
if ($params) { $stmt = $conn->prepare($sql); $stmt->bind_param($types, ...$params); $stmt->execute(); $rs = $stmt->get_result(); }
else { $rs = $conn->query($sql); }

$page_title = 'Laporan Diagnosa';
$active     = 'laporan';
include '../partials/admin_header.php';

$qs = $_GET; unset($qs['page']);
$base = '?' . http_build_query($qs);
?>

<div class="page-header">
    <h2>Laporan Diagnosa</h2>
    <p class="lead">Daftar seluruh riwayat diagnosa yang dilakukan pengguna (anonim).</p>
</div>

<?php if ($msg): ?>
    <div class="alert alert-<?php echo htmlspecialchars($msg_type); ?>"><i class="fa-solid fa-circle-info"></i> <?php echo htmlspecialchars($msg); ?></div>
<?php endif; ?>

<div class="card">
    <form method="get" data-testid="form-filter">
        <div class="form-row">
            <div class="form-group">
                <label>Pencarian</label>
                <input type="text" name="q" value="<?php echo htmlspecialchars($q); ?>" placeholder="Nama pasien / kode gejala / nama penyakit" data-testid="input-q">
            </div>
            <div class="form-group">
                <label>Dari Tanggal</label>
                <input type="date" name="from" value="<?php echo htmlspecialchars($from); ?>" data-testid="input-from">
            </div>
            <div class="form-group">
                <label>Sampai Tanggal</label>
                <input type="date" name="to" value="<?php echo htmlspecialchars($to); ?>" data-testid="input-to">
            </div>
            <div class="form-group" style="display:flex;align-items:flex-end;gap:.5rem;">
                <button type="submit" class="btn btn-primary" data-testid="btn-filter"><i class="fa-solid fa-filter"></i> Filter</button>
                <a href="laporan.php" class="btn btn-secondary" data-testid="btn-reset-filter"><i class="fa-solid fa-rotate"></i></a>
            </div>
        </div>
    </form>
</div>

<div class="card">
    <div class="card-head">
        <h3><i class="fa-solid fa-table-list" style="color:var(--primary);margin-right:.4rem;"></i>Hasil (<?php echo $total; ?> entri)</h3>
        <a href="?<?php echo http_build_query(array_merge($_GET, ['export'=>1])); ?>" class="btn btn-secondary btn-sm" data-testid="btn-export-csv"><i class="fa-solid fa-file-csv"></i> Export CSV</a>
    </div>
    <div class="table-wrap">
        <table class="data">
            <thead>
                <tr><th>ID</th><th>Tanggal</th><th>Pasien</th><th>Gejala</th><th>Hasil</th><th style="width:80px;">Aksi</th></tr>
            </thead>
            <tbody>
                <?php if ($rs->num_rows === 0): ?>
                    <tr><td colspan="6" class="text-center text-muted" style="padding:2rem;">Tidak ada data.</td></tr>
                <?php else: while ($r = $rs->fetch_assoc()):
                    $glist = json_decode($r['gejala_dipilih'] ?? '[]', true) ?: [];
                    $hlist = json_decode($r['hasil_dianosa'] ?? '[]', true) ?: [];
                ?>
                    <tr data-testid="row-laporan-<?php echo $r['id']; ?>">
                        <td>#<?php echo (int)$r['id']; ?></td>
                        <td class="text-sm"><?php echo date('d M Y · H:i', strtotime($r['tanggal'])); ?></td>
                        <td>
                            <strong><?php echo htmlspecialchars($r['nama_pasien'] ?: '— Anonim'); ?></strong>
                            <?php if ($r['umur'] || $r['jenis_kelamin']): ?>
                                <div class="text-muted text-sm"><?php echo (int)$r['umur']; ?> th · <?php echo $r['jenis_kelamin']==='L'?'L':($r['jenis_kelamin']==='P'?'P':'-'); ?></div>
                            <?php endif; ?>
                        </td>
                        <td class="text-sm" style="max-width:200px;">
                            <?php echo htmlspecialchars(implode(', ', array_slice($glist, 0, 8))).(count($glist)>8?'…':''); ?>
                            <div class="text-muted text-sm"><?php echo count($glist); ?> gejala</div>
                        </td>
                        <td class="text-sm" style="max-width:300px;">
                            <?php if (count($hlist) === 0): ?>
                                <span class="text-muted">Tidak terdeteksi</span>
                            <?php else:
                                foreach ($hlist as $h): ?>
                                    <div style="margin-bottom:.2rem;">
                                        <strong><?php echo htmlspecialchars($h['nama'] ?? '-'); ?></strong>
                                        <span class="text-muted text-sm"> (<?php echo htmlspecialchars($h['kode'] ?? '-'); ?>)</span>
                                    </div>
                            <?php endforeach;
                            endif; ?>
                        </td>
                        <td><a href="?hapus=<?php echo $r['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus entri ini?');" data-testid="btn-hapus-laporan-<?php echo $r['id']; ?>"><i class="fa-solid fa-trash"></i></a></td>
                    </tr>
                <?php endwhile; endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($pages > 1): ?>
    <div style="display:flex;justify-content:center;gap:.4rem;margin-top:1rem;flex-wrap:wrap;">
        <?php for ($i=1; $i<=$pages; $i++): ?>
            <a href="<?php echo $base; ?>&page=<?php echo $i; ?>" class="btn <?php echo $i===$page?'btn-primary':'btn-secondary'; ?> btn-sm"><?php echo $i; ?></a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>
</div>

<?php include '../partials/admin_footer.php'; ?>
