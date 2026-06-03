<?php

try {
    $host = "localhost";
    $banco = "aula_univille";
    $usuario = "root";
    $senha = "";

    $pdo = new PDO(
        "mysql:host=$host;port=$porta;dbname=$banco;charset=utf8",
        $usuario,
        $senha
    );
    echo "Conexão realizada com sucesso!";
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
}

?>