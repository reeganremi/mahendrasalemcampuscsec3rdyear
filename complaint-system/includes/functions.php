<?php
if (!defined('BASE_URL')) {
    $proto = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS']!== 'off')? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST']?? 'localhost';
    // For workspace proxy
    if (strpos($host,'.app')!==false) {
        define('BASE_URL', '');
    } else {
        define('BASE_URL', $proto.'://'.$host);
    }
}
function e($s){ return htmlspecialchars($s?? '', ENT_QUOTES, 'UTF-8'); }
function csrf_token(){ if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token']=bin2hex(random_bytes(32)); return $_SESSION['csrf_token']; }
function verify_csrf($t){ return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $t?? ''); }
function is_logged_in(){ return isset($_SESSION['user_id']); }
function redirect($url){
    if ($url[0]!=='/' && strpos($url,'http')!==0) $url = '/'.$url;
    header("Location: $url"); exit;
}
function set_flash($msg,$type='success'){ $_SESSION['flash']=['message'=>$msg,'type'=>$type]; }
function get_flash(){ $f=$_SESSION['flash']??null; unset($_SESSION['flash']); return $f; }
function complaint_ref($id){ return 'CMP'.str_pad($id,6,'0',STR_PAD_LEFT); }
function status_badge_class($s){ $m=['Pending'=>'badge-pending','In Progress'=>'badge-progress','Resolved'=>'badge-resolved','Closed'=>'badge-closed']; return $m[$s]??'badge-pending'; }
?>
