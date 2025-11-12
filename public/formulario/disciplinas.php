<?php
session_start();

// Inicializa o array de disciplinas na sessão, se ainda não existir
if (!isset($_SESSION['disciplinas'])) {
    $_SESSION['disciplinas'] = [];
}

// Adicionar disciplina
if (isset($_POST['acao']) && $_POST['acao'] === 'adicionar') {
    $nome = trim($_POST['nome']);
    $carga = trim($_POST['carga']);

    if ($nome !== '' && $carga !== '') {
        $nova = [
            'id' => uniqid(),
            'nome' => $nome,
            'carga' => $carga
        ];
        $_SESSION['disciplinas'][] = $nova;
    }
    header("Location: disciplinas.php");
    exit;
}

// Excluir disciplina
if (isset($_GET['excluir'])) {
    $idExcluir = $_GET['excluir'];
    $_SESSION['disciplinas'] = array_filter($_SESSION['disciplinas'], function($d) use ($idExcluir) {
        return $d['id'] !== $idExcluir;
    });
    header("Location: disciplinas.php");
    exit;
}

// Editar disciplina
if (isset($_POST['acao']) && $_POST['acao'] === 'editar') {
    $idEditar = $_POST['id'];
    foreach ($_SESSION['disciplinas'] as &$disc) {
        if ($disc['id'] === $idEditar) {
            $disc['nome'] = $_POST['nome'];
            $disc['carga'] = $_POST['carga'];
            break;
        }
    }
    header("Location: disciplinas.php");
    exit;
}

// Buscar disciplina para edição
$editar = null;
if (isset($_GET['editar'])) {
    $id = $_GET['editar'];
    foreach ($_SESSION['disciplinas'] as $d) {
        if ($d['id'] === $id) {
            $editar = $d;
            break;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>CRUD de Disciplinas (Sessão + Bootstrap)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <h1 class="text-center mb-4">CRUD de Disciplinas (Salvas em Sessão)</h1>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="post" class="row g-3">
                <input type="hidden" name="acao" value="<?= $editar ? 'editar' : 'adicionar' ?>">
                <?php if ($editar): ?>
                    <input type="hidden" name="id" value="<?= $editar['id'] ?>">
                <?php endif; ?>

                <div class="col-md-6">
                    <label class="form-label">Nome da disciplina:</label>
                    <input type="text" name="nome" value="<?= $editar['nome'] ?? '' ?>" class="form-control" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Carga horária (h):</label>
                    <input type="number" name="carga" value="<?= $editar['carga'] ?? '' ?>" class="form-control" required>
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn <?= $editar ? 'btn-warning' : 'btn-primary' ?> w-100">
                        <?= $editar ? 'Salvar' : 'Adicionar' ?>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-striped align-middle">
                <thead class="table-dark text-center">
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Carga Horária</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                <?php if (empty($_SESSION['disciplinas'])): ?>
                    <tr><td colspan="4" class="text-muted">Nenhuma disciplina cadastrada.</td></tr>
                <?php else: ?>
                    <?php foreach ($_SESSION['disciplinas'] as $d): ?>
                        <tr>
                            <td><?= htmlspecialchars($d['id']) ?></td>
                            <td><?= htmlspecialchars($d['nome']) ?></td>
                            <td><?= htmlspecialchars($d['carga']) ?>h</td>
                            <td>
                                <a href="?editar=<?= $d['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
                                <a href="?excluir=<?= $d['id'] ?>" class="btn btn-sm btn-danger"
                                   onclick="return confirm('Excluir esta disciplina?')">Excluir</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="text-center mt-4">
        <a href="?reset=1" class="btn btn-outline-secondary">Limpar Sessão</a>
    </div>
</div>

</body>
</html>