<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../front_end/signin.html");
    exit;
}
