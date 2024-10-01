<?php
session_start();

// Phần này dùng để kết nối đến CSDL trong MySQL dùng PDO MySQL

include 'functions.php';
$pdo = pdo_connect_mysql();

// Trang này cài đặt trang Home Page (home.php) làm mặc định
// Tất cả các khách hàng vào sẽ nhìn thấy trang Home đầu tiên.

$page = isset($_GET['page']) && file_exists($_GET['page'] . '.php') ? $_GET['page'] : 'home';

// Lệnh Include gọi trang
include $page . '.php';
?>
