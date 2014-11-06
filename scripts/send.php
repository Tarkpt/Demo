<?php
$email_text = "";
foreach($_POST as $key => $value){
	if($value != ""){$email_text.="<br>".ucfirst(str_replace("_", " ",$key))." - ".utf8_decode(stripcslashes($value));}
}
$to = "geral@gestyrest.com";
$subject = utf8_encode("Contacto");
$text_email = utf8_encode($email_text);
$header = "MIME-Version: 1.0\n" . "Content-type: text/html; charset=utf-8\n";
if(mail($to, $subject, $text_email, $header)){
	//This message gets display to user on success
	echo "<script type=\"text/javascript\">alert(\"Mensagem enviada com sucesso\")</script>";
	}
else
{
	//Message displayed to user on failure
	echo "<script type=\"text/javascript\">alert(\"Erro no envio do email. Volte a tentar...\")</script>";
}
echo "<meta http-equiv='refresh' content='2;URL=http://www.gestyrest.com/teste/index.html'>";
?>
