<?php
session_start();
echo "<h1>DEBUG MODE</h1>";
echo "<pre>";
echo "SESSION: ";
print_r($_SESSION);
echo "</pre>";
echo "<p>Time: " . date('Y-m-d H:i:s') . "</p>";
?>
<p><a href="login.php?clear=1">Go to Login</a></p>