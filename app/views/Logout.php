<?php

session_start();
unset($_SESSION['login_id']);
unset($_SESSION['email']);
unset($_SESSION['password']);

header("Location: /epita_php/login");

?>
