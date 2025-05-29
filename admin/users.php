<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: ../login/');
    exit;
}

include '../config.php';
$db = new Database();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    if (empty($_POST['id']) || !is_numeric($_POST['id'])) {
        echo json_encode([
            "success" => false,
            "title" => "❌ Xato!",
            "message" => "Foydalanuvchi ID topilmadi!"
        ]);
        exit;
    }

    $id = intval($_POST['id']);
    $name = trim($_POST['name']);
    $username = trim($_POST['username']);
    $role = trim($_POST['role'] ?? 'user');

    if (empty($name) || empty($username)) {
        echo json_encode([
            "success" => false,
            "title" => "⚠️ Diqqat!",
            "message" => "Ism va username to‘ldirilishi kerak!"
        ]);
        exit;
    }

    if (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)) {
        echo json_encode([
            "success" => false,
            "title" => "❌ Username xato!",
            "message" => "Username 3-20 ta belgidan iborat, faqat a-z, 0-9 va _ bo‘lishi mumkin!"
        ]);
        exit;
    }

    $check = $db->select("users", "*", "username = ? AND id != ?", [$username, $id], 'si');
    if ($check) {
        echo json_encode([
            "success" => false,
            "title" => "❌ Username band!",
            "message" => "Bunday username allaqachon mavjud!"
        ]);
        exit;
    }

    $updated = $db->update("users", [
        "name" => $name,
        "username" => $username,
        "role" => $role
    ], "id = ?", [$id], 'i');

    echo json_encode([
        "success" => $updated,
        "title" => $updated ? "✅ Yangilandi!" : "😕 Xatolik",
        "message" => $updated ? "Ma'lumotlar saqlandi!" : "Yangilashda muammo yuz berdi "
    ]);
    exit;
}

$users = $db->select('users', '*');
?>

<?php include './header.php'; ?>

<div class="container">
    <table class="table table-bordered table-hover">
        <thead class="table-light">
            <tr>
                <th>#</th>
                <th>Ism</th>
                <th>Username</th>
                <th>Rol</th>
                <th>Amallar</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $index => $user): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= htmlspecialchars($user['name']) ?></td>
                    <td><?= htmlspecialchars($user['username']) ?></td>
                    <td><?= htmlspecialchars($user['role']) ?></td>
                    <td>
                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                            data-bs-target="#editModal<?= $user['id'] ?>"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-sm btn-danger" data-bs-toggle="modal"
                            data-bs-target="#deleteModal<?= $user['id'] ?>"><i class="fas fa-trash-alt"></i></button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- 🔽 MODALLARNI TASHQARIDA YARATING -->
    <?php foreach ($users as $user): ?>
        <!-- Edit Modal -->
        <div class="modal fade" id="editModal<?= $user['id'] ?>" tabindex="-1"
            aria-labelledby="editModalLabel<?= $user['id'] ?>" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form class="edit-user-form" data-id="<?= $user['id'] ?>">
                        <input type="hidden" name="id" value="<?= $user['id'] ?>">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Ism</label>
                                <input type="text" class="form-control" name="name"
                                    value="<?= htmlspecialchars($user['name']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Username</label>
                                <input type="text" class="form-control" name="username"
                                    value="<?= htmlspecialchars($user['username']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Rol</label>
                                <select class="form-select" name="role">
                                    <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                                    <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>User</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                            <button type="submit" class="btn btn-primary">Saqlash</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Delete Modal -->
        <div class="modal fade" id="deleteModal<?= $user['id'] ?>" tabindex="-1"
            aria-labelledby="deleteModalLabel<?= $user['id'] ?>" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="post" action="user_delete.php">
                        <div class="modal-header">
                            <h5 class="modal-title">Foydalanuvchini o'chirish</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Yopish"></button>
                        </div>
                        <div class="modal-body">
                            <p>Haqiqatan ham <strong><?= htmlspecialchars($user['name']) ?></strong> foydalanuvchisini
                                o'chirmoqchimisiz?</p>
                            <input type="hidden" name="id" value="<?= $user['id'] ?>">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                            <button type="submit" class="btn btn-danger">O'chirish</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<script>
    document.querySelectorAll('.edit-user-form').forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch('users.php', {
                method: 'POST',
                body: formData
            })
                .then(res => res.json())
                .then(result => {
                    Swal.fire({
                        icon: result.success ? 'success' : 'error',
                        title: result.title,
                        text: result.message
                    }).then(() => {
                        if (result.success) window.location.reload();
                    });
                })
                .catch(err => {
                    Swal.fire({
                        icon: 'error',
                        title: '❌ Xatolik',
                        text: 'Server bilan aloqa uzildi!'
                    });
                    console.error(err);
                });
        });
    });
</script>

<?php include './footer.php'; ?>