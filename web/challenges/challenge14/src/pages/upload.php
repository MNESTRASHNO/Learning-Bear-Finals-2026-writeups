<?php
$message = null;
$messageType = null;
$uploadedName = null;

$allowedExtensions = ['txt'];
$maxSize = 2 * 1024 * 1024;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $message = 'Upload failed. Please try again.';
        $messageType = 'error';
    } else {
        $originalName = $file['name'];
        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        if (!in_array($ext, $allowedExtensions)) {
            $message = 'Invalid file type. Only .txt files are allowed.';
            $messageType = 'error';
        } elseif ($file['size'] > $maxSize) {
            $message = 'File too large. Maximum size is 2MB.';
            $messageType = 'error';
        } else {
            $safeName = preg_replace('/[^a-zA-Z0-9._-]/', '_', basename($originalName));
            if (empty($safeName) || $safeName[0] === '.') {
                $safeName = uniqid('file_') . '.txt';
            }

            $destination = __DIR__ . '/../uploads/' . $safeName;
            if (file_exists($destination)) {
                $safeName = time() . '_' . $safeName;
                $destination = __DIR__ . '/../uploads/' . $safeName;
            }

            if (move_uploaded_file($file['tmp_name'], $destination)) {
                $message = 'File uploaded successfully.';
                $messageType = 'success';
                $uploadedName = $safeName;
            } else {
                $message = 'Failed to save file. Please try again.';
                $messageType = 'error';
            }
        }
    }
}
?>

<?php if ($message): ?>
<div class="alert alert-<?= $messageType ?>">
    <?= htmlspecialchars($message) ?>
    <?php if ($uploadedName): ?>
        &mdash; <a href="/uploads/<?= htmlspecialchars($uploadedName) ?>" class="alert-link"><?= htmlspecialchars($uploadedName) ?></a>
    <?php endif; ?>
</div>
<?php endif; ?>

<div class="upload-card">
    <h2>Upload File</h2>
    <p>Share your text files with the world. Only <strong>.txt</strong> files up to 2MB are accepted.</p>

    <form method="POST" enctype="multipart/form-data" id="uploadForm">
        <div class="dropzone" id="dropzone">
            <svg viewBox="0 0 24 24" width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/>
                <polyline points="17 8 12 3 7 8"/>
                <line x1="12" y1="3" x2="12" y2="15"/>
            </svg>
            <p class="drop-text">Drag and drop your file here</p>
            <p class="drop-or">or</p>
            <label class="browse-label">
                Browse Files
                <input type="file" name="file" accept=".txt" id="fileInput" hidden>
            </label>
            <p class="upload-hint">Accepted formats: .txt &mdash; Max size: 2MB</p>
        </div>
        <div class="file-selected" id="fileName"></div>
        <button type="submit" class="btn btn-primary btn-submit" id="submitBtn" style="display:none">Upload File</button>
    </form>
</div>

<script>
(function() {
    var dropzone = document.getElementById('dropzone');
    var fileInput = document.getElementById('fileInput');
    var submitBtn = document.getElementById('submitBtn');
    var fileNameDiv = document.getElementById('fileName');

    ['dragenter', 'dragover'].forEach(function(evt) {
        dropzone.addEventListener(evt, function(e) {
            e.preventDefault();
            dropzone.classList.add('dragover');
        });
    });

    ['dragleave', 'drop'].forEach(function(evt) {
        dropzone.addEventListener(evt, function(e) {
            e.preventDefault();
            dropzone.classList.remove('dragover');
        });
    });

    dropzone.addEventListener('drop', function(e) {
        if (e.dataTransfer.files.length) {
            fileInput.files = e.dataTransfer.files;
            showFile();
        }
    });

    fileInput.addEventListener('change', showFile);

    function showFile() {
        if (fileInput.files.length) {
            fileNameDiv.textContent = fileInput.files[0].name;
            fileNameDiv.style.display = 'block';
            submitBtn.style.display = 'block';
        }
    }
})();
</script>
