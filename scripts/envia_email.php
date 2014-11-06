<?php
set_time_limit(600);
// Aqui vamos configurar o cabe�alho (header) do e-mail, formatos, remetentes, destinat�rios de c�pias
$headers = "MIME-Version: 1.0\n";   // Abaixo estabelecemos o Remetente do E-mail com o From: 
$headers.= "From: candidaturas@gestyrest.com\r\n"; // Caso se queira especificar o e-mail de Resposta, utilizamos o Reply-To:, caso voc� n�o queira, basta excluir a linha abaixo 
//$headers.= "Reply-To: ".$_POST['email']."\r\n"; // Se desejar enviar c�pia para outro e-mail utiliza-se o Bcc:, especificando o e-mail de destino, se n�o quiser mandar essa c�pia, basta remover a linha abaixo--> Bcc = c�pia oculta
//$headers.= "Bcc: fernando.jesus@gestyrest.com\r\n";
// Nesta linha abaixo, configuramos o "limite" ou boundary em ingl�s que � necess�rio para o arquivo em anexo.
$boundary = "XYZ-" . date("dmYis") . "-ZYX";
// Aqui abaixo, vamos colocar o corpo da mensagem, como vamos utilizar padr�o HTML, teremos de utilizar tags HTML abaixo
 
// Cconfiguramos o e-mail do destinat�rio
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
	<ol>Curr�culo: <strong>".nl2br($_POST['curriculo'])."</strong></ol>
</ul>
</div>
";
 
// Agora vem a parte pr�priamente dita do arquivo anexo.
// Aqui verificamos se foi enviado pelo formul�rio o arquivo. Lembrando que o nome do campo no formul�rio ter� de ser "arquivo", caso voc� queira usar outro, ter� de mudar aqui abaixo.
// Caso n�o tenha sido enviado um arquivo, ele desconsidera e envia o e-mail normalmente, mas sem nada anexado.
$arquivo = isset($_FILES["arquivo"]) ? $_FILES["arquivo"] : FALSE;
if(file_exists($arquivo["tmp_name"]) and !empty($arquivo)){
	
 	$extensao = strtolower(end(explode('.',$_FILES['arquivo']['name'])));
	if (!in_array($extensao, $permitido)) {
	//if(!preg_match("/\.(doc|docx|txt|wri|rtf|pdf)$/", $extensao)) {
		$err_msg = "Extens�o n�o permitida ! Verifique a informa��o prestada na caixa � direita.";
	}
	else if ($tamanho < $FILES['arquivo']['size'] && $err_msg == NULL) {
		$err_msg= "Excedeu o tamanho permitido !";
	}
	else if ($err_msg == NULL){
	
		// Especificamos o tipo de conte�do do e-mail
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
	    // Nesta linha a seguir, vamos dividir a vari�vel do arquivo em pequenos peda�os para podermos enviar
		$anexo = chunk_split($anexo);
		// Nas linhas abaixo vamos passar os par�metros de formata��o e codifica��o, juntamente com a inclus�o do arquivo anexado no corpo da mensagem.
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
 
		// Ap�s ter configurado tudo que � necess�rio, vamos fazer o envio propriamente dito
		if(mail($destinatario, $assunto, $mensagem, $headers)){  // enviando o email
			$err_msg = "Email enviado com sucesso !";
		}else{
			$err_msg = "Ocorreu um erro no envio do email !";
		}
	}
}else{
 
	$headers.= "Content-Type: text/html; charset=\"ISO-8859-1\"\n\n";
	// Nas linhas abaixo vamos passar os par�metros de formata��o e codifica��o, juntamente com a inclus�o do arquivo anexado no corpo da mensagem.
	$mensagem = "$corpo_mensagem\n";
 
	// Ap�s ter configurado tudo que � necess�rio, vamos fazer o envio propriamente dito
	if(mail($destinatario, $assunto, $mensagem, $headers)){  // enviando o email
		$err_msg = "Email enviado com sucesso !";
	}else{
		$err_msg = "Ocorreu um erro ao tentar enviar o email ";
	}
}
echo "<script type='text/javascript'>alert('{$err_msg}');</script>";
echo "<meta http-equiv='refresh' content='2;URL=http://www.gestyrest.com/teste/emprego.html'>";
?>