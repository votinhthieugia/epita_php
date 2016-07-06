<?php
session_start();

if (!isset($_SESSION['login_id'])) {
  header("Location: /epita_php/login");
  return;
}

?>
