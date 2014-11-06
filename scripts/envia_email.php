<?php
set_time_limit(600);
// Aqui vamos configurar o cabeçalho (header) do e-mail, formatos, remetentes, destinatários de cópias
$headers = "MIME-Version: 1.0\n";   // Abaixo estabelecemos o Remetente do E-mail com o From: 
$headers.= "From: candidaturas@gestyrest.com\r\n"; // Caso se queira especificar o e-mail de Resposta, utilizamos o Reply-To:, caso você não queira, basta excluir a linha abaixo 
//$headers.= "Reply-To: ".$_POST['email']."\r\n"; // Se desejar enviar cópia para outro e-mail utiliza-se o Bcc:, especificando o e-mail de destino, se não quiser mandar essa cópia, basta remover a linha abaixo--> Bcc = cópia oculta
//$headers.= "Bcc: fernando.jesus@gestyrest.com\r\n";
// Nesta linha abaixo, configuramos o "limite" ou boundary em inglês que é necessário para o arquivo em anexo.
$boundary = "XYZ-" . date("dmYis") . "-ZYX";
// Aqui abaixo, vamos colocar o corpo da mensagem, como vamos utilizar padrão HTML, teremos de utilizar tags HTML abaixo
 
// Cconfiguramos o e-mail do destinatário
$destinatario = "geral@gestyrest.com";
//Definimos o assuntos do nosso e-mail
$assunto = "Candidatura";
$tamanho = 1024 * 1024 * 5; //5 MB
$permitido = array("doc","docx","rtf","wri","txt","pdf");
$err_msg = NULL;

 
$corpo_mensagem = "
 
   <!--
   * { font-size:8pt; font-face:Verdana; list-style:none; padding:0; margin:0; }
   ul li { height:25px; border-bottom:dotted 1px #000; }
   ol { height: 25px; }
 
-->
<div>
<ul>
	<li>Nome: <strong>".$_POST['nome']."</strong></li>
	<li>E-mail: <strong>".$_POST['email']."</strong></li>
	<li>Telefone: <strong>".$_POST['telefone']."</strong></li>
	<li>Vaga: <strong>".$_POST['vaga']."</strong></li>
	<ol>Currículo: <strong>".nl2br($_POST['curriculo'])."</strong></ol>
</ul>
</div>
";
 
// Agora vem a parte própriamente dita do arquivo anexo.
// Aqui verificamos se foi enviado pelo formulário o arquivo. Lembrando que o nome do campo no formulário terá de ser "arquivo", caso você queira usar outro, terá de mudar aqui abaixo.
// Caso não tenha sido enviado um arquivo, ele desconsidera e envia o e-mail normalmente, mas sem nada anexado.
$arquivo = isset($_FILES["arquivo"]) ? $_FILES["arquivo"] : FALSE;
if(file_exists($arquivo["tmp_name"]) and !empty($arquivo)){
	
 	$extensao = strtolower(end(explode('.',$_FILES['arquivo']['name'])));
	if (!in_array($extensao, $permitido)) {
	//if(!preg_match("/\.(doc|docx|txt|wri|rtf|pdf)$/", $extensao)) {
		$err_msg = "Extensão não permitida ! Verifique a informação prestada na caixa à direita.";
	}
	else if ($tamanho < $FILES['arquivo']['size'] && $err_msg == NULL) {
		$err_msg= "Excedeu o tamanho permitido !";
	}
	else if ($err_msg == NULL){
	
		// Especificamos o tipo de conteúdo do e-mail
		$headers.= "Content-type: multipart/mixed; boundary=\"$boundary\"\r\n";
		$headers.= "$boundary\n"; 
 
		// Nesta linha abaixo, abrimos o arquivo enviado.
    	$fp = fopen($_FILES["arquivo"]["tmp_name"],"rb");
		// Agora vamos ler o arquivo aberto na linha anterior
	    $anexo = fread($fp,filesize($_FILES["arquivo"]["tmp_name"]));
		// Codificamos os dados com MIME para o e-mail
	    $anexo = base64_encode($anexo);
		// Fechamos o arquivo aberto anteriormente
		fclose($fp);
	    // Nesta linha a seguir, vamos dividir a variável do arquivo em pequenos pedaços para podermos enviar
		$anexo = chunk_split($anexo);
		// Nas linhas abaixo vamos passar os parâmetros de formatação e codificação, juntamente com a inclusão do arquivo anexado no corpo da mensagem.
   		$mensagem = "--$boundary\n";
	    $mensagem.= "Content-Transfer-Encoding: 8bits\n";
	    $mensagem.= "Content-Type: text/html; charset=\"ISO-8859-1\"\n\n";
	    $mensagem.= "$corpo_mensagem\n";
	    $mensagem.= "--$boundary\n";
	    $mensagem.= "Content-Type: ".$arquivo["type"]."\n";
	    $mensagem.= "Content-Disposition: attachment; filename=\"".$arquivo["name"]."\"\n";
	    $mensagem.= "Content-Transfer-Encoding: base64\n\n";
	    $mensagem.= "$anexo\n";
	    $mensagem.= "--$boundary--\r\n"; 
 
		// Após ter configurado tudo que é necessário, vamos fazer o envio propriamente dito
		if(mail($destinatario, $assunto, $mensagem, $headers)){  // enviando o email
			$err_msg = "Email enviado com sucesso !";
		}else{
			$err_msg = "Ocorreu um erro no envio do email !";
		}
	}
}else{
 
	$headers.= "Content-Type: text/html; charset=\"ISO-8859-1\"\n\n";
	// Nas linhas abaixo vamos passar os parâmetros de formatação e codificação, juntamente com a inclusão do arquivo anexado no corpo da mensagem.
	$mensagem = "$corpo_mensagem\n";
 
	// Após ter configurado tudo que é necessário, vamos fazer o envio propriamente dito
	if(mail($destinatario, $assunto, $mensagem, $headers)){  // enviando o email
		$err_msg = "Email enviado com sucesso !";
	}else{
		$err_msg = "Ocorreu um erro ao tentar enviar o email ";
	}
}
echo "<script type='text/javascript'>alert('{$err_msg}');</script>";
echo "<meta http-equiv='refresh' content='2;URL=http://www.gestyrest.com/teste/emprego.html'>";
?>