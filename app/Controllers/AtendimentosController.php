<?php

class AtendimentosController
{
    private PDO $pdo;

    public function __construct()
    {
        require __DIR__ . '/../../config/database.php';
        $this->pdo = $pdo;
    }

    // 1. LISTAR COM JOIN (Traz os nomes vinculados em vez de só IDs)
    public function listar(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        
        $sql = 'SELECT a.id, a.pessoa_id, p.nome AS pessoa_nome, 
                       a.tipo_atendimento_id, t.descricao AS tipo_descricao, 
                       a.usuario_id, u.nome AS usuario_nome, 
                       a.observacoes, a.status, a.data_atendimento 
                FROM atendimentos a
                INNER JOIN pessoas p ON a.pessoa_id = p.id
                INNER JOIN tipos_atendimentos t ON a.tipo_atendimento_id = t.id
                INNER JOIN usuarios u ON a.usuario_id = u.id
                ORDER BY a.id DESC';
                
        $stmt = $this->pdo->query($sql);
        $atendimentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode($atendimentos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    // 2. VISUALIZAR (Busca detalhada de um único atendimento por ID com JOIN)
    public function visualizar(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            http_response_code(400);
            echo json_encode(['erro' => 'ID inválido para visualização.']);
            return;
        }

        $sql = 'SELECT a.id, a.pessoa_id, p.nome AS pessoa_nome, 
                       a.tipo_atendimento_id, t.descricao AS tipo_descricao, 
                       a.usuario_id, u.nome AS usuario_nome, 
                       a.observacoes, a.status, a.data_atendimento 
                FROM atendimentos a
                INNER JOIN pessoas p ON a.pessoa_id = p.id
                INNER JOIN tipos_atendimentos t ON a.tipo_atendimento_id = t.id
                INNER JOIN usuarios u ON a.usuario_id = u.id
                WHERE a.id = :id';
                
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        $atendimento = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$atendimento) {
            http_response_code(404);
            echo json_encode(['erro' => 'Atendimento não encontrado.']);
            return;
        }
        
        echo json_encode($atendimento, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    // 3. CRIAR (Insere o atendimento com o status inicial)
    public function criar(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        
        $pessoa_id = filter_input(INPUT_POST, 'pessoa_id', FILTER_VALIDATE_INT);
        $tipo_atendimento_id = filter_input(INPUT_POST, 'tipo_atendimento_id', FILTER_VALIDATE_INT);
        $usuario_id = filter_input(INPUT_POST, 'usuario_id', FILTER_VALIDATE_INT);
        $observacoes = trim($_POST['observacoes'] ?? '');
        $status = trim($_POST['status'] ?? 'em_andamento'); // Se não enviar, assume 'em_andamento'
        
        if (!$pessoa_id || !$tipo_atendimento_id || !$usuario_id || $observacoes === '') {
            http_response_code(400);
            echo json_encode(['erro' => 'Campos obrigatórios ausentes.']);
            return;
        }

        try {
            $sql = 'INSERT INTO atendimentos (pessoa_id, tipo_atendimento_id, usuario_id, observacoes, status) 
                    VALUES (:pessoa_id, :tipo_atendimento_id, :usuario_id, :observacoes, :status)';
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':pessoa_id', $pessoa_id, PDO::PARAM_INT);
            $stmt->bindValue(':tipo_atendimento_id', $tipo_atendimento_id, PDO::PARAM_INT);
            $stmt->bindValue(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $stmt->bindValue(':observacoes', $observacoes);
            $stmt->bindValue(':status', $status);
            $stmt->execute();
            
            http_response_code(201);
            echo json_encode([
                'mensagem' => 'Atendimento registrado com sucesso.',
                'id' => $this->pdo->lastInsertId()
            ], JSON_UNESCAPED_UNICODE);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao registrar atendimento: ' . $e->getMessage()]);
        }
    }

    // 4. ATUALIZAR STATUS (Altera apenas a situação do atendimento)
    public function atualizarStatus(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $status = trim($_POST['status'] ?? '');
        
        if (!$id || $status === '') {
            http_response_code(400);
            echo json_encode(['erro' => 'ID e Status são obrigatórios para esta atualização.']);
            return;
        }

        try {
            $sql = 'UPDATE atendimentos SET status = :status WHERE id = :id';
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':status', $status);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            echo json_encode(['mensagem' => 'Status do atendimento atualizado com sucesso.'], JSON_UNESCAPED_UNICODE);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao atualizar status: ' . $e->getMessage()]);
        }
    }
}