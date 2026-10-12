<?php
// Secure DB config - supports both JSON and MySQL
// For MySQL, use PDO with prepared statements
$use_mysql = false; // Set true when using real MySQL
if($use_mysql){
  $host='localhost'; $db='db_455fprc7h'; $user='root'; $pass=''; $charset='utf8mb4';
  $dsn="mysql:host=$host;dbname=$db;charset=$charset";
  $options=[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC, PDO::ATTR_EMULATE_PREPARES=>false];
  try{ $pdo=new PDO($dsn,$user,$pass,$options); }catch(Exception $e){ die("DB Error: ".htmlspecialchars($e->getMessage())); }
} else {
  $pdo=null; // Using secure JSON with hashed passwords
}
?>
