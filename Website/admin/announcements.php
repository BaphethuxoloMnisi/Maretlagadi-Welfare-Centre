<?php
session_start();
require_once '../includes/db.php';
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit; }

$pageTitle = "Announcements";
$activeNav = "announcements";
$error = ""; $success = "";
$uploadDir = '../uploads/announcements/';

if (!is_dir($uploadDir)) { mkdir($uploadDir, 0755, true); }

function handleImageUpload($fileInputName, $uploadDir) {
    if (!isset($_FILES[$fileInputName]) || $_FILES[$fileInputName]['error'] === UPLOAD_ERR_NO_FILE) {
        return [null, null]; // no new file - not an error
    }
    if ($_FILES[$fileInputName]['error'] !== UPLOAD_ERR_OK) {
        return [null, "There was an error uploading the image."];
    }

    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    $mime = mime_content_type($_FILES[$fileInputName]['tmp_name']);

    if (!isset($allowed[$mime])) return [null, "Only JPG, PNG, or WEBP images are allowed."];
    if ($_FILES[$fileInputName]['size'] > 5 * 1024 * 1024) return [null, "Image must be smaller than 5MB."];

    $filename = 'ann_' . bin2hex(random_bytes(6)) . '.' . $allowed[$mime];
    if (!move_uploaded_file($_FILES[$fileInputName]['tmp_name'], $uploadDir . $filename)) {
        return [null, "Failed to save the uploaded image."];
    }
    return ['uploads/announcements/' . $filename, null];
}

// CREATE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['form_action'] ?? '') === 'create') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');

    if ($title === '' || $content === '') {
        $error = "Title and content are required.";
    } else {
        [$imagePath, $uploadError] = handleImageUpload('image', $uploadDir);
        if ($uploadError) {
            $error = $uploadError;
        } else {
            $stmt = $pdo->prepare("INSERT INTO announcement (title, content, image_path) VALUES (?, ?, ?)");
            $stmt->execute([$title, $content, $imagePath]);
            $success = "Announcement posted successfully.";
        }
    }
}

// UPDATE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['form_action'] ?? '') === 'update') {
    $id = (int) ($_POST['announcement_id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');

    if ($title === '' || $content === '') {
        $error = "Title and content are required.";
    } else {
        [$imagePath, $uploadError] = handleImageUpload('image', $uploadDir);
        if ($uploadError) {
            $error = $uploadError;
        } elseif ($imagePath) {
            // New image uploaded - delete the old one and update
            $stmt = $pdo->prepare("SELECT image_path FROM announcement WHERE announcement_id = ?");
            $stmt->execute([$id]);
            $old = $stmt->fetchColumn();
            if ($old && file_exists('../' . $old)) unlink('../' . $old);

            $stmt = $pdo->prepare("UPDATE announcement SET title=?, content=?, image_path=? WHERE announcement_id=?");
            $stmt->execute([$title, $content, $imagePath, $id]);
            $success = "Announcement updated successfully.";
        } else {
            // No new image - keep the existing one
            $stmt = $pdo->prepare("UPDATE announcement SET title=?, content=? WHERE announcement_id=?");
            $stmt->execute([$title, $content, $id]);
            $success = "Announcement updated successfully.";
        }
    }
}

// DELETE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['form_action'] ?? '') === 'delete') {
    $id = (int) ($_POST['announcement_id'] ?? 0);
    $stmt = $pdo->prepare("SELECT image_path FROM announcement WHERE announcement_id = ?");
    $stmt->execute([$id]);
    $img = $stmt->fetchColumn();
    if ($img && file_exists('../' . $img)) unlink('../' . $img);

    $stmt = $pdo->prepare("DELETE FROM announcement WHERE announcement_id = ?");
    $stmt->execute([$id]);
    $success = "Announcement deleted.";
}

// Load record for editing, if requested
$editing = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM announcement WHERE announcement_id = ?");
    $stmt->execute([(int) $_GET['edit']]);
    $editing = $stmt->fetch();
}

$announcements = $pdo->query("SELECT * FROM announcement ORDER BY created_at DESC")->fetchAll();

include 'includes/layout_top.php';
?>

<?php if ($success): ?><div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

<div class="row g-4">
  <div class="col-lg-5">
    <div class="card stat-card p-4">
      <h6 class="fw-bold mb-3"><?php echo $editing ? 'Edit Announcement' : 'Post New Announcement'; ?></h6>
      <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="form_action" value="<?php echo $editing ? 'update' : 'create'; ?>">
        <?php if ($editing): ?>
          <input type="hidden" name="announcement_id" value="<?php echo $editing['announcement_id']; ?>">
        <?php endif; ?>

        <div class="mb-3">
          <label class="form-label">Title</label>
          <input type="text" name="title" class="form-control" required
                 value="<?php echo $editing ? htmlspecialchars($editing['title']) : ''; ?>">
        </div>
        <div class="mb-3">
          <label class="form-label">Content</label>
          <textarea name="content" rows="4" class="form-control" required><?php echo $editing ? htmlspecialchars($editing['content']) : ''; ?></textarea>
        </div>
        <div class="mb-3">
          <label class="form-label">Image <?php echo $editing ? '(leave blank to keep current)' : '(optional)'; ?></label>
          <input type="file" name="image" class="form-control" accept="image/png, image/jpeg, image/webp">
          <?php if ($editing && $editing['image_path']): ?>
            <img src="../<?php echo htmlspecialchars($editing['image_path']); ?>" class="announcement-thumb mt-2">
          <?php endif; ?>
        </div>

        <div class="d-flex gap-2">
          <button type="submit" class="btn btn-brand rounded-pill px-4"><?php echo $editing ? 'Save Changes' : 'Post Announcement'; ?></button>
          <?php if ($editing): ?><a href="announcements.php" class="btn btn-outline-dark rounded-pill px-4">Cancel</a><?php endif; ?>
        </div>
      </form>
    </div>
  </div>

  <div class="col-lg-7">
    <div class="card stat-card">
      <div class="card-header bg-white fw-semibold">All Announcements</div>
      <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
          <thead class="table-light"><tr><th>Image</th><th>Title</th><th>Posted</th><th></th></tr></thead>
          <tbody>
            <?php foreach ($announcements as $a): ?>
            <tr>
              <td>
                <?php if ($a['image_path']): ?>
                  <img src="../<?php echo htmlspecialchars($a['image_path']); ?>" class="announcement-thumb">
                <?php else: ?>
                  <div class="announcement-thumb d-flex align-items-center justify-content-center text-secondary" style="font-size:.65rem;">No image</div>
                <?php endif; ?>
              </td>
              <td>
                <div class="fw-semibold"><?php echo htmlspecialchars($a['title']); ?></div>
                <div class="text-secondary small"><?php echo htmlspecialchars(mb_strimwidth($a['content'], 0, 60, '...')); ?></div>
              </td>
              <td class="small text-secondary"><?php echo date('d M Y', strtotime($a['created_at'])); ?></td>
              <td class="text-end">
                <a href="announcements.php?edit=<?php echo $a['announcement_id']; ?>" class="btn btn-sm btn-outline-dark">Edit</a>
                <form method="POST" class="d-inline" onsubmit="return confirm('Delete this announcement?');">
                  <input type="hidden" name="form_action" value="delete">
                  <input type="hidden" name="announcement_id" value="<?php echo $a['announcement_id']; ?>">
                  <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                </form>
              </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($announcements)): ?>
              <tr><td colspan="4" class="text-center text-secondary py-4">No announcements yet.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php include '../includes/layout_bottom.php'; ?>