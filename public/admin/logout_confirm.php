<?php
// admin/logout_confirm.php
session_start();
session_unset();
session_destroy();

header("Location: login.php");
exit;
