<?php
// Carrega os controllers responsáveis pelos endpoints.
require_once __DIR__ . '/app/Controllers/AtendimentosController.php';
require_once __DIR__ . '/app/Controllers/TiposAtendimentosController.php';
require_once __DIR__ . '/app/Controllers/UsuarioController.php';
require_once __DIR__ . '/app/Controllers/PessoasController.php'; // Importação do novo controller (Atividade 20)

// Define controller e action por query string.
$controller = $_GET['controller'] ?? 'home';
$action = $_GET['action'] ?? 'index';

// Roteador tratando múltiplos controllers com if / elseif
if ($controller === 'usuarios') {
    $usuariosController = new UsuariosController();

    // Escolhe qual método do controller executar.
    switch ($action) {
        case 'listar':
            $usuariosController->listar();
            break;

        case 'buscar':
            $usuariosController->buscarPorId();
            break;

        case 'criar':
            $usuariosController->criar();
            break;

        case 'atualizar':
            $usuariosController->atualizar();
            break;

        case 'excluir':
            $usuariosController->excluir();
            break;

        default:
            echo 'Ação de usuários não encontrada.';
            break;
    }
} elseif ($controller === 'pessoas') { // Nova rota adicionada para atender a tabela pessoas
    $pessoasController = new PessoasController();

    // Escolhe qual método do controller de pessoas executar.
    switch ($action) {
        case 'listar':
            $pessoasController->listar();
            break;

        case 'buscar':
            $pessoasController->buscarPorId();
            break;

        case 'criar':
            $pessoasController->criar();
            break;

        case 'atualizar':
            $pessoasController->atualizar();
            break;

        case 'excluir':
            $pessoasController->excluir();
            break;

        default:
            echo 'Ação de pessoas não encontrada.';
            break;
    }

    } elseif ($controller === 'tipos_atendimentos') {
    $tiposController = new TiposAtendimentosController();

    switch ($action) {
        case 'listar':
            $tiposController->listar();
            break;
        case 'buscar':
            $tiposController->buscarPorId();
            break;
        case 'criar':
            $tiposController->criar();
            break;
        case 'atualizar':
            $tiposController->atualizar();
            break;
        case 'excluir':
            $tiposController->excluir();
            break;
        default:
            echo 'Ação de tipos_atendimentos não encontrada.';
            break;
    }
    
}
     elseif ($controller === 'atendimentos') {
    $atendimentosController = new AtendimentosController();

    switch ($action) {
        case 'listar':
            $atendimentosController->listar();
            break;
        case 'visualizar':
            $atendimentosController->visualizar();
            break;
        case 'criar':
            $atendimentosController->criar();
            break;
        case 'atualizar_status':
            $atendimentosController->atualizarStatus();
            break;
        default:
            echo 'Ação de atendimentos não encontrada.';
            break;
    }
}
 else {
    // Resposta básica para indicar que a aplicação está no ar.
    echo '<h1>AtendeLab</h1>';
    echo '<p>Projeto em execução. Use ?controller=usuarios&action=listar ou ?controller=pessoas&action=listar para testar.</p>';
}