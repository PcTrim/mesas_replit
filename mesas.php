<?php
header('Content-Type: text/html; charset=UTF-8');

// ============================================
// COMANDA ELETRONICA - SISTEMA PDV
// Versao: 2.0 - Janeiro 2026
// ============================================

// Receber dados do garcom (vem da tela anterior)
$btngarcon = isset($_POST['btngarcon']) ? $_POST['btngarcon'] : '';

// Receber numero da mesa
$nmesa = isset($_POST['nmesa']) ? $_POST['nmesa'] : '';
$nropessoas = isset($_POST['nropessoas']) && $_POST['nropessoas'] != '' ? intval($_POST['nropessoas']) : 1;
$txtnropessoas = isset($_POST['txtnropessoas']) && $_POST['txtnropessoas'] != '' ? intval($_POST['txtnropessoas']) : $nropessoas;
if($txtnropessoas < 1) $txtnropessoas = 1;
$txtdividir = isset($_POST['txtdividir']) ? $_POST['txtdividir'] : '';

// Receber produto selecionado
$btnprod = isset($_POST['btnprod']) ? $_POST['btnprod'] : '';
$keyprod = isset($_POST['keyprod']) ? $_POST['keyprod'] : '';
$vlprod = isset($_POST['vlprod']) ? $_POST['vlprod'] : '';
$txtcodigopro = isset($_POST['txtcodigopro']) ? $_POST['txtcodigopro'] : '';

// Receber dados para incluir produto
$txtincluir = isset($_POST['txtincluir']) ? $_POST['txtincluir'] : '';
$txtnmesa = isset($_POST['txtnmesa']) ? $_POST['txtnmesa'] : '';
$txtcodprod = isset($_POST['txtcodprod']) ? $_POST['txtcodprod'] : '';
$txtnmprod = isset($_POST['txtnmprod']) ? $_POST['txtnmprod'] : '';
$txtvlprod = isset($_POST['txtvlprod']) ? $_POST['txtvlprod'] : '';
$txtnmclaprod = isset($_POST['txtnmclaprod']) ? $_POST['txtnmclaprod'] : '';
$txtimpprod = isset($_POST['txtimpprod']) ? $_POST['txtimpprod'] : '';
$txtimposto = isset($_POST['txtimposto']) ? $_POST['txtimposto'] : '';
$txtlancprod = isset($_POST['txtlancprod']) ? $_POST['txtlancprod'] : '';
$txtobs = isset($_POST['txtobs']) ? $_POST['txtobs'] : '';
$QTPROD = isset($_POST['QTPROD']) ? $_POST['QTPROD'] : array();

// Receber acoes
$btnexcluir = isset($_POST['btnexcluir']) ? $_POST['btnexcluir'] : '';
$btnenviar = isset($_POST['btnenviar']) ? $_POST['btnenviar'] : '';
$btnfecharconta = isset($_POST['btnfecharconta']) ? $_POST['btnfecharconta'] : '';
$btnliberarmesa = isset($_POST['btnliberarmesa']) ? $_POST['btnliberarmesa'] : '';
$btnocuparmesa = isset($_POST['btnocuparmesa']) ? $_POST['btnocuparmesa'] : '';

// Permissoes do garcom
$txtpermitiraddconsumacao = isset($_POST['txtpermitiraddconsumacao']) ? $_POST['txtpermitiraddconsumacao'] : '';
$txtpermitirfecharconta = isset($_POST['txtpermitirfecharconta']) ? $_POST['txtpermitirfecharconta'] : '';

// ============================================
// PROCESSAR ACOES
// ============================================

// Incluir produto na mesa
if($txtincluir == 'sim' && $txtnmesa != '' && $txtcodprod != '') {
        include("conexaomodulomesa.php");
        // Todas as operações de banco já foram feitas acima
        mysqli_close($con);
        // Não faz redirecionamento automático, deixa o POST seguir normalmente
    include("conexaomodulomesa.php");
    $qtd = isset($QTPROD[0]) ? $QTPROD[0] : 1;
    $obsesc = mysqli_real_escape_string($con, $txtobs);
    $nrpessoas = isset($txtnropessoas) && is_numeric($txtnropessoas) ? intval($txtnropessoas) : 1;
    $sql_insert = "INSERT INTO mesas (MesaNro, MercadoriaCodigo, Mercadoria, Quanti, valor, Ficacao, Impressora, poposto, Lancamento, obs, producao, Servidopor, NroPessoas, equipamento)
                   VALUES ($txtnmesa, '$txtcodprod', '$txtnmprod', $qtd, $txtvlprod, '$txtnmclaprod', '$txtimpprod', '$txtimposto', $txtlancprod, '$obsesc', 'Nao', '$btngarcon', $nrpessoas, '$btngarcon')";
    mysqli_query($con, $sql_insert);

    // Marcar mesa como ocupada ou atualizar status
    if($txtnmesa != '' && is_numeric($txtnmesa)) {
        $mesaNro = intval($txtnmesa);
        $sql_check = mysqli_query($con, "SELECT * FROM mesaocupada WHERE MesaNro = $mesaNro");
        if($sql_check && mysqli_num_rows($sql_check) == 0) {
            mysqli_query($con, "INSERT INTO mesaocupada (MesaNro, status) VALUES ($mesaNro, 'OCUPADA')");
        } else {
            // Se já existe, garante que o status seja OCUPADA
            mysqli_query($con, "UPDATE mesaocupada SET status = 'OCUPADA' WHERE MesaNro = $mesaNro");
        }
    }
    mysqli_close($con);
}

// Excluir item da mesa
if($btnexcluir != '') {
    include("conexaomodulomesa.php");
    mysqli_query($con, "DELETE FROM mesas WHERE KeyChave = $btnexcluir");
    mysqli_close($con);
}

// Enviar para producao
if($btnenviar != '') {
    include("conexaomodulomesa.php");
    mysqli_query($con, "UPDATE mesas SET producao = 'Sim' WHERE MesaNro = $btnenviar AND producao = 'Nao'");
    mysqli_close($con);
}

// Fechar conta
if($btnfecharconta != '') {
    include("conexaomodulomesa.php");
    // Verifica se existe o campo 'statusmesa', se não, tenta atualizar o campo 'status'
    $sql_test = mysqli_query($con, "SHOW COLUMNS FROM mesaocupada LIKE 'statusmesa'");
    if ($sql_test && mysqli_num_rows($sql_test) > 0) {
        $sql_update = "UPDATE mesaocupada SET statusmesa = 'CONTA' WHERE MesaNro = $btnfecharconta";
    } else {
        $sql_update = "UPDATE mesaocupada SET status = 'CONTA' WHERE MesaNro = $btnfecharconta";
    }
    $res = mysqli_query($con, $sql_update);
    echo '<div style="color:orange;font-size:0.9em;"><b>DEBUG:</b> FECHAR CONTA mesa=' . htmlspecialchars($btnfecharconta) . ' | resultado=' . (($res)?'OK':'ERRO') . ' | erro=' . mysqli_error($con) . ' | SQL=' . htmlspecialchars($sql_update) . '</div>';
    mysqli_close($con);
}

// Liberar mesa
if($btnliberarmesa != '') {
    include("conexaomodulomesa.php");
    mysqli_query($con, "DELETE FROM mesas WHERE MesaNro = $btnliberarmesa");
    mysqli_query($con, "DELETE FROM mesaocupada WHERE MesaNro = $btnliberarmesa");
    mysqli_close($con);
    $nmesa = '';
}

// Ocupar mesa novamente
if($btnocuparmesa != '') {
    include("conexaomodulomesa.php");
    mysqli_query($con, "UPDATE mesaocupada SET statusmesa = 'OCUPADA' WHERE MesaNro = $btnocuparmesa");
    mysqli_close($con);
}

// ============================================
// BUSCAR DADOS
// ============================================

// Dados do garcom
$addcons = '';
$permfecharconta = '';
$cdgar = '';
$nmgar = '';
if($btngarcon != '') {
    include("conexaomodulomesa.php");
    $sql_garcon = mysqli_query($con, "SELECT * FROM garcon WHERE NomeGarcon = '$btngarcon'");
    if($sql_garcon && $row = mysqli_fetch_assoc($sql_garcon)) {
        $cdgar = $row['CodigoGarcon'];
        $nmgar = $row['NomeGarcon'];
        $addcons = $row['permitiraddconsumacao'];
        $permfecharconta = $row['permitirfecharconta'];
    }
    mysqli_close($con);
}

// Status da mesa
$statusmesa = '';
if($nmesa != '') {
    include("conexaomodulomesa.php");
    $sql_status = mysqli_query($con, "SELECT * FROM mesaocupada WHERE MesaNro = $nmesa");
    if($sql_status && $row = mysqli_fetch_assoc($sql_status)) {
        $statusmesa = $row['status'];
    }
    mysqli_close($con);
}

// Buscar consumacao do sistema
$cconsumacao = '';
$parcialconsumacao = 0;
include("conexaomodulomesa.php");
$sql_sistema = mysqli_query($con, "SELECT * FROM sistema LIMIT 1");
if($sql_sistema && $row = mysqli_fetch_assoc($sql_sistema)) {
    // Verificar se o campo existe antes de usar
    if(isset($row['parcialconsumacao'])) {
        $parcialconsumacao = $row['parcialconsumacao'];
    } elseif(isset($row['Consumacao'])) {
        $parcialconsumacao = $row['Consumacao'];
    }
    if($parcialconsumacao > 0) {
        $cconsumacao = 'Com Consumacao R$' . number_format($parcialconsumacao, 2, ',', '.');
    } else {
        $cconsumacao = 'Sem Consumacao';
    }
}
mysqli_close($con);

// Buscar produto por codigo
if($btnprod == '' && $txtcodigopro != '') {
    include("conexaomodulomesa.php");
    $sql_busca = mysqli_query($con, "SELECT * FROM produtos WHERE KeyChave = $txtcodigopro");
    if($sql_busca && $row = mysqli_fetch_assoc($sql_busca)) {
        $btnprod = $row['Produto'];
    }
    mysqli_close($con);
}

// Dados do produto selecionado
$keyprod2 = '';
$claprod2 = '';
$impprod2 = '';
$imposto2 = '';
$vlprod2 = '0.00';
if($btnprod != '') {
    include("conexaomodulomesa.php");
    $sql_prod = mysqli_query($con, "SELECT * FROM produtos WHERE Produto = '$btnprod'");
    if($sql_prod && $row = mysqli_fetch_assoc($sql_prod)) {
        $keyprod2 = $row['KeyChave'];
        $claprod2 = $row['Classificacao'];
        $impprod2 = $row['Impressora'];
        $imposto2 = $row['Imposto'];
        $vlprod2 = number_format($row['Preco'], 2, '.', '');
    }
    mysqli_close($con);
}

// Buscar ultimo lancamento e numero de pessoas da mesa
$lanc = 1;
$pessoasDoBanco = 0;
if($nmesa != '') {
    include("conexaomodulomesa.php");
    $sql_lanc = mysqli_query($con, "SELECT MAX(Lancamento) as maxlanc, MAX(NroPessoas) as nrpessoas FROM mesas WHERE MesaNro = $nmesa");
    if($sql_lanc && $row = mysqli_fetch_assoc($sql_lanc)) {
        $lanc = ($row['maxlanc'] ? $row['maxlanc'] : 0) + 1;
        $pessoasDoBanco = ($row['nrpessoas'] ? intval($row['nrpessoas']) : 0);
    }
    mysqli_close($con);

    // SEMPRE usar o valor do banco se existir (sobrescreve o padrao)
    if($pessoasDoBanco > 0) {
        $txtnropessoas = $pessoasDoBanco;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>PDV - Comanda Eletronica</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary: #FEAC01;
            --primary-dark: #e09800;
            --bg-dark: #0d1117;
            --bg-card: #161b22;
            --bg-input: #21262d;
            --text-primary: #f0f6fc;
            --text-secondary: #8b949e;
            --success: #238636;
            --danger: #da3633;
            --info: #58a6ff;
            --warning: #d29922;
        }

        :root.tema-claro {
            --bg-dark: #f5f7fa;
            --bg-card: #ffffff;
            --bg-input: #e9ecef;
            --text-primary: #1a1a2e;
            --text-secondary: #6c757d;
        }

        * {
            box-sizing: border-box;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            background: var(--bg-dark);
            color: var(--text-primary);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            min-height: 100vh;
            padding: 0;
            margin: 0;
            transition: background 0.5s ease, color 0.5s ease;
        }

        .tema-indicator {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            background: var(--bg-input);
            border-radius: 15px;
            font-size: 0.8rem;
            color: var(--text-secondary);
            border: 1px solid var(--primary);
        }

        .tema-indicator i {
            color: var(--primary);
        }

        .header-pdv {
            background: var(--bg-card);
            border-bottom: 2px solid var(--primary);
            transition: background 0.5s ease;
            padding: 15px 20px;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .header-pdv .logo {
            height: 50px;
            filter: drop-shadow(0 0 10px rgba(254, 172, 1, 0.3));
        }

        .header-pdv .garcon-name {
            color: var(--primary);
            font-size: 1.4rem;
            font-weight: 700;
        }

        .btn-pdv {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: #000;
            border: none;
            border-radius: 12px;
            padding: 15px 30px;
            font-size: 1.1rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.2s ease;
            box-shadow: 0 4px 15px rgba(254, 172, 1, 0.3);
        }

        .btn-pdv:hover, .btn-pdv:active {
            background: linear-gradient(135deg, var(--primary-dark) 0%, #c78700 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(254, 172, 1, 0.4);
            color: #000;
        }

        .btn-pdv-outline {
            background: transparent;
            color: var(--primary);
            border: 2px solid var(--primary);
            border-radius: 12px;
            padding: 12px 25px;
            font-size: 1rem;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .btn-pdv-outline:hover {
            background: var(--primary);
            color: #000;
        }

        .btn-danger-pdv {
            background: linear-gradient(135deg, var(--danger) 0%, #b62324 100%);
            color: #fff;
            border: none;
            border-radius: 12px;
            padding: 15px 30px;
            font-size: 1.1rem;
            font-weight: 700;
        }

        .btn-success-pdv {
            background: linear-gradient(135deg, var(--success) 0%, #1a7f37 100%);
            color: #fff;
            border: none;
            border-radius: 12px;
            padding: 15px 30px;
            font-size: 1.1rem;
            font-weight: 700;
            box-shadow: 0 4px 15px rgba(35, 134, 54, 0.3);
        }

        .btn-info-pdv {
            background: linear-gradient(135deg, var(--info) 0%, #1f6feb 100%);
            color: #fff;
            border: none;
            border-radius: 12px;
            padding: 15px 30px;
            font-size: 1.1rem;
            font-weight: 700;
        }

        .card-pdv {
            background: var(--bg-card);
            border: 1px solid rgba(254, 172, 1, 0.2);
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }

        .card-pdv-header {
            color: var(--primary);
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid rgba(254, 172, 1, 0.3);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .mesa-input-container {
            background: var(--bg-card);
            border-radius: 20px;
            padding: 25px;
            margin: 20px;
            border: 2px solid var(--primary);
            box-shadow: 0 0 30px rgba(254, 172, 1, 0.15);
        }

        .mesa-number-input {
            background: var(--bg-input);
            border: 2px solid var(--primary);
            border-radius: 15px;
            color: var(--primary);
            font-size: 2.5rem;
            font-weight: 700;
            text-align: center;
            width: 120px;
            height: 70px;
            padding: 10px;
        }

        .mesa-number-input:focus {
            outline: none;
            box-shadow: 0 0 20px rgba(254, 172, 1, 0.4);
        }

        .pessoas-input {
            background: var(--bg-input);
            border: 2px solid var(--primary);
            border-radius: 15px;
            color: var(--primary);
            font-size: 1.5rem;
            font-weight: 700;
            text-align: center;
            width: 80px;
            height: 50px;
        }

        .status-badge {
            padding: 8px 20px;
            border-radius: 25px;
            font-size: 1rem;
            font-weight: 700;
            text-transform: uppercase;
        }

        .status-ocupada {
            background: linear-gradient(135deg, var(--danger) 0%, #b62324 100%);
            color: #fff;
        }

        .status-conta {
            background: linear-gradient(135deg, var(--info) 0%, #1f6feb 100%);
            color: #fff;
        }

        .status-liberada {
            background: linear-gradient(135deg, var(--success) 0%, #1a7f37 100%);
            color: #fff;
        }

        .category-menu {
            display: flex;
            overflow-x: auto;
            gap: 12px;
            padding: 15px 20px;
            background: var(--bg-card);
            border-radius: 15px;
            margin: 20px;
            -webkit-overflow-scrolling: touch;
        }

        .category-menu::-webkit-scrollbar {
            height: 6px;
        }

        .category-menu::-webkit-scrollbar-track {
            background: var(--bg-input);
            border-radius: 3px;
        }

        .category-menu::-webkit-scrollbar-thumb {
            background: var(--primary);
            border-radius: 3px;
        }

        .category-btn {
            flex-shrink: 0;
            background: var(--bg-input);
            color: var(--text-primary);
            border: 1px solid rgba(254, 172, 1, 0.3);
            border-radius: 12px;
            padding: 12px 25px;
            font-size: 1rem;
            font-weight: 600;
            white-space: nowrap;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .category-btn:hover, .category-btn.active {
            background: var(--primary);
            color: #000;
            border-color: var(--primary);
        }

        .product-list {
            padding: 0 20px;
        }

        .category-section {
            margin-bottom: 30px;
        }

        .category-title {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: #000;
            padding: 15px 25px;
            border-radius: 12px;
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .product-card {
            background: var(--bg-card);
            border: 1px solid rgba(254, 172, 1, 0.15);
            border-radius: 12px;
            padding: 18px 20px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            transition: all 0.2s ease;
            width: 100%;
            text-align: left;
        }

        .product-card:hover, .product-card:active {
            background: var(--bg-input);
            border-color: var(--primary);
            transform: translateX(5px);
        }

        .product-info {
            flex: 1;
        }

        .product-code {
            color: var(--text-secondary);
            font-size: 0.85rem;
            margin-bottom: 3px;
        }

        .product-name {
            color: var(--text-primary);
            font-size: 1.15rem;
            font-weight: 600;
        }

        .product-price {
            color: var(--primary);
            font-size: 1.4rem;
            font-weight: 700;
        }

        .table-pdv {
            width: 100%;
            color: var(--text-primary);
        }

        .table-pdv th {
            color: var(--primary);
            font-weight: 600;
            padding: 12px 10px;
            border-bottom: 2px solid var(--primary);
            font-size: 0.95rem;
        }

        .table-pdv td {
            padding: 12px 10px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            vertical-align: middle;
        }

        .table-pdv .total-row td {
            border-top: 2px solid var(--primary);
            font-weight: 700;
            font-size: 1.1rem;
        }

        .table-pdv .total-label {
            color: var(--primary);
        }

        .quantity-modal {
            background: var(--bg-card);
            border: 2px solid var(--primary);
            border-radius: 20px;
            padding: 30px;
            margin: 20px;
            box-shadow: 0 0 50px rgba(254, 172, 1, 0.2);
        }

        .quantity-modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .quantity-modal-title {
            color: var(--primary);
            font-size: 1.5rem;
            font-weight: 700;
        }

        .btn-close-modal {
            background: var(--danger);
            color: #fff;
            border: none;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .product-selected {
            background: var(--bg-input);
            border-radius: 12px;
            padding: 15px 20px;
            margin-bottom: 25px;
            text-align: center;
        }

        .product-selected-name {
            color: var(--text-primary);
            font-size: 1.3rem;
            font-weight: 600;
        }

        .product-selected-price {
            color: var(--primary);
            font-size: 1.8rem;
            font-weight: 700;
        }

        .quantity-controls {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 20px;
            margin-bottom: 25px;
        }

        .btn-quantity {
            background: var(--primary);
            color: #000;
            border: none;
            border-radius: 50%;
            width: 70px;
            height: 70px;
            font-size: 2rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }

        .btn-quantity:hover, .btn-quantity:active {
            background: var(--primary-dark);
            transform: scale(1.1);
        }

        .quantity-input {
            background: var(--bg-input);
            border: 2px solid var(--primary);
            border-radius: 15px;
            color: var(--primary);
            font-size: 2.5rem;
            font-weight: 700;
            text-align: center;
            width: 100px;
            height: 70px;
        }

        .obs-input {
            background: var(--bg-input);
            border: 2px solid rgba(254, 172, 1, 0.3);
            border-radius: 12px;
            color: var(--text-primary);
            font-size: 1.1rem;
            padding: 15px 20px;
            width: 100%;
            margin-bottom: 25px;
        }

        .obs-input:focus {
            outline: none;
            border-color: var(--primary);
        }

        .search-code {
            background: var(--bg-card);
            border-radius: 15px;
            padding: 15px 20px;
            margin: 0 20px 20px;
        }

        .search-code-input {
            background: var(--bg-input);
            border: 2px solid var(--primary);
            border-radius: 10px;
            color: var(--primary);
            font-size: 1.2rem;
            padding: 12px 20px;
            width: 200px;
        }

        .search-code-input:focus {
            outline: none;
            box-shadow: 0 0 15px rgba(254, 172, 1, 0.3);
        }

        .btn-search {
            background: var(--primary);
            color: #000;
            border: none;
            border-radius: 10px;
            padding: 12px 25px;
            font-weight: 700;
        }

        .btn-excluir {
            background: var(--danger);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 8px 15px;
            font-size: 0.9rem;
        }

        .consumacao-badge {
            background: linear-gradient(135deg, var(--warning) 0%, #b88a1d 100%);
            color: #000;
            border: none;
            border-radius: 25px;
            padding: 10px 25px;
            font-weight: 700;
            font-size: 1rem;
        }

        .action-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 20px;
        }

        @media (max-width: 768px) {
            .mesa-number-input {
                font-size: 2rem;
                width: 100px;
                height: 60px;
            }
            .product-price {
                font-size: 1.2rem;
            }
            .btn-pdv, .btn-danger-pdv, .btn-success-pdv {
                padding: 12px 20px;
                font-size: 1rem;
            }
        }
    </style>
    <script>
        function aplicarTemaAutomatico() {
            var hora = new Date().getHours();
            var isDia = (hora >= 6 && hora < 18);
            var indicador = document.getElementById('temaIndicator');

            if(isDia) {
                document.documentElement.classList.add('tema-claro');
                if(indicador) indicador.innerHTML = '<i class="bi bi-sun-fill"></i> Dia';
            } else {
                document.documentElement.classList.remove('tema-claro');
                if(indicador) indicador.innerHTML = '<i class="bi bi-moon-fill"></i> Noite';
            }
        }

        setInterval(aplicarTemaAutomatico, 60000);

        window.addEventListener('DOMContentLoaded', function() {
            aplicarTemaAutomatico();
        });
    </script>
</head>
<body>

<!-- HEADER -->
<div class="header-pdv">
    <div class="d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-3">
            <img src="Imagens/logopctrim.png" alt="Logo" class="logo" onerror="this.style.display='none'">
            <span class="garcon-name"><i class="bi bi-person-badge me-2"></i><?php echo $btngarcon != '' ? $btngarcon : 'PcTrim'; ?></span>
            <span class="tema-indicator d-none d-md-flex" id="temaIndicator"><i class="bi bi-circle-half"></i></span>
        </div>
        <form method="POST" action="index.php" class="d-inline">
            <button type="submit" class="btn-pdv-outline">
                <i class="bi bi-arrow-left me-2"></i>Retornar
            </button>
        </form>
    </div>
</div>

<?php if($btnprod != ''): ?>
<!-- ============================================ -->
<!-- MODAL DE QUANTIDADE -->
<!-- ============================================ -->
<div class="quantity-modal">
    <form method="POST" action="mesas.php">
        <div class="quantity-modal-header">
            <span class="quantity-modal-title"><i class="bi bi-plus-lg me-2"></i>Inserir Quantidade</span>
            <button type="submit" class="btn-close-modal" name="btngarcon" value="<?php echo $btngarcon; ?>">
                <i class="bi bi-x"></i>
            </button>
            <input type="hidden" name="nmesa" value="<?php echo $nmesa; ?>">
        </div>
    </form>

    <form method="POST" action="mesas.php">
        <div class="product-selected">
            <div class="product-selected-name"><?php echo $keyprod2; ?> - <?php echo $btnprod; ?></div>
            <div class="product-selected-price">R$ <?php echo number_format($vlprod2, 2, ',', '.'); ?></div>
        </div>

        <div class="quantity-controls">
            <button type="button" class="btn-quantity" onclick="changeQty(-1)"><i class="bi bi-dash"></i></button>
            <input type="text" name="QTPROD[]" id="qtdInput" class="quantity-input" value="1" readonly>
            <button type="button" class="btn-quantity" onclick="changeQty(1)"><i class="bi bi-plus"></i></button>
        </div>

        <label style="color: var(--primary); font-weight: 600; margin-bottom: 10px; display: block;">Observacao (opcional)</label>
        <input type="text" name="txtobs" class="obs-input" placeholder="Ex: sem acucar, bem passado...">

        <input type="hidden" name="txtincluir" value="sim">
        <input type="hidden" name="txtnmesa" value="<?php echo $nmesa; ?>">
        <input type="hidden" name="txtcodprod" value="<?php echo $keyprod2; ?>">
        <input type="hidden" name="txtnmprod" value="<?php echo $btnprod; ?>">
        <input type="hidden" name="txtvlprod" value="<?php echo $vlprod2; ?>">
        <input type="hidden" name="txtnmclaprod" value="<?php echo $claprod2; ?>">
        <input type="hidden" name="txtimpprod" value="<?php echo $impprod2; ?>">
        <input type="hidden" name="txtimposto" value="<?php echo $imposto2; ?>">
        <input type="hidden" name="txtlancprod" value="<?php echo $lanc; ?>">
        <input type="hidden" name="btngarcon" value="<?php echo $btngarcon; ?>">
        <input type="hidden" name="nmesa" value="<?php echo $nmesa; ?>">
        <input type="hidden" name="txtnropessoas" value="<?php echo $txtnropessoas; ?>">

        <button type="submit" class="btn-success-pdv w-100">
            <i class="bi bi-check-lg me-2"></i>Adicionar ao Pedido
        </button>
    </form>
</div>

<?php else: ?>
<!-- ============================================ -->
<!-- INPUT DA MESA -->
<!-- ============================================ -->
<div class="mesa-input-container">
    <form method="POST" action="mesas.php">
        <div class="text-center mb-3">
            <small class="text-secondary">Informe o numero da mesa. Se necessario, coloque o numero de pessoas.</small>
        </div>

        <div class="row align-items-center g-3 justify-content-center">
            <div class="col-auto">
                <label class="text-secondary">N. Mesa</label>
                <input type="number" name="nmesa" class="mesa-number-input" value="<?php echo $nmesa; ?>" placeholder="N">
            </div>

            <div class="col-auto">
                <?php
                $statusClass = 'status-liberada';
                $statusText = 'LIBERADA';
                if($statusmesa == 'OCUPADA') {
                    $statusClass = 'status-ocupada';
                    $statusText = 'OCUPADA';
                } elseif($statusmesa == 'CONTA') {
                    $statusClass = 'status-conta';
                    $statusText = 'CONTA';
                }
                ?>
                <span class="status-badge <?php echo $statusClass; ?>"><?php echo $statusText; ?></span>
                <!-- debug removido -->
            </div>

            <div class="col-auto">
                <label class="text-secondary">Pessoas</label>
                <input type="number" name="nropessoas" class="pessoas-input" value="<?php echo $txtnropessoas; ?>" min="1">
            </div>

            <div class="col-auto">
                <input type="hidden" name="btngarcon" value="<?php echo $btngarcon; ?>">
                <button type="submit" class="btn-pdv">
                    <i class="bi bi-search me-2"></i>Buscar
                </button>
            </div>
        </div>

        <?php if($cconsumacao != '' && $nmesa != ''): ?>
        <div class="text-center mt-3">
            <span class="consumacao-badge"><?php echo $cconsumacao; ?></span>
        </div>
        <?php endif; ?>
    </form>
</div>

<?php if($nmesa != '' && ($statusmesa == '' || $statusmesa == 'OCUPADA')): ?>
<!-- ============================================ -->
<!-- ITENS DA MESA -->
<!-- ============================================ -->
<div class="card-pdv mx-3">
    <div class="card-pdv-header">
        <i class="bi bi-receipt"></i>Itens da Mesa <?php echo $nmesa; ?>
    </div>

    <div class="table-responsive">
        <table class="table-pdv">
            <thead>
                <tr>
                    <th>Qtd</th>
                    <th>Produto</th>
                    <th>Valor</th>
                    <th>Subtotal</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $total = 0;
                include("conexaomodulomesa.php");
                $sql_itens = mysqli_query($con, "SELECT * FROM mesas WHERE MesaNro = $nmesa ORDER BY Lancamento DESC");
                if($sql_itens) {
                    while($item = mysqli_fetch_assoc($sql_itens)) {
                        $qtditem = isset($item['Quanti']) ? $item['Quanti'] : 0;
                        $valoritem = isset($item['valor']) ? $item['valor'] : 0;
                        $proditem = isset($item['Mercadoria']) ? $item['Mercadoria'] : '';
                        $obsitem = isset($item['obs']) ? $item['obs'] : '';
                        $producaoitem = isset($item['producao']) ? $item['producao'] : '';
                        $subtotal = $qtditem * $valoritem;
                        $total += $subtotal;
                        echo '<tr>';
                        echo '<td>' . $qtditem . '</td>';
                        echo '<td>' . $proditem;
                        if($obsitem != '') {
                            echo '<br><small class="text-secondary">' . $obsitem . '</small>';
                        }
                        echo '</td>';
                        echo '<td>R$ ' . number_format($valoritem, 2, ',', '.') . '</td>';
                        echo '<td>R$ ' . number_format($subtotal, 2, ',', '.') . '</td>';
                        echo '<td>';
                        if($producaoitem == 'Nao') {
                            echo '<form method="POST" action="mesas.php" class="d-inline">';
                            echo '<input type="hidden" name="btngarcon" value="' . $btngarcon . '">';
                            echo '<input type="hidden" name="nmesa" value="' . $nmesa . '">';
                            echo '<button type="submit" name="btnexcluir" value="' . $item['KeyChave'] . '" class="btn-excluir"><i class="bi bi-trash"></i></button>';
                            echo '</form>';
                        }
                        echo '</td>';
                        echo '</tr>';
                    }
                }
                mysqli_close($con);

                // Adicionar consumacao ao total
                $totalComConsumacao = $total;
                if($parcialconsumacao > 0) {
                    $totalComConsumacao = $total + ($parcialconsumacao * $txtnropessoas);
                }
                ?>
                <tr class="total-row">
                    <td colspan="3" class="total-label">TOTAL:</td>
                    <td colspan="2">R$ <?php echo number_format($totalComConsumacao, 2, ',', '.'); ?></td>
                </tr>
                <?php
                $nrPessoasExibir = ($txtnropessoas > 0) ? $txtnropessoas : 1;
                $valorPorPessoa = $totalComConsumacao / $nrPessoasExibir;
                ?>
                <tr class="total-row" style="background: var(--primary); color: #000;">
                    <td colspan="3" class="total-label"><i class="bi bi-people-fill me-2"></i>POR PESSOA (<?php echo $nrPessoasExibir; ?>):</td>
                    <td colspan="2"><strong>R$ <?php echo number_format($valorPorPessoa, 2, ',', '.'); ?></strong></td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Botoes de acao -->
    <div class="action-buttons">
        <?php
        // Só exibe o botão Fechar Conta se todos os itens da mesa tiverem producao = 'Sim'
        $podeFecharConta = false;
        if ($permfecharconta == 's' && $total > 0) {
            include("conexaomodulomesa.php");
            $sql_check_producao = mysqli_query($con, "SELECT COUNT(*) as total, SUM(producao = 'Sim') as enviados FROM mesas WHERE MesaNro = $nmesa");
            if ($sql_check_producao && $row = mysqli_fetch_assoc($sql_check_producao)) {
                if ($row['total'] > 0 && $row['total'] == $row['enviados']) {
                    $podeFecharConta = true;
                }
            }
            mysqli_close($con);
        }
        if ($podeFecharConta): ?>
        <form method="POST" action="mesas.php" class="d-inline">
            <input type="hidden" name="btngarcon" value="<?php echo $btngarcon; ?>">
            <input type="hidden" name="nmesa" value="<?php echo $nmesa; ?>">
            <input type="hidden" name="txtnropessoas" value="<?php echo $txtnropessoas; ?>">
            <button type="submit" name="btnfecharconta" value="<?php echo $nmesa; ?>" class="btn-info-pdv">
                <i class="bi bi-cash-stack me-2"></i>Fechar Conta
            </button>
        </form>
        <?php endif; ?>

        <form method="POST" action="mesas.php" class="d-inline">
            <input type="hidden" name="btngarcon" value="<?php echo $btngarcon; ?>">
            <input type="hidden" name="nmesa" value="<?php echo $nmesa; ?>">
            <input type="hidden" name="txtnropessoas" value="<?php echo $txtnropessoas; ?>">
            <button type="submit" name="btnenviar" value="<?php echo $nmesa; ?>" class="btn-success-pdv">
                <i class="bi bi-printer me-2"></i>Enviar Producao
            </button>
        </form>
    </div>
</div>

<!-- ============================================ -->
<!-- MENU DE CATEGORIAS -->
<!-- ============================================ -->
<div class="category-menu">
    <?php
    include("conexaomodulomesa.php");
    $sql_cat = mysqli_query($con, "SELECT * FROM classificacao WHERE parcial > 0 ORDER BY ordem");
    if($sql_cat) {
        while($cat = mysqli_fetch_assoc($sql_cat)) {
            echo '<a href="#cat_' . md5($cat['Classificacao']) . '" class="category-btn">' . $cat['Classificacao'] . '</a>';
        }
    }
    mysqli_close($con);
    ?>
</div>

<!-- BUSCA POR CODIGO -->
<div class="search-code">
    <form method="POST" action="mesas.php" class="d-flex gap-3 flex-wrap align-items-center justify-content-center">
        <input type="number" name="txtcodigopro" class="search-code-input" placeholder="Codigo do produto">
        <button type="submit" class="btn-search"><i class="bi bi-search me-2"></i>Buscar</button>
        <input type="hidden" name="btngarcon" value="<?php echo $btngarcon; ?>">
        <input type="hidden" name="nmesa" value="<?php echo $nmesa; ?>">
        <input type="hidden" name="txtnropessoas" value="<?php echo $txtnropessoas; ?>">
    </form>
</div>

<!-- ============================================ -->
<!-- CATALOGO DE PRODUTOS -->
<!-- ============================================ -->
<div class="product-list">
    <?php
    include("conexaomodulomesa.php");
    $sql_cats = mysqli_query($con, "SELECT * FROM classificacao WHERE parcial > 0 ORDER BY ordem");
    if($sql_cats) {
        while($categoria = mysqli_fetch_assoc($sql_cats)) {
            $nmcla = $categoria['Classificacao'];
            echo '<div class="category-section">';
            echo '<a name="cat_' . md5($nmcla) . '"></a>';
            echo '<div class="category-title"><i class="bi bi-grid-3x3-gap me-2"></i>' . $nmcla . '</div>';

            // Buscar produtos da categoria
            $sql_prods = mysqli_query($con, "SELECT * FROM produtos WHERE Classificacao = '$nmcla' AND em_falta != 'Sim' ORDER BY Produto");
            if($sql_prods) {
                echo '<form method="POST" action="mesas.php">';
                echo '<input type="hidden" name="btngarcon" value="' . $btngarcon . '">';
                echo '<input type="hidden" name="nmesa" value="' . $nmesa . '">';
                echo '<input type="hidden" name="txtnropessoas" value="' . $txtnropessoas . '">';

                while($prod = mysqli_fetch_assoc($sql_prods)) {
                    echo '<button type="submit" name="btnprod" value="' . $prod['Produto'] . '" class="product-card">';
                    echo '<div class="product-info">';
                    echo '<div class="product-code">Cod: ' . $prod['KeyChave'] . '</div>';
                    echo '<div class="product-name">' . $prod['Produto'] . '</div>';
                    echo '</div>';
                    echo '<div class="product-price">R$ ' . number_format($prod['Preco'], 2, ',', '.') . '</div>';
                    echo '</button>';
                }
                echo '</form>';
            }
            echo '</div>';
        }
    }
    mysqli_close($con);
    ?>
</div>

<?php elseif($nmesa != '' && $statusmesa == 'CONTA'): ?>
<!-- ============================================ -->
<!-- MESA COM CONTA FECHADA -->
<!-- ============================================ -->
<div class="card-pdv mx-3">
    <div class="card-pdv-header">
        <i class="bi bi-cash-stack"></i>Conta Fechada - Mesa <?php echo $nmesa; ?>
    </div>

    <div class="table-responsive">
        <table class="table-pdv">
            <thead>
                <tr>
                    <th>Qtd</th>
                    <th>Produto</th>
                    <th>Valor</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $total = 0;
                include("conexaomodulomesa.php");
                $sql_itens = mysqli_query($con, "SELECT * FROM mesas WHERE MesaNro = $nmesa ORDER BY Lancamento DESC");
                if($sql_itens) {
                    while($item = mysqli_fetch_assoc($sql_itens)) {
                        $qtditem = isset($item['Quanti']) ? $item['Quanti'] : 0;
                        $valoritem = isset($item['valor']) ? $item['valor'] : 0;
                        $proditem = isset($item['Mercadoria']) ? $item['Mercadoria'] : '';
                        $subtotal = $qtditem * $valoritem;
                        $total += $subtotal;
                        echo '<tr>';
                        echo '<td>' . $qtditem . '</td>';
                        echo '<td>' . $proditem . '</td>';
                        echo '<td>R$ ' . number_format($valoritem, 2, ',', '.') . '</td>';
                        echo '<td>R$ ' . number_format($subtotal, 2, ',', '.') . '</td>';
                        echo '</tr>';
                    }
                }
                mysqli_close($con);

                $totalComConsumacao = $total;
                if($parcialconsumacao > 0) {
                    $totalComConsumacao = $total + ($parcialconsumacao * $txtnropessoas);
                }
                $nrPessoasExibir2 = ($txtnropessoas > 0) ? $txtnropessoas : 1;
                $valorPorPessoa2 = $totalComConsumacao / $nrPessoasExibir2;
                ?>
                <tr class="total-row">
                    <td colspan="2" class="total-label">TOTAL:</td>
                    <td colspan="2">R$ <?php echo number_format($totalComConsumacao, 2, ',', '.'); ?></td>
                </tr>
                <tr class="total-row" style="background: var(--primary); color: #000;">
                    <td colspan="2" class="total-label"><i class="bi bi-people-fill me-2"></i>POR PESSOA (<?php echo $nrPessoasExibir2; ?>):</td>
                    <td colspan="2"><strong>R$ <?php echo number_format($valorPorPessoa2, 2, ',', '.'); ?></strong></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="action-buttons">
        <form method="POST" action="mesas.php" class="d-inline">
            <input type="hidden" name="btngarcon" value="<?php echo $btngarcon; ?>">
            <button type="submit" name="btnliberarmesa" value="<?php echo $nmesa; ?>" class="btn-success-pdv">
                <i class="bi bi-check-circle me-2"></i>Liberar Mesa
            </button>
        </form>

        <form method="POST" action="mesas.php" class="d-inline">
            <input type="hidden" name="btngarcon" value="<?php echo $btngarcon; ?>">
            <input type="hidden" name="nmesa" value="<?php echo $nmesa; ?>">
            <button type="submit" name="btnocuparmesa" value="<?php echo $nmesa; ?>" class="btn-danger-pdv">
                <i class="bi bi-arrow-counterclockwise me-2"></i>Reabrir Mesa
            </button>
        </form>
    </div>
</div>
<?php endif; ?>

<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function changeQty(delta) {
    var input = document.getElementById('qtdInput');
    var val = parseInt(input.value) || 1;
    val += delta;
    if(val < 1) val = 1;
    if(val > 99) val = 99;
    input.value = val;
}
</script>

</body>
</html>

