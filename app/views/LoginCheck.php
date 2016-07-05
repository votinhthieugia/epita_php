<?php
session_start();

if (!isset($_SESSION['login_id'])) {
  header("Location: /epita_php/app/views/Login.php");
  return;
}

?>
