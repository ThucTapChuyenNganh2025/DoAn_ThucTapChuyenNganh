<?php
session_start();
session_destroy();
header("Location: dangnhapadmin.php");
exit();