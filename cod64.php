<?php

if (!function_exists('docx_to_base64')) {
  function docx_to_base64($docPath) {
    try {
        // Verificar se o arquivo existe
        if (!file_exists($docPath)) {
            throw new Exception("<br>Arquivo não encontrado: $docPath");
        }

        // Lê o conteúdo do arquivo Word
        $file_content = file_get_contents($docPath);

        // Verificar se a leitura do arquivo foi bem-sucedida
        if ($file_content === false) {
            throw new Exception("<br>Erro ao ler o conteúdo do arquivo: $docPath");
        }

        // Codifica para Base64
        $base64_content = base64_encode($file_content);

        // Verificar se a codificação foi bem-sucedida
        if ($base64_content === false) {
            throw new Exception("<br>Erro ao codificar o conteúdo para Base64");
        }

        return $base64_content;
    } catch (Exception $e) {
        // Tratar exceções e exibir mensagem de erro
        echo "Erro: " . $e->getMessage() . "\n";
        return null;
    }
  }
}

// Exemplo de uso:
$docx_path = "./modelos/capaProcesso.docx";

//RESULTADO
$base64_content = docx_to_base64($docx_path);

if ($base64_content !== null) {
    //echo "Conteúdo do arquivo em formato Base64:\n";
    //echo $base64_content . "\n<br><br>";
    // Agora, você pode usar $base64_content no payload da sua solicitação.
} else {
    echo "Falha ao processar o arquivo.\n";
}

?>
