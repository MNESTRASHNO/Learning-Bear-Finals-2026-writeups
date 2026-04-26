<?php
$uploadDir = __DIR__ . '/../uploads/';
$files = [];

if (is_dir($uploadDir)) {
    foreach (scandir($uploadDir) as $entry) {
        if ($entry === '.' || $entry === '..') continue;
        $fullPath = $uploadDir . $entry;
        if (is_file($fullPath)) {
            $files[] = [
                'name' => $entry,
                'size' => filesize($fullPath),
                'time' => filemtime($fullPath),
            ];
        }
    }
    usort($files, function ($a, $b) { return $b['time'] - $a['time']; });
}

function formatFileSize($bytes) {
    if ($bytes < 1024) return $bytes . ' B';
    if ($bytes < 1048576) return round($bytes / 1024, 1) . ' KB';
    return round($bytes / 1048576, 1) . ' MB';
}
?>

<div class="files-section">
    <h2>Uploaded Files</h2>

    <?php if (empty($files)): ?>
    <div class="empty-state">
        <svg viewBox="0 0 24 24" width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M13 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V9z"/>
            <polyline points="13 2 13 9 20 9"/>
        </svg>
        <p>No files have been uploaded yet.</p>
        <a href="/?page=upload.php" class="btn btn-primary">Upload a File</a>
    </div>
    <?php else: ?>
    <table class="files-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Size</th>
                <th>Date</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($files as $f): ?>
            <tr>
                <td>
                    <span class="file-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                            <polyline points="14 2 14 8 20 8"/>
                            <line x1="16" y1="13" x2="8" y2="13"/>
                            <line x1="16" y1="17" x2="8" y2="17"/>
                        </svg>
                        <?= htmlspecialchars($f['name']) ?>
                    </span>
                </td>
                <td><?= formatFileSize($f['size']) ?></td>
                <td><?= date('M d, Y H:i', $f['time']) ?></td>
                <td><a href="/uploads/<?= htmlspecialchars($f['name']) ?>" class="btn btn-sm" download>Download</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>
