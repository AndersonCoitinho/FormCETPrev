<?php

session_start();

// URL e AcessToken
$CLICKSIGN_API_URL = "https://sandbox.clicksign.com/api/v1/"; #"https://app.clicksign.com/api/v1/";
$ACCESS_TOKEN = "34623016-0f69-42c3-9ac9-fa917de8c47f"; #"f002168c-d7f5-4cb0-9b1a-d88f8bacfcca";
include 'cod64.php';

// Recupera os valores das variáveis de sessão 
$nome = isset($_SESSION['nome']) ? $_SESSION['nome'] : '';
$fone = isset($_SESSION['fone']) ? $_SESSION['fone'] : '';
$cpf = isset($_SESSION['cpf']) ? $_SESSION['cpf'] : '';
$data_nascimento = isset($_SESSION['data_nascimento']) ? $_SESSION['data_nascimento'] : '';

// Recupera os parâmetros da URL
$documento1 = isset($_GET['documento1']) ? urldecode($_GET['documento1']) : '';
$documento2 = isset($_GET['documento2']) ? urldecode($_GET['documento2']) : '';
$documento3 = isset($_GET['documento3']) ? urldecode($_GET['documento3']) : '';
$documento6 = isset($_GET['documento6']) ? urldecode($_GET['documento6']) : '';

// Função para criar um novo signer
function createSigner($nome, $fone, $cpf, $data_nascimento) {
    global $CLICKSIGN_API_URL, $ACCESS_TOKEN;

    // Dados do novo signer a ser criado
    $signerData = array(
        'signer' => array(
            'email' => '',
            'phone_number' => $fone,
            'auths' => ['whatsapp'],
            'name' => $nome,
            'documentation' => $cpf,
            'birthday' => $data_nascimento,
            'communicate_by' => 'whatsapp',
            'has_documentation' => true,
            'selfie_enabled' => true,
            'handwritten_enabled' => false,
            'location_required_enabled' => true,
            'official_document_enabled' => false,
            'liveness_enabled' => false,
            'facial_biometrics_enabled' => false
        )
    );

    // URL signers q vai apos a base URl
    $endpoint = "signers";
    
    // Monta a URL completa com o token de acesso
    $url = "{$CLICKSIGN_API_URL}$endpoint?access_token={$ACCESS_TOKEN}";

    // Exibe a URL
    //echo "URL da key signer: " . $url . "\n<br>";

    // Cabeçalhos da requisição
    $headers = [
        'Accept: application/json',
        'Content-Type: application/json',
    ];

    try {
        // Inicia uma sessão cURL
        $ch = curl_init($url);
        
        // Configura opções cURL
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Retorna a resposta como string
        curl_setopt($ch, CURLOPT_POST, true); // Configura a requisição como POST
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($signerData)); // Envia os dados do signer no formato JSON
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); // Adiciona os cabeçalhos à requisição

        // Executa a requisição cURL e armazena a resposta
        $response = curl_exec($ch);

        // Verifica se houve algum erro durante a execução da requisição cURL
        if (curl_errno($ch)) {
            echo "Erro cURL: " . curl_error($ch) . "\n";
        }
        
        // Obtém o código de status HTTP da resposta
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // Fecha a sessão cURL
        curl_close($ch);

        // Verifica se a requisição foi bem-sucedida (código 201)
        if ($httpCode == 201) {
            // Processa os dados da resposta (decodifica o JSON)
            //echo "Signer criado com sucesso!\n";
            //echo "<br>";
            $responseData = json_decode($response, true);
            //print_r(json_decode($response, true));
            //print_r($responseData);
            // Retorna a chave do signer
            return $responseData['signer']['key'];
        } else {
            // Imprime uma mensagem de erro se a requisição falhou
            echo "<br>Erro na requisição: {$httpCode}\n";
            echo $response;
            return null;
        }
    } catch (Exception $e) {
        // Imprime uma mensagem de erro se ocorrer uma exceção durante a requisição
        echo "<br>Erro na requisição: {$e->getMessage()}\n";
        return null;
    }
    
}

// Função para criar um documento
function createDocument($docPath) {
    global $CLICKSIGN_API_URL, $ACCESS_TOKEN;

    // Divisão do Nome do Documento
    $docName = pathinfo($docPath, PATHINFO_FILENAME);

    // Calcular prazo com base na data atual + 5 dias
    $deadlineAt = date('c', strtotime('+5 day'));

    // Obter o conteúdo em Base64 usando a função do arquivo cod64.php
    $base64_content = docx_to_base64($docPath);

    // Verificar se a conversão foi bem-sucedida
    if ($base64_content === null) {
        echo "<br><br>Falha ao processar o arquivo.\n";
        return null;
    }

    // Dados do novo documento a ser criado
    $documentData = [
        'document' => [
            "path" => "/{$docName}.docx",
            'content_base64' => "data:application/vnd.openxmlformats-officedocument.wordprocessingml.document;base64," . $base64_content,
            'deadline_at' => $deadlineAt,
            'auto_close' => true,
            'locale' => 'pt-BR',
            'sequence_enabled' => true,
        ],
    ];

    // URL para criar o documento
    $endpoint = "documents";

    // Monta a URL completa com o token de acesso
    $url = "{$CLICKSIGN_API_URL}$endpoint?access_token={$ACCESS_TOKEN}";

    // Exibe a URL
    //echo "URL da Key documento: " . $url . "\n<br>";

    // Cabeçalhos da requisição
    $headers = [
        'Accept: application/json',
        'Content-Type: application/json',
    ];

    try {
        // Inicia uma sessão cURL
        $ch = curl_init($url);

        // Configura opções cURL
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Retorna a resposta como string
        curl_setopt($ch, CURLOPT_POST, true); // Configura a requisição como POST
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($documentData)); // Envia os dados do documento no formato JSON
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); // Adiciona os cabeçalhos à requisição

        // Executa a requisição cURL e armazena a resposta
        $response = curl_exec($ch);

        // Verifica se houve algum erro durante a execução da requisição cURL
        if (curl_errno($ch)) {
            echo "Erro cURL: " . curl_error($ch) . "\n";
        }

        // Obtém o código de status HTTP da resposta
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // Fecha a sessão cURL
        curl_close($ch);

        // Verifica se a requisição foi bem-sucedida (código 201)
        if ($httpCode == 201) {
            // Processa os dados da resposta (decodifica o JSON)
            //echo "Documento criado com sucesso!\n";
            //echo "<br>";
            $responseData = json_decode($response, true);
            //print_r($responseData);
            // Retorna a chave do documento
            return $responseData['document']['key'];
        } else {
            // Imprime uma mensagem de erro se a requisição falhou
            echo "<br>Erro na requisição: {$httpCode}\n";
            echo $response;
            return null;
        }
    } catch (Exception $e) {
        // Imprime uma mensagem de erro se ocorrer uma exceção durante a requisição
        echo "<br>Erro na requisição: {$e->getMessage()}\n";
        return null;
    }
}

// Função que adiciona um signer a um documento
function addSignerToDocument($documentKey, $signerKey) {
    global $CLICKSIGN_API_URL, $ACCESS_TOKEN;

    // Dados para adicionar o signatário ao documento
    $requestData = [
        'list' => [
            'document_key' => $documentKey,
            'signer_key' => $signerKey,
            'sign_as' => "sign",
            'group' => 1,
            'message' => "",
            'refusable' => false,
        ],
    ];

    // URL para adicionar o signatário ao documento
    $endpoint = "lists";

    // Monta a URL completa com o token de acesso
    $url = "{$CLICKSIGN_API_URL}$endpoint?access_token={$ACCESS_TOKEN}";

    // Exibe a URL
    //echo "URL da junção signer e document: " . $url . "\n<br>";

    // Cabeçalhos da requisição
    $headers = [
        'Accept: application/json',
        'Content-Type: application/json',
    ];

    try {
        // Inicia uma sessão cURL
        $ch = curl_init($url);

        // Configura opções cURL
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Retorna a resposta como string
        curl_setopt($ch, CURLOPT_POST, true); // Configura a requisição como POST
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData)); // Envia os dados no formato JSON
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); // Adiciona os cabeçalhos à requisição

        // Executa a requisição cURL e armazena a resposta
        $response = curl_exec($ch);

        // Verifica se houve algum erro durante a execução da requisição cURL
        if (curl_errno($ch)) {
            echo "Erro cURL: " . curl_error($ch) . "\n";
        }

        // Obtém o código de status HTTP da resposta
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // Fecha a sessão cURL
        curl_close($ch);

        // Verifica se a requisição foi bem-sucedida (código 201)
        if ($httpCode == 201) {
            // Processa os dados da resposta (decodifica o JSON)
            //echo "Signatário adicionado ao documento com sucesso!\n<br>";
            $responseData = json_decode($response, true);
            //print_r($responseData);

            // Retorna a chave do documento (pode ser útil, dependendo da sua aplicação)
            return $responseData['list']['key'];
        } else {
            // Imprime uma mensagem de erro se a requisição falhou
            echo "<br>Erro na requisição: {$httpCode}\n";
            echo $response;
            return null;
        }
    } catch (Exception $e) {
        // Imprime uma mensagem de erro se ocorrer uma exceção durante a requisição
        echo "<br>Erro na requisição: {$e->getMessage()}\n";
        return null;
    }
}

// Função que cria Batch (lote)
function createBatch($signerKey, $documentKey) {
    global $CLICKSIGN_API_URL, $ACCESS_TOKEN;

    // Dados do novo lote a ser criado
    $batchData = [
        'batch' => [
            'signer_key' => $signerKey,
            'document_keys' => $documentKey,
            'summary' => true,
        ],
    ];


    // URL para criar o lote
    $endpoint = "batches";

    // Monta a URL completa com o token de acesso
    $url = "{$CLICKSIGN_API_URL}$endpoint?access_token={$ACCESS_TOKEN}";

    // Exibe a URL
    //echo "URL da criação do lote: " . $url . "\n<br>";

    // Cabeçalhos da requisição
    $headers = [
        'Accept: application/json',
        'Content-Type: application/json',
    ];

    try {
        // Inicia uma sessão cURL
        $ch = curl_init($url);

        // Configura opções cURL
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Retorna a resposta como string
        curl_setopt($ch, CURLOPT_POST, true); // Configura a requisição como POST
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($batchData)); // Envia os dados do lote no formato JSON
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); // Adiciona os cabeçalhos à requisição

        // Executa a requisição cURL e armazena a resposta
        $response = curl_exec($ch);

        // Verifica se houve algum erro durante a execução da requisição cURL
        if (curl_errno($ch)) {
            echo "Erro cURL: " . curl_error($ch) . "\n";
        }

        // Obtém o código de status HTTP da resposta
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // Fecha a sessão cURL
        curl_close($ch);

        // Verifica se a requisição foi bem-sucedida (código 201)
        if ($httpCode == 201) {
            // Processa os dados da resposta (decodifica o JSON)
            //echo "Lote criado com sucesso!\n";
            //echo "<br>";
            $responseData = json_decode($response, true);
            //print_r($responseData);

            // Retorna a chave do lote (batch)
            return $responseData['batch']['key'];
        } else {
            // Imprime uma mensagem de erro se a requisição falhou
            echo "<br>Erro na requisição: {$httpCode}\n";
            echo $response;
            return null;
        }
    } catch (Exception $e) {
        // Imprime uma mensagem de erro se ocorrer uma exceção durante a requisição
        echo "<br>Erro na requisição ultimo: {$e->getMessage()}\n";
        return null;
    }
}

// Função que envia notificação via WhatsApp
function notifyByWhatsApp($requestSignatureKey) {
    global $CLICKSIGN_API_URL, $ACCESS_TOKEN;

    // Dados para notificar por WhatsApp
    $requestData = [
        'request_signature_key' => $requestSignatureKey,
    ];

    // URL para notificar por WhatsApp
    $endpoint = "notify_by_whatsapp";

    // Monta a URL completa com o token de acesso
    $url = "{$CLICKSIGN_API_URL}$endpoint?access_token={$ACCESS_TOKEN}";

    // Exibe a URL
    //echo "URL da notificação por WhatsApp: " . $url . "\n<br>";

    // Cabeçalhos da requisição
    $headers = [
        'Accept: application/json',
        'Content-Type: application/json',
    ];

    try {
        // Inicia uma sessão cURL
        $ch = curl_init($url);

        // Configura opções cURL
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Retorna a resposta como string
        curl_setopt($ch, CURLOPT_POST, true); // Configura a requisição como POST
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData)); // Envia os dados no formato JSON
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); // Adiciona os cabeçalhos à requisição

        // Executa a requisição cURL e armazena a resposta
        $response = curl_exec($ch);

        // Verifica se houve algum erro durante a execução da requisição cURL
        if (curl_errno($ch)) {
            echo "Erro cURL: " . curl_error($ch) . "\n";
        }

        // Obtém o código de status HTTP da resposta
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // Fecha a sessão cURL
        curl_close($ch);

        // Verifica se a requisição foi bem-sucedida (código 202)
        if ($httpCode == 202) {
            // Processa os dados da resposta (decodifica o JSON)
            echo "Notificação por WhatsApp enviada com sucesso!\n";
            echo "<br>";
            $responseData = json_decode($response, true);
            //print_r($responseData);
        } else {
            // Imprime uma mensagem de erro se a requisição falhou
            echo "<br>Erro na requisição: {$httpCode}\n";
            echo $response;
        }
    } catch (Exception $e) {
        // Imprime uma mensagem de erro se ocorrer uma exceção durante a requisição
        echo "<br>Erro na requisição ultimos: {$e->getMessage()}\n";
    }
}
// ---------------------- UTILIZADO PARA TESTES ------------------------- //
/* 
echo "------------------- CRIANDO SIGNER  ------------------------<br>";
$signerKey = createSigner($nome, $fone, $cpf, $data_nascimento);
echo "<br><br>";
var_dump($signerKey);
echo "<br><br>";

echo "------------------- CRIANDO DOCUMENTO 1 ------------------------<br>";
$documentKey1 = createDocument($documento1, $signerKey);
echo "<br><br>";
var_dump($documentKey1);

echo "------------------- CRIANDO DOCUMENTO 2 ------------------------<br>";
$documentKey2 = createDocument($documento2, $signerKey);
echo "<br><br>";
var_dump($documentKey2);

echo "------------------- CRIANDO DOCUMENTO 3 ------------------------<br>";
$documentKey3 = createDocument($documento3, $signerKey);
echo "<br><br>";
var_dump($documentKey3);

echo "------------------- CRIANDO DOCUMENTO 6 ------------------------<br>";
$documentKey6 = createDocument($documento6, $signerKey);
echo "<br><br>";
var_dump($documentKey6);

echo "------------------- CRIANDO JUNÇÃO SIGNER E DOCUMENTO 1 ------------------------<br>";
$signerDocument1 = addSignerToDocument($documentKey1, $signerKey);
echo "<br><br>";
var_dump($signerDocument1);

echo "------------------- CRIANDO JUNÇÃO SIGNER E DOCUMENTO 2 ------------------------<br>";
$signerDocument2 = addSignerToDocument($documentKey2, $signerKey);
echo "<br><br>";
var_dump($signerDocument2);

echo "------------------- CRIANDO JUNÇÃO SIGNER E DOCUMENTO 3 ------------------------<br>";
$signerDocument3 = addSignerToDocument($documentKey3, $signerKey);
echo "<br><br>";
var_dump($signerDocument3);

echo "------------------- CRIANDO JUNÇÃO SIGNER E DOCUMENTO 6 ------------------------<br>";
$signerDocument6 = addSignerToDocument($documentKey6, $signerKey);
echo "<br><br>";
var_dump($signerDocument6);

echo "------------------- CRIANDO LOTES DE DOCUMENTOS  ------------------------<br>";
$batchKey = createBatch($signerKey, [$documentKey1, $documentKey2, $documentKey3, $documentKey6]);
echo "<br><br>";
// Exibir a chave do lote
echo "Chave do lote (batch): $batchKey";
echo "<br><br>";

echo "------------------- ENVIANDO VIA WHATSAPP  ------------------------<br>";
notifyByWhatsApp($batchKey);
*/

$signerKey = createSigner($nome, $fone, $cpf, $data_nascimento);

$documentKey1 = createDocument($documento1, $signerKey);
$documentKey2 = createDocument($documento2, $signerKey);
$documentKey3 = createDocument($documento3, $signerKey);
$documentKey6 = createDocument($documento6, $signerKey);

$signerDocument1 = addSignerToDocument($documentKey1, $signerKey);
$signerDocument2 = addSignerToDocument($documentKey2, $signerKey);
$signerDocument3 = addSignerToDocument($documentKey3, $signerKey);
$signerDocument6 = addSignerToDocument($documentKey6, $signerKey);

$batchKey = createBatch($signerKey, [$documentKey1, $documentKey2, $documentKey3, $documentKey6]);

notifyByWhatsApp($batchKey);

// Adicione o seguinte código JavaScript para exibir um alerta
//echo '<script>alert("Notificação por WhatsApp enviada com sucesso!");</script>';
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enviando documento...</title>
</head>
<body>
</body>
</html>


