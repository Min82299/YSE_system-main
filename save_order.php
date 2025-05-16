<?php
// データベース接続を読み込む
require_once 'db.php';
// レスポンス形式をJSONに設定
header("Content-Type: application/json");

// クライアントからデータを受け取る
$data = json_decode(file_get_contents("php://input"), true);
$cart = $data['cart'];
$paid = intval($data['paid']); // お客様からの支払い金額

// 注文IDを現在のタイムスタンプとして設定
$order_id = time();
$success = false;

if (!empty($cart)) {
    // 現在の日時を取得
    $now = date("Y-m-d H:i:s");

    // 税金を含まない合計金額を計算
    $subtotal = 0;
    foreach ($cart as $item) {
        $subtotal += $item['qty'] * $item['price'];
    }

    // 税金の計算 (8%の消費税)
    $tax_total = floor($subtotal * 0.08);
    $total_with_tax = $subtotal + $tax_total;
    $change = $paid - $total_with_tax; // お釣り

    foreach ($cart as $item) {
        $name = mysqli_real_escape_string($conn, $item['name']);
        $qty = intval($item['qty']);
        $price = intval($item['price']);
        $total = $qty * $price;
        $tax = floor($total * 0.08); // 個別商品の税金計算
        $total_with_tax = $total + $tax; // 税込み合計

        // sales_historyテーブルにデータを挿入
        $sql = "INSERT INTO sales_history 
           (order_id, created_at, product_name, quantity, total, tax, paid, `change`)
           VALUES 
           ($order_id, '$now', '$name', $qty, $total_with_tax, $tax, $paid, $change)";
        mysqli_query($conn, $sql); // データベースに挿入実行
    }

    $success = true;
}

// 処理結果をJSON形式で返す
echo json_encode([
    "success" => $success,
    "order_id" => $order_id
]);
?>

