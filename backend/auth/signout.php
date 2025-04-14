<?php
session_start();
session_unset();
session_destroy();

if (ini_get("session.use_cookies")) {
    setcookie(session_name(), '', time() - 42000, '/');
}

header("Location: ../../frontend/signin.html?logged_out=1");
exit;
