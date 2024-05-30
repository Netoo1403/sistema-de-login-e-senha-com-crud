<?php
// Inicia a sessão
session_start();

// Desfaz todas as variáveis de sessão
$_SESSION = array();

// Destroi a sessão
session_destroy();

// Exibe uma mensagem de deslogado em forma de popup
echo "<script>alert('Você foi deslogado com sucesso!');</script>";

// Redireciona para a página de login
echo "<script>window.location.href='login.php';</script>";
exit;
?>
