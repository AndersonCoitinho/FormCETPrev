document.addEventListener("DOMContentLoaded", function () {
    // Seleciona todas as células da tabela com a classe "title-cell"
    var cells = document.querySelectorAll("td.title-cell");

    // Inicializa a largura máxima como 0
    var maxWidth = 0;

    // Itera sobre todas as células para encontrar a largura máxima
    cells.forEach(function (cell) {
        maxWidth = Math.max(maxWidth, cell.clientWidth);
    });

    // Itera sobre todas as células e define a largura delas como a largura máxima encontrada
    cells.forEach(function (cell) {
        cell.style.width = maxWidth + "px";
    });
});

function downloadTodos() {
    // Obter todos os botões de download
    var botoesDownload = document.querySelectorAll('.download-button');

    // Criar links temporários e simular o clique em cada um
    botoesDownload.forEach(function (botao) {
        var link = document.createElement('a');
        link.href = botao.getAttribute('href');
        link.setAttribute('download', ''); // Adicionar o atributo download
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    });
}

function copiarDados() {
    var campos = [
        document.getElementById('nome').textContent,
        document.getElementById('estadoCivil').textContent,
        document.getElementById('profissao').textContent,
        document.getElementById('fone').textContent,
        document.getElementById('fone_recado').textContent,
        document.getElementById('cpf').textContent,
        document.getElementById('rg').textContent,
        document.getElementById('endereco').textContent,
        document.getElementById('bairro').textContent,
        document.getElementById('cidade').textContent,
        document.getElementById('estado').textContent,
        document.getElementById('cep').textContent,
        document.getElementById('dataFormatadaNascimento').textContent,
        document.getElementById('dataFormatadaContrato').textContent,
        '',
        document.getElementById('consultor').textContent,
    ];

    var dadosExcel = campos.join('\t');
    // Cria um elemento de input
    var inputElement = document.createElement('input');
    inputElement.setAttribute('value', dadosExcel);
    // Anexa o elemento de input à página
    document.body.appendChild(inputElement);
    // Seleciona o texto no input
    inputElement.select();
    // Copia o texto para a área de transferência
    document.execCommand('copy');
    // Remove o elemento de input
    document.body.removeChild(inputElement);
}

function preencherCampos() {
    // Preenche os campos Responsavel, cpfResponsavel, profissaoResponsavel automatico
    var selectElement = document.getElementById('consultoria');
    var nomeElement = document.getElementById('responsavel');
    var cfpElement = document.getElementById('cpfResponsavel');
    var profissaoElement = document.getElementById('profissaoResponsavel');

    var opcaoSelecionada = selectElement.value;

    // Defina os valores a serem preenchidos com base na opção selecionada
    switch (opcaoSelecionada) {
        case 'CETPrev':
            nomeElement.value = 'TAINARA KARINE HAAG';
            cfpElement.value = '050.358.609-90';
            profissaoElement.value = 'SUPERVISORA ADMINISTRATIVO';
            break;
        case 'Facil':
            nomeElement.value = 'SIDNEY TURCATTO';
            cfpElement.value = '039.654.869-50';
            profissaoElement.value = 'SUPERVISORA ADMINISTRATIVO';
            break;
        case 'FacilItajai':
            nomeElement.value = 'PRISCILA BATSCHAUER DE SOUZA';
            cfpElement.value = '036.768.219-20';
            profissaoElement.value = 'AUXILIAR ADMINISTRATIVO';
            break;
        case 'CruzEMelo':
            nomeElement.value = 'ANA VALÉRIA MALINOSKI CRUZ';
            cfpElement.value = '029.277.149-59';
            profissaoElement.value = 'AUXILIAR ADMINISTRATIVO';
            break;
        case 'Agil':
            nomeElement.value = 'GUSTAVO HENRIQUE DE MELO';
            cfpElement.value = '095.018.639-21';
            profissaoElement.value = 'SUPERVISORA ADMINISTRATIVO';
            break;
        case 'Outros':
            // Se a opção for "Outros", limpar os campos
            nomeElement.value = '';
            cfpElement.value = '';
            profissaoElement.value = '';
            break;
        default:
            break;
    }
}

function mostrarAba(aba) {
    var abas = document.querySelectorAll('.aba');
    abas.forEach(function (element) {
        element.style.display = 'none';
    });

    document.getElementById(aba).style.display = 'block';
}
