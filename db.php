<?php
// データベース接続情報
$servername = "localhost";  // サーバー名
$username = "root";         // ユーザー名
$password = "";             // パスワード
$dbname = "cobini_pos";     // データベース名

// データベース接続を作成
$conn = mysqli_connect($servername, $username, $password, $dbname);

// 接続チェック
if (!$conn) {
    die("接続失敗: " . mysqli_connect_error());
}

// 文字セットを設定
mysqli_set_charset($conn, "utf8");
?>

