<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Produtos - API</title>
    <style>
        section { margin-bottom: 2em; }
        input, button { margin: 0.2em; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ccc; padding: 0.5em; }
    </style>
    <link rel="stylesheet" href="produtos.css">
</head>
<body>
    
<div id="modalProducts-main" class="modal-products">
        <div id="modalProducts" class="modal-products-content">
            <div class="modal-close-btn">
                <button class="btn-products-close" id="btn-products-close">&times;</button>
            </div>    
            
            <h1 id="productName" class="text-product"></h1>
            <p id="productInfo"></p>
            
        </div>
    </div>

    <div class="header">
        <h1>API de Produtos</h1>
        <nav>
            <a href="index.html">Início</a>
            <a href="produtos.php">Produtos</a>
            <a href="sobre.html">Sobre</a>
        </nav>
    </div>

    <!-- Seção de pesquisa -->
    <section class="pesquisa">
        <h2>Pesquisar Produto por ID</h2>
        <input type="number" id="pesquisaId" placeholder="Código do Produto">
        <button onclick="buscarProduto()">Buscar</button>
        
        
        <div class="busca-resultados">
            <div>
                <p>Nome do Produto:</p>
                <input type="text" id="nomeProduto" placeholder="Nome do produto">
            </div>
            
            <div>
                <p>Descrição do Produto:</p>
                <input type="text" id="descricaoProduto" placeholder="Descrição do produto">
            </div>

            <div>
                <p>Valor do Produto:</p>
                <input type="number" id="valorProduto" placeholder="Valor do produto">
            </div>

            <div>
                <p>Unidade do Produto:</p>
                <input type="text" id="unidadeProduto" placeholder="Unidade do produto">
            </div>
            
            <div>
                <p>Tipo de Aplicação:</p>
                <input type="text" id="tipoAplicacaoProduto" placeholder="Tipo de aplicação">
            </div>

        </div>
        

    </section>

    <!-- Seção de listagem -->
    <section>
        <div class="listagem">
            <h2 style="color: white">Produtos em Catálogo</h2>
            <button onclick="listarProdutos()">Listar Todos</button>
        </div>
        
        <table id="tabelaProdutos">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Descrição</th>
                    <th>Valor</th>
                    <th>Unidade</th>
                    <th>Tipo Aplicação</th>
                    <th class="th-info">Informações</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </section>

    <!-- Seção de cadastro -->
    <section class="cadastro-produto">
        <h2>Incluir Novo Produto</h2>
        <form id="formProduto" onsubmit="cadastrarProduto(event)">
            <div>
                <p>Nome do Produto:</p>
                <input type="text" name="nome" placeholder="Nome" required>
            </div>
            <div>
                <p>Descrição do Produto:</p>
                <input type="text" name="descricao" placeholder="Descrição" required>
            </div>

            <div>
                <p>Valor do Produto:</p>
                <input type="number" step="0.01" name="valor" placeholder="Valor" required>
            </div>

            <div>
                <p>Unidade do Produto:</p>
                <input type="text" name="unidade" placeholder="Unidade" required>
            </div>

            <div>
                <p>Tipo de Aplicação:</p>
                <input type="text" name="tipo_aplicacao" placeholder="Tipo Aplicação" required>
            </div>
            
            <button type="submit">Cadastrar</button>
        </form>
        <pre id="resultadoCadastro"></pre>
    </section>

    

<script>
const apiUrl = 'http://localhost:8000/items.php';

function buscarProduto() {
    const id = document.getElementById('pesquisaId').value;
    fetch(`${apiUrl}?id=${id}`)
        .then(r => r.json())
        .then(data => {
            document.getElementById('nomeProduto').value = data.dados.nome || '';
            document.getElementById('descricaoProduto').value = data.dados.descricao || '';
            document.getElementById('valorProduto').value = data.dados.valor  || '';
            document.getElementById('unidadeProduto').value = data.dados.unidade || '';
            document.getElementById('tipoAplicacaoProduto').value = data.dados.tipo_aplicacao || '';
        });
}

function listarProdutos() {
    fetch(apiUrl)
        .then(r => r.json())
        .then(data => {
            const tbody = document.getElementById('tabelaProdutos').querySelector('tbody');
            tbody.innerHTML = '';
            (data.dados || []).forEach(produto => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${produto.id}</td>
                    <td>${produto.nome}</td>
                    <td>${produto.descricao}</td>
                    <td>R$${produto.valor}</td>
                    <td>${produto.unidade}</td>
                    <td>${produto.tipo_aplicacao}</td>
                    <td><button class="btn-product-details" onclick="modalProducts()">Detalhes</button></td>
                `;
                tbody.appendChild(tr);
            });
        });
}

function cadastrarProduto(event) {
    event.preventDefault();
    const form = event.target;
    const dados = {
        nome: form.nome.value,
        descricao: form.descricao.value,
        valor: form.valor.value,
        unidade: form.unidade.value,
        tipo_aplicacao: form.tipo_aplicacao.value
    };
    fetch(apiUrl, {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(dados)
    })
    .then(r => r.json())
    .then(data => {
        document.getElementById('resultadoCadastro').textContent = JSON.stringify(data, null, 2);
        form.reset();
        listarProdutos();
    });
}

function modalProducts() {
    id = event.target.closest('tr').querySelector('td').textContent;
    fetch(`${apiUrl}?id=${id}`, {
        method: 'GET',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify()
    })
    .then(r => r.json())
    .then(data => {
        document.getElementById('productName').textContent = data.dados.nome || 'Produto';
        document.getElementById('productInfo').innerHTML = `
            <p><strong>ID:</strong> ${data.dados.id || 'N/A'}</p>
            <p><strong>Descrição:</strong> ${data.dados.descricao || 'N/A'}</p>
            <p><strong>Valor:</strong> R$${data.dados.valor || '0.00'}</p>
            <p><strong>Unidade:</strong> ${data.dados.unidade || 'N/A'}</p>
            <p><strong>Tipo de Aplicação:</strong> ${data.dados.tipo_aplicacao || 'N/A'}</p>
        `;
    })

    const modal = document.getElementById('modalProducts-main');
    modal.style.display = 'block';

    
   const close = document.getElementById('btn-products-close');
    
    close.addEventListener('click', function() {
        modal.style.display = 'none';
    });



}


// Carrega a lista ao abrir a página
listarProdutos();
</script>
</body>
</html>