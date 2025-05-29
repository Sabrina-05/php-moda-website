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

    $name = trim($_POST['name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $role = trim($_POST['role'] ?? 'user');
    $password = trim($_POST['password'] ?? '');

    if (empty($name) || empty($username) || empty($password)) {
        echo json_encode([
            "success" => false,
            "title" => "⚠️ Diqqat!",
            "message" => "Ism, username va parol to‘ldirilishi kerak!"
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

    $check = $db->select("users", "*", "username = ?", [$username], 's');
    if ($check) {
        echo json_encode([
            "success" => false,
            "title" => "❌ Username band!",
            "message" => "Bunday username allaqachon mavjud!"
        ]);
        exit;
    }

    $inserted = $db->insert("users", [
        "name" => $name,
        "username" => $username,
        "role" => $role,
        "password" => password_hash($password, PASSWORD_DEFAULT)
    ]);

    echo json_encode([
        "success" => $inserted,
        "title" => $inserted ? "✅ Qo‘shildi!" : "😕 Xatolik",
        "message" => $inserted ? "Foydalanuvchi muvaffaqiyatli qo‘shildi!" : "Qo‘shishda muammo yuz berdi"
    ]);
    exit;
}
?>

<?php include './header.php'; ?>

<div class="row">
    <div class="container">
        <form id="add-user-form" class="mb-4">
            <div class="mb-3">
                <label class="form-label">Ism</label>
                <input type="text" class="form-control" name="name" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" class="form-control" name="username" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Parol</label>
                <input type="password" class="form-control" name="password" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Rol</label>
                <select class="form-select" name="role">
                    <option value="user">User</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Qo‘shish</button>
            <a href="users.php" class="btn btn-secondary">Orqaga</a>
        </form>
    </div>
</div>

<script>
    document.querySelectorAll('#add-user-form').forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch('', {
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
                        if (result.success) window.location.href = './users.php'
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