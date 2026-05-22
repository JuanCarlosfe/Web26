<?php
session_start();
session_destroy(); // Destruye todas las sesiones del navegador
header("Location: index.html");
exit();