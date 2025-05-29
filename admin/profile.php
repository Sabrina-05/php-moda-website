<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: ../login/');
    exit;
}

include '../config.php';
$db = new Database();

$userId = $_SESSION['user']['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    $name = trim($_POST['name']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    if (empty($name) || empty($username)) {
        echo json_encode([
            "success" => false,
            "title" => "⚠️ Diqqat!",
            "message" => "Ismingiz va username to‘ldirilishi kerak!"
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

    // Username boshqa foydalanuvchi tomonidan band emasligini tekshirish
    $existingUser = $db->select('users', '*', "username = ? AND id != ?", [$username, $userId], 'si');
    if (!empty($existingUser)) {
        echo json_encode([
            "success" => false,
            "title" => "❌ Username band!",
            "message" => "Bu username allaqachon mavjud, boshqa tanlang!"
        ]);
        exit;
    }

    $updateData = [
        'name' => $name,
        'username' => $username
    ];

    if (!empty($password) || !empty($confirm_password)) {
        if ($password !== $confirm_password) {
            echo json_encode([
                "success" => false,
                "title" => "❌ Parollar mos emas!",
                "message" => "Iltimos, parollar bir xil bo‘lishi kerak!"
            ]);
            exit;
        }
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $updateData['password'] = $hashedPassword;
    }

    $updated = $db->update('users', $updateData, "id = ?", [$userId], 'i');

    if ($updated) {
        $_SESSION['user']['name'] = $name;
        $_SESSION['user']['username'] = $username;

        echo json_encode([
            "success" => true,
            "title" => "✅ Muvaffaqiyat!",
            "message" => "Ma'lumotlar muvaffaqiyatli yangilandi!"
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "title" => "😕 Hech narsa o‘zgarmadi",
            "message" => "Ehtimol siz hech nima o‘zgartirmadingiz yoki xatolik yuz berdi!"
        ]);
    }
    exit;
}

$user = $db->select('users', '*', "id = ?", [$userId], 'i')[0];
?>

<?php include './header.php'; ?>

<div class="row">
    <div class="container mt-5">
        <h2 class="mb-4">👤 Profil Ma'lumotlari</h2>

        <form id="profileForm">
            <div class="mb-3">
                <label for="name" class="form-label">To‘liq ismingiz</label>
                <input type="text" class="form-control" id="name" name="name"
                    value="<?= htmlspecialchars($user['name']) ?>" required>
            </div>

            <div class="mb-3">
                <label for="username" class="form-label">Username (o‘zgartirish mumkin)</label>
                <input type="text" class="form-control" id="username" name="username"
                    value="<?= htmlspecialchars($user['username']) ?>" required
                    placeholder="3-20 ta harf, raqam yoki _">
            </div>

            <div class="mb-3 position-relative">
                <label for="password" class="form-label">Yangi parol (agar o‘zgartirmoqchi bo‘lsangiz)</label>
                <div class="input-group">
                    <input type="password" id="password" class="form-control" name="password"
                        placeholder="Yangi parol kiriting">
                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password')">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <div class="mb-3 position-relative">
                <label for="confirm_password" class="form-label">Parolni tasdiqlang</label>
                <div class="input-group">
                    <input type="password" id="confirm_password" class="form-control" name="confirm_password"
                        placeholder="Parolni takrorlang">
                    <button class="btn btn-outline-secondary" type="button"
                        onclick="togglePassword('confirm_password')">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100">💾 Saqlash</button>
        </form>
    </div>

    <script>
        function togglePassword(fieldId) {
            const input = document.getElementById(fieldId);
            const button = input.nextElementSibling;
            const icon = button.querySelector('i');

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        document.getElementById('profileForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(this);

            fetch('', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
                .then(response => response.json())
                .then(result => {
                    Swal.fire({
                        icon: result.success ? 'success' : 'error',
                        title: result.title,
                        text: result.message
                    }).then(() => {
                        if (result.success) {
                            window.location.reload();
                        }
                    });
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: '❌ Tarmoq xatosi',
                        text: 'Server bilan bog‘lanishda muammo yuz berdi.'
                    });
                    console.error('Fetch error:', error);
                });
        });
    </script>

</div>

<?php include './footer.php'; ?>