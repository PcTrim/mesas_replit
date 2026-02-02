<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>Comanda Eletronica - Selecao de Garcom</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --cor-fundo: #1a1a2e;
            --cor-card: #16213e;
            --cor-destaque: #FEAC01;
            --cor-texto: #ffffff;
            --cor-texto-secundario: #a0a0a0;
            --cor-borda: #0f3460;
        }

        :root.tema-claro {
            --cor-fundo: #f5f7fa;
            --cor-card: #ffffff;
            --cor-destaque: #FEAC01;
            --cor-texto: #1a1a2e;
            --cor-texto-secundario: #6c757d;
            --cor-borda: #dee2e6;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-tap-highlight-color: transparent;
        }

        html, body {
            height: 100%;
            overflow: hidden;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--cor-fundo);
            color: var(--cor-texto);
            transition: background 0.5s ease, color 0.5s ease;
        }

        .app-container {
            height: 100vh;
            display: flex;
            flex-direction: column;
            padding: 15px;
        }

        .header-pdv {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 20px;
            background: var(--cor-card);
            border-radius: 12px;
            margin-bottom: 15px;
            border: 1px solid var(--cor-borda);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: background 0.5s ease, border-color 0.5s ease;
        }

        .header-pdv .logo-area {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .header-pdv .logo-area img {
            height: 50px;
            width: auto;
        }

        .header-pdv .titulo-sistema {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--cor-destaque);
        }

        .header-pdv .data-hora {
            font-size: 1rem;
            color: var(--cor-texto-secundario);
        }

        .tema-indicator {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 5px 12px;
            background: var(--cor-fundo);
            border-radius: 20px;
            font-size: 0.85rem;
            color: var(--cor-texto-secundario);
            border: 1px solid var(--cor-borda);
        }

        .tema-indicator i {
            color: var(--cor-destaque);
        }

        @media (max-width: 768px) {
            .header-pdv .titulo-sistema { font-size: 1rem; }
            .header-pdv .data-hora { display: none; }
            .header-pdv .logo-area img { height: 40px; }
            .tema-indicator { display: none; }
        }

        .titulo-secao {
            text-align: center;
            padding: 20px;
            background: var(--cor-card);
            border-radius: 12px;
            margin-bottom: 15px;
            border: 1px solid var(--cor-borda);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: background 0.5s ease, border-color 0.5s ease;
        }

        .titulo-secao h1 {
            font-size: 1.8rem;
            font-weight: 600;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
        }

        .titulo-secao h1 i {
            color: var(--cor-destaque);
            font-size: 2rem;
        }

        @media (max-width: 768px) {
            .titulo-secao h1 { font-size: 1.4rem; }
            .titulo-secao h1 i { font-size: 1.6rem; }
            .titulo-secao { padding: 15px; }
        }

        .grid-garcons {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 5px;
        }

        .grid-garcons::-webkit-scrollbar {
            width: 8px;
        }

        .grid-garcons::-webkit-scrollbar-track {
            background: var(--cor-card);
            border-radius: 4px;
        }

        .grid-garcons::-webkit-scrollbar-thumb {
            background: var(--cor-destaque);
            border-radius: 4px;
        }

        .btn-garcom {
            width: 100%;
            min-height: 100px;
            padding: 20px;
            margin-bottom: 12px;
            font-size: 1.4rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #1a1a2e;
            background: linear-gradient(180deg, #FFD700 0%, var(--cor-destaque) 100%);
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(254, 172, 1, 0.3);
            transition: all 0.15s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            cursor: pointer;
        }

        .btn-garcom:hover {
            transform: scale(1.02);
            box-shadow: 0 6px 25px rgba(254, 172, 1, 0.5);
        }

        .btn-garcom:active {
            transform: scale(0.98);
            box-shadow: 0 2px 10px rgba(254, 172, 1, 0.3);
        }

        .btn-garcom i {
            font-size: 1.8rem;
        }

        @media (max-width: 768px) {
            .btn-garcom {
                min-height: 85px;
                font-size: 1.2rem;
                padding: 15px;
            }
            .btn-garcom i { font-size: 1.5rem; }
        }

        @media (max-width: 576px) {
            .btn-garcom {
                min-height: 75px;
                font-size: 1.1rem;
            }
            .btn-garcom i { font-size: 1.3rem; }
        }

        .footer-pdv {
            padding: 12px 20px;
            background: var(--cor-card);
            border-radius: 12px;
            margin-top: 15px;
            border: 1px solid var(--cor-borda);
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: background 0.5s ease, border-color 0.5s ease;
        }

        .footer-pdv .nome-estabelecimento {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--cor-destaque);
            margin-bottom: 3px;
        }

        .footer-pdv .endereco {
            font-size: 0.9rem;
            color: var(--cor-texto-secundario);
        }

        .footer-pdv .dev-info {
            font-size: 0.75rem;
            color: var(--cor-texto-secundario);
            margin-top: 8px;
            opacity: 0.7;
        }

        @media (max-width: 576px) {
            .footer-pdv .nome-estabelecimento { font-size: 1rem; }
            .footer-pdv .endereco { font-size: 0.8rem; }
            .footer-pdv { padding: 10px 15px; }
        }

        .loading-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(26, 26, 46, 0.95);
            z-index: 9999;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }

        .tema-claro .loading-overlay {
            background: rgba(245, 247, 250, 0.95);
        }

        .loading-overlay.active {
            display: flex;
        }

        .loading-overlay .spinner-border {
            width: 4rem;
            height: 4rem;
            color: var(--cor-destaque);
            border-width: 5px;
        }

        .loading-overlay .loading-text {
            margin-top: 20px;
            font-size: 1.3rem;
            color: var(--cor-texto);
            font-weight: 600;
        }
    </style>
    <script>
        function selecionarGarcon(nome) {
            document.getElementById('btngarcon_hidden').value = nome;
            document.getElementById('loadingOverlay').classList.add('active');
            document.online.submit();
        }

        function atualizarRelogio() {
            var agora = new Date();
            var opcoes = { weekday: 'short', day: '2-digit', month: 'short', hour: '2-digit', minute: '2-digit' };
            var elem = document.getElementById('dataHora');
            if(elem) elem.textContent = agora.toLocaleDateString('pt-BR', opcoes);
        }

        function aplicarTemaAutomatico() {
            var hora = new Date().getHours();
            var isDia = (hora >= 6 && hora < 18);
            var indicador = document.getElementById('temaIndicator');

            if(isDia) {
                document.documentElement.classList.add('tema-claro');
                if(indicador) indicador.innerHTML = '<i class="bi bi-sun-fill"></i> Modo Dia';
            } else {
                document.documentElement.classList.remove('tema-claro');
                if(indicador) indicador.innerHTML = '<i class="bi bi-moon-fill"></i> Modo Noite';
            }
        }

        setInterval(atualizarRelogio, 1000);
        setInterval(aplicarTemaAutomatico, 60000);

        window.onload = function() {
            atualizarRelogio();
            aplicarTemaAutomatico();
        };
    </script>
</head>
<body>

    <div class="loading-overlay" id="loadingOverlay">
        <div class="spinner-border" role="status"></div>
        <div class="loading-text">Carregando mesas...</div>
    </div>

    <div class="app-container">

        <header class="header-pdv">
            <div class="logo-area">
                <a href="http://www.pctrim.com.br" target="_blank">
                    <img src="logopctrim.png" alt="Logo PcTrim" aria-label="Logo PcTrim">
                </a>
                <span class="titulo-sistema">COMANDA ELETRONICA</span>
            </div>
            <div class="tema-indicator" id="temaIndicator">
                <i class="bi bi-circle-half"></i> Carregando...
            </div>
            <span class="data-hora" id="dataHora"></span>
        </header>

        <section class="titulo-secao">
            <h1>
                <i class="bi bi-person-badge-fill"></i>
                Selecione o Garcom ou Equipamento
            </h1>
        </section>

        <?php
        include("conexaomodulomesa.php");
        if(!$con){
            echo '<div class="alert alert-danger" role="alert">Erro ao conectar ao banco de dados. Tente novamente mais tarde.</div>';
            exit;
        }
        ?>

        <div class="grid-garcons">
            <form name="online" method="POST" action="mesas.php">
                <input type="hidden" name="btngarcon" id="btngarcon_hidden" value="">
                <div class="row g-3">
                    <?php
                    $sql = mysqli_query($con, "SELECT * FROM garcon ORDER BY NomeGarcon");
                    if($sql) {
                        while($garcon = mysqli_fetch_assoc($sql)) {
                                                        $nmgar = isset($garcon['NomeGarcon']) ? htmlspecialchars($garcon['NomeGarcon']) : '';
                                                        echo '<div class="col-12 col-md-6 col-lg-4">';
                                                        echo '<button type="button" class="btn-garcom" onclick="selecionarGarcon(\''.$nmgar.'\')" aria-label="Selecionar garçom '. $nmgar .'">';
                                                        echo '<i class="bi bi-person-circle" aria-hidden="true"></i>';
                                                        echo $nmgar;
                                                        echo '</button>';
                                                        echo '</div>';
                        }
                    }
                    mysqli_close($con);
                    ?>
                </div>
            </form>
        </div>

        <?php
        include("conexaomodulomesa.php");
        $nmcli = '';
        $endcli = '';
        $sql1 = mysqli_query($con, "SELECT * FROM sistema LIMIT 1");
        if($sql1) {
            $sistema = mysqli_fetch_assoc($sql1);
            if($sistema) {
                $nmcli = isset($sistema['CabecalhoComanda']) ? htmlspecialchars($sistema['CabecalhoComanda']) : '';
                $endcli = isset($sistema['EnderecoComanda']) ? htmlspecialchars($sistema['EnderecoComanda']) : '';
            }
        }
        mysqli_close($con);
        ?>

        <footer class="footer-pdv" aria-label="Rodapé">
            <div class="nome-estabelecimento"><?php echo $nmcli; ?></div>
            <div class="endereco"><?php echo $endcli; ?></div>
            <div class="dev-info">
                <i class="bi bi-code-slash" aria-hidden="true"></i> PcTrim Tecnologia
            </div>
        </footer>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

