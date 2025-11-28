<?php
header('Content-Type: text/html; charset=utf-8');
require_once __DIR__ . '/../config/connect.php';
@mysqli_select_db($conn, 'webchotot');

$out = '';
$out .= '<option value="0">-- Chọn tỉnh / địa điểm --</option>' . "\n";
$res = $conn->query("SELECT id, province, district FROM locations ORDER BY province ASC, district ASC LIMIT 1000");
if ($res && $res->num_rows > 0) {
    while ($r = $res->fetch_assoc()) {
        $text = htmlspecialchars($r['province'] . (!empty($r['district']) ? ' - ' . $r['district'] : ''), ENT_QUOTES, 'UTF-8');
        $out .= '<option value="' . intval($r['id']) . '">' . $text . '</option>' . "\n";
    }
} else {
    // fallback small list
    $fallback = [1=>'An Giang',2=>'Bắc Ninh',3=>'Cà Mau',29=>'TP.Hà Nội',31=>'TP.Hồ Chí Minh'];
    foreach ($fallback as $k=>$v) $out .= '<option value="'.intval($k).'">'.htmlspecialchars($v, ENT_QUOTES, 'UTF-8').'</option>' . "\n";
}

echo $out;
exit;
?>
