<?php
require_once 'vendor/autoload.php'; // Carregue a biblioteca PHPWord

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\TemplateProcessor;
use Aws\S3\S3Client;

/* CONFIG AWS */
/*
$config = [
    'region' => $_ENV['AWS_REGION'], 
    'version' => 'latest',   
    'credentials' => [
        'key' => $_ENV['AWS_ACCESS_KEY_ID'],
        'secret' => $_ENV['AWS_SECRET_ACCESS_KEY'],
    ],
];
$s3 = new S3Client($config);
*/

// VIZUALIZAR OS POST
/*
echo '<pre>';
print_r($_POST);
echo '</pre>';
*/

/* ARMAZENA OS DADOS */
$nome = mb_strtoupper($_POST["nome"], 'UTF-8');
$nacionalidade = mb_strtoupper($_POST["nacionalidade"], 'UTF-8');
$estadoCivil = mb_strtoupper($_POST["estadoCivil"], 'UTF-8');
$profissao = mb_strtoupper($_POST["profissao"], 'UTF-8');
$fone = mb_strtoupper($_POST["fone"], 'UTF-8');
$fone_recado = mb_strtoupper($_POST["fone_recado"], 'UTF-8');
$cpf = mb_strtoupper($_POST["cpf"], 'UTF-8');
$rg = mb_strtoupper($_POST["rg"], 'UTF-8');
$endereco = mb_strtoupper($_POST["endereco"], 'UTF-8');
$bairro = mb_strtoupper($_POST["bairro"], 'UTF-8');
$cep = mb_strtoupper($_POST["cep"], 'UTF-8');
$cidade = mb_strtoupper($_POST["cidade"], 'UTF-8');
$estado = mb_strtoupper($_POST["estado"], 'UTF-8');
$data_nascimento = mb_strtoupper($_POST["data_nascimento"], 'UTF-8');
$data = mb_strtoupper($_POST["data"], 'UTF-8');
$dataFormatadaNascimento = date("d/m/Y", strtotime($data_nascimento));
$dataFormatadaContrato = date("d/m/Y", strtotime($data));

$consultoria = mb_strtoupper($_POST["consultoria"], 'UTF-8');
$responsavel = isset($_POST['responsavel']) ? mb_strtoupper($_POST['responsavel'], 'UTF-8') : '';
$cpfResponsavel = isset($_POST['cpfResponsavel']) ? mb_strtoupper($_POST['cpfResponsavel'], 'UTF-8') : '';
$profissaoResponsavel = isset($_POST['profissaoResponsavel']) ? mb_strtoupper($_POST['profissaoResponsavel'], 'UTF-8') : '';
$consultor = isset($_POST['consultor']) ? mb_strtoupper($_POST['consultor'], 'UTF-8') : '';

$profissaoEpoca = mb_strtoupper($_POST["profissaoEpoca"], 'UTF-8');
$funcaoDesempenhava = mb_strtoupper($_POST["funcaoDesempenhava"], 'UTF-8');
$dataAcidente = mb_strtoupper($_POST["dataAcidente"], 'UTF-8'); 
$afastouInss = isset($_POST['afastouInss']) ? mb_strtoupper($_POST['afastouInss'], 'UTF-8') : '';
$tempoAfastado = mb_strtoupper($_POST["tempoAfastado"], 'UTF-8');
$senhaInss = mb_strtoupper($_POST["senhaInss"], 'UTF-8');
$hospital = mb_strtoupper($_POST["hospital"], 'UTF-8');
$detalhesAcidente = mb_strtoupper($_POST["detalhesAcidente"], 'UTF-8');
$membrosAfetados = mb_strtoupper($_POST["membrosAfetados"], 'UTF-8');
$tipoAcidente = isset($_POST['tipoAcidente']) ? mb_strtoupper($_POST['tipoAcidente'], 'UTF-8') : '';

$rgoucnh = isset($_POST['rgoucnh']) ? $_POST['rgoucnh'] : '';
$cpfEntregue = isset($_POST['cpfEntregue']) ? $_POST['cpfEntregue'] : '';
$residencia = isset($_POST['residencia']) ? $_POST['residencia'] : '';
$cnis = isset($_POST['cnis']) ? $_POST['cnis'] : '';
$ctps = isset($_POST['ctps']) ? $_POST['ctps'] : '';
$extrato = isset($_POST['extrato']) ? $_POST['extrato'] : '';
$laudoMedicoInss = isset($_POST['laudoMedicoInss']) ? $_POST['laudoMedicoInss'] : '';
$copiaprocesso = isset($_POST['copiaprocesso']) ? $_POST['copiaprocesso'] : '';
$raiox = isset($_POST['raiox']) ? $_POST['raiox'] : '';
$ressonancia = isset($_POST['ressonancia']) ? $_POST['ressonancia'] : '';
$exames = isset($_POST['exames']) ? $_POST['exames'] : '';
$prontuario = isset($_POST['prontuario']) ? $_POST['prontuario'] : '';
$laudoMedico = isset($_POST['laudoMedico']) ? $_POST['laudoMedico'] : '';
$cat = isset($_POST['cat']) ? $_POST['cat'] : '';
$bo = isset($_POST['bo']) ? $_POST['bo'] : '';



$timestamp = strtotime($data); // Converte a data para um timestamp
if ($timestamp !== false) {
    $dataPorExtenso = new IntlDateFormatter(
        'pt_BR', // Localização (português do Brasil)
        IntlDateFormatter::LONG, // Estilo de formatação (por extenso)
        IntlDateFormatter::NONE // Tipo de formatação de hora (não aplicável)
    );
} else {
    echo "Data inválida.";
}
$dataPorExtensoString = $dataPorExtenso->format($timestamp);


// Limpe o diretório de saída antes de gerar novos documentos
$directory = './novo/';
$files = glob($directory . '*'); // Obtém todos os arquivos no diretório

foreach ($files as $file) {
    if (is_file($file)) {
        unlink($file); // Exclui cada arquivo no diretório
    }
}
    /* RECEBE OS DOCUMENTOS MODELO, O NOME E O TITULO */
$documentos = [
    [
        'modelo' => './modelos/capaProcesso.docx',
        'saida' => './novo/CAPA DO PROCESSO - ' .$nome . '.docx',
        'bucket' => 'CAPA DO PROCESSO - ' .$nome . '.docx',
        'titulo' => 'CAPA DO PROCESSO - ' .$nome
    ],
    [
        'modelo' => './modelos/contratoHonorarios.docx',
        'saida' => './novo/CONTRATO HONORÁRIO - ' .$nome . '.docx',
        'bucket' => 'CONTRATO HONORÁRIO - ' .$nome . '.docx',
        'titulo' => 'CONTRATO HONORÁRIO - ' .$nome
    ],
    [
        'modelo' => './modelos/declaracaoDeResidencia.docx',
        'saida' => './novo/DECLARAÇÃO DE RESIDENCIA - ' .$nome . '.docx',
        'bucket' => 'DECLARAÇÃO DE RESIDENCIA - ' .$nome . '.docx',
        'titulo' => 'DECLARAÇÃO DE RESIDENCIA - ' .$nome
    ],
    [
        'modelo' => './modelos/justicagratuita.docx',
        'saida' => './novo/JUSTIÇA GRATUITA - ' .$nome . '.docx',
        'bucket' => 'JUSTIÇA GRATUITA - ' .$nome . '.docx',
        'titulo' => 'JUSTIÇA GRATUITA - ' .$nome
    ],
    [
        'modelo' => './modelos/minutaAuxilioAcidenteFederal.docx',
        'saida' => './novo/MINUTA AUXILIO ACIDENTE FEDERAL - ' .$nome . '.docx',
        'bucket' => 'MINUTA AUXILIO ACIDENTE FEDERAL - ' .$nome . '.docx',
        'titulo' => 'MINUTA AUXILIO ACIDENTE FEDERAL - ' .$nome
    ],
    [
        'modelo' => './modelos/procuracao.docx',
        'saida' => './novo/PROCURAÇÃO - ' .$nome . '.docx',
        'bucket' => 'PROCURAÇÃO - ' .$nome . '.docx',
        'titulo' => 'PROCURAÇÃO - ' .$nome
    ],
    [
        'modelo' => './modelos/requerimentoAdmAuxilioAcidente.docx',
        'saida' => './novo/REQUERIMENTO ADMINISTRATIVO AUXILIO ACIDENTE - ' .$nome . '.docx',
        'bucket' => 'REQUERIMENTO ADMINISTRATIVO AUXILIO ACIDENTE - ' .$nome . '.docx',
        'titulo' => 'REQUERIMENTO ADMINISTRATIVO AUXILIO ACIDENTE - ' .$nome
    ],
    [
        'modelo' => './modelos/termoDeRenuncia.docx',
        'saida' => './novo/TERMO DE RENÚNCIA - ' .$nome . '.docx',
        'bucket' => 'TERMO DE RENÚNCIA - ' .$nome . '.docx',
        'titulo' => 'TERMO DE RENÚNCIA - ' .$nome
    ],
];

foreach ($documentos as $documento) {
    // Carregue o modelo DOCX
    $templateProcessor = new TemplateProcessor($documento['modelo']);

    // Substitua a variável {{nome}} pelo valor do usuário
    $templateProcessor->setValue('{{nome}}', $nome);
    $templateProcessor->setValue('{{nacionalidade}}', $nacionalidade);
    $templateProcessor->setValue('{{estadoCivil}}', $estadoCivil);
    $templateProcessor->setValue('{{profissao}}', $profissao);
    if (!empty($fone_recado)) {
        $templateProcessor->setValue('{{fone}}', $fone . ' OU ' . $fone_recado);
    } else {
        $templateProcessor->setValue('{{fone}}', $fone);
    }
    $templateProcessor->setValue('{{cpf}}', $cpf);
    $templateProcessor->setValue('{{rg}}', $rg);
    $templateProcessor->setValue('{{data_nascimento}}', $dataFormatadaNascimento);
    $templateProcessor->setValue('{{endereco}}', $endereco);
    $templateProcessor->setValue('{{bairro}}', $bairro);
    $templateProcessor->setValue('{{cep}}', $cep);
    $templateProcessor->setValue('{{cidade}}', $cidade);
    $templateProcessor->setValue('{{estado}}', $estado);
    $templateProcessor->setValue('{{data}}', $dataPorExtensoString);

    $templateProcessor->setValue('{{consultor}}', $consultor);
    $templateProcessor->setValue('{{consultoria}}', $consultoria);
    $templateProcessor->setValue('{{profissaoEpoca}}', $profissaoEpoca);
    $templateProcessor->setValue('{{funcaoDesempenhava}}', $funcaoDesempenhava);
    $templateProcessor->setValue('{{dataAcidente}}', $dataAcidente);
    $templateProcessor->setValue('{{afastouInss}}', $afastouInss);
    $templateProcessor->setValue('{{tempoAfastado}}', $tempoAfastado);
    $templateProcessor->setValue('{{senhaInss}}', $senhaInss);
    $templateProcessor->setValue('{{hospital}}', $hospital);
    $templateProcessor->setValue('{{detalhesAcidente}}', $detalhesAcidente);
    $templateProcessor->setValue('{{membrosAfetados}}', $membrosAfetados);
    $templateProcessor->setValue('{{tipoAcidente}}', $tipoAcidente);
    $templateProcessor->setValue('{{responsavel}}', $responsavel);
    $templateProcessor->setValue('{{cpfResponsavel}}', $cpfResponsavel);
    $templateProcessor->setValue('{{profissaoResponsavel}}', $profissaoResponsavel);

    $templateProcessor->setValue('{{rgoucnh}}', $rgoucnh);
    $templateProcessor->setValue('{{cpfEntregue}}', $cpfEntregue);
    $templateProcessor->setValue('{{residencia}}', $residencia);
    $templateProcessor->setValue('{{cnis}}', $cnis);
    $templateProcessor->setValue('{{ctps}}', $ctps);
    $templateProcessor->setValue('{{extrato}}', $extrato);
    $templateProcessor->setValue('{{laudoMedicoInss}}', $laudoMedicoInss);
    $templateProcessor->setValue('{{copiaprocesso}}', $copiaprocesso);
    $templateProcessor->setValue('{{raiox}}', $raiox);
    $templateProcessor->setValue('{{ressonancia}}', $ressonancia);
    $templateProcessor->setValue('{{exames}}', $exames);
    $templateProcessor->setValue('{{prontuario}}', $prontuario);
    $templateProcessor->setValue('{{laudoMedico}}', $laudoMedico);
    $templateProcessor->setValue('{{cat}}', $cat);
    $templateProcessor->setValue('{{bo}}', $bo);
    
    // Salve o documento final com um nome único
    $templateProcessor->saveAs($documento['saida']);
    
    $nomeArquivo = basename($documento['saida']);
    $conteudoArquivo = file_get_contents($documento['saida']);

    /* LOCAL DE UPLOAD NO AWS */
    /*
    $s3->putObject([
        'Bucket' => 'cetprev-documentos',
        'Key' =>  $nomeArquivo, //Caminho desejado no S3
        'Body' => $conteudoArquivo,
    ]);
    */
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Download de Documentos</title>
</head>
<body>
    <h1>Documentos Gerados:</h1>
    <?php foreach ($documentos as $documento): ?>
        <p>Arquivo: <?php echo $documento['titulo']; ?><a href="<?php echo $documento['saida']; ?>" download><br>Download</a></p>
    <?php endforeach; ?>
    <h2>Para a Planilha:</h2>
<table border="1">
    <tr>
        <td>Nome</td>
        <td>Estado Civil</td>
        <td>Profissão</td>
        <td>Telefone</td>
        <td>Tel Recado</td>
        <td>CPF</td>
        <td>RG</td>
        <td>Endereço</td>
        <td>Bairro</td>
        <td>Cidade</td>
        <td>UF</td>
        <td>CEP</td>
        <td>Data Nascimento</td>
        <td>Data Contrato</td>
        <td>Consultor</td>
    </tr>
    <tr>
            <td id="nome" data-copy="<?php echo $nome; ?>"><?php echo $nome; ?></td>
            <td id="estadoCivil" data-copy="<?php echo $estadoCivil; ?>"><?php echo $estadoCivil; ?></td>
            <td id="profissao" data-copy="<?php echo $profissao; ?>"><?php echo $profissao; ?></td>
            <td id="fone" data-copy="<?php echo $fone; ?>"><?php echo $fone; ?></td>
            <td id="fone_recado" data-copy="<?php echo $fone_recado; ?>"><?php echo $fone_recado; ?></td>
            <td id="cpf" data-copy="<?php echo $cpf; ?>"><?php echo $cpf; ?></td>
            <td id="rg" data-copy="<?php echo $rg; ?>"><?php echo $rg; ?></td>
            <td id="endereco" data-copy="<?php echo $endereco; ?>"><?php echo $endereco; ?></td>
            <td id="bairro" data-copy="<?php echo $bairro; ?>"><?php echo $bairro; ?></td>
            <td id="cidade" data-copy="<?php echo $cidade; ?>"><?php echo $cidade; ?></td>
            <td id="estado" data-copy="<?php echo $estado; ?>"><?php echo $estado; ?></td>
            <td id="cep" data-copy="<?php echo $cep; ?>"><?php echo $cep; ?></td>
            <td id="dataFormatadaNascimento" data-copy="<?php echo $dataFormatadaNascimento; ?>"><?php echo $dataFormatadaNascimento; ?></td>
            <td id="dataFormatadaContrato" data-copy="<?php echo $dataFormatadaContrato; ?>"><?php echo $dataFormatadaContrato; ?></td>
            <td id="consultor" data-copy="<?php echo $consultor; ?>"><?php echo $consultor; ?></td>
    </tr>
</table>

<button id="copiarDados" onclick="copiarDados()">Copiar Dados</button>
<script>
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
        alert('Dados copiados para a área de transferência');
    }
</script>

</body>
</html>

