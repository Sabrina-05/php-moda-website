<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../login/');
    exit;
}

include '../config.php';
$db = new Database();

$productCount = $db->executeQuery("SELECT COUNT(*) AS total FROM products")->get_result()->fetch_all(MYSQLI_ASSOC)[0]['total'];
$categoryCount = $db->executeQuery("SELECT COUNT(*) AS total FROM categories")->get_result()->fetch_all(MYSQLI_ASSOC)[0]['total'];
$userCount = $db->executeQuery("SELECT COUNT(*) AS total FROM users")->get_result()->fetch_all(MYSQLI_ASSOC)[0]['total'];
$visitorCount = $db->executeQuery("SELECT COUNT(DISTINCT user_id) AS total FROM cards")->get_result()->fetch_all(MYSQLI_ASSOC)[0]['total'];
?>

<?php include './header.php'; ?>

<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3><?= $productCount ?></h3>
                <p>Mahsulotlar</p>
            </div>
            <div class="icon">
                <i class="ion ion-bag"></i>
            </div>
            <a href="#" class="small-box-footer">Batafsil <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3><?= $categoryCount ?></h3>
                <p>Kategoriyalar</p>
            </div>
            <div class="icon">
                <i class="ion ion-stats-bars"></i>
            </div>
            <a href="#" class="small-box-footer">Batafsil <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3><?= $userCount ?></h3>
                <p>Foydalanuvchilar</p>
            </div>
            <div class="icon">
                <i class="ion ion-person-add"></i>
            </div>
            <a href="#" class="small-box-footer">Batafsil <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3><?= $visitorCount ?></h3>
                <p>Xaridorlar</p>
            </div>
            <div class="icon">
                <i class="ion ion-pie-graph"></i>
            </div>
            <a href="#" class="small-box-footer">Batafsil <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
</div>

<?php include './footer.php'; ?>