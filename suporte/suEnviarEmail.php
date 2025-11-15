<?php
require_once("../ativos/PHPMailer/PHPMailerAutoload.php");
require_once("suConexao.php");

// Enviar email da vistoria de veículos pelo google
//
function enviarEmail($_sistema, $_aeroporto, $_usuario, $_email, $_assunto, $_mensagem, $_anexo = ''){
	$_retorno = false;

    // Enviando o email
	try {
		//Create a new PHPMailer instance
		$_mail = new PHPMailer;
    $_mail->SMTPDebug = 3; // Nível de depuração recomendado para testes
		//Tell PHPMailer to use SMTP
		$_mail->isSMTP();
		$_mail->CharSet = "UTF-8";

		//Enable SMTP debugging
		// 0 = off (for production use)
		// 1 = client messages
		// 2 = client and server messages
		$_mail->SMTPDebug = 0;

		//Ask for HTML-friendly debug output
		//$_mail->Debugoutput = 'html';

		//Set the hostname of the mail server
		$_mail->Host = 'smtp.gmail.com';
		// use
		// $_mail->Host = gethostbyname('smtp.gmail.com');
		// if your network does not support SMTP over IPv6

		//Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
		$_mail->Port = 587;
		//$_mail->Port = 465;

		//Set the encryption system to use - ssl (deprecated) or tls
		$_mail->SMTPSecure = 'tls';
		//$_mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;

		//Whether to use SMTP authentication
		$_mail->SMTPAuth = true;

		//Username to use for SMTP authentication - use full email address for gmail
		// Caso tenha problema de acesso, conectar no google com esta conta e acessar o
		// link https://myaccount.google.com/apppasswords para gerar uma senha de app
		//
		$_mail->Username = 'encode.informatica@gmail.com';
		//Password to use for SMTP authentication
		//$_mail->Password = 'bfgv jhtp jwmy rqij';
		$_mail->Password = 'yrju nlhk vpqn yjtz';

		//Set who the message is to be sent from
		$_mail->setFrom('encode.informatica@gmail.com', '');

		//Set who the message is to be sent to
		//
		switch ($_assunto) {
			case 'Suporte':
				$_mailDestino = 'suporte@decolamais.com.br';
				$_mail->addAddress($_mailDestino);
				$_mail->addReplyTo($_email, '');
			break;

			case 'Elogio':
			case 'Reclamação':
			case 'Sugestão':
			case 'Outros':
				$_mailDestino = 'contato@decolamais.com.br';
				$_mail->addAddress($_mailDestino);
				$_mail->addReplyTo($_email, '');
			break;

			case 'Recuperação de Senha':
				$_mail->addAddress($_email);
			break;

			default:
				// Pegando todos os administradores do sistema
				try {
					$_mailDestino = 'suporte@decolamais.com.br';
					$_conexao = conexao();
					$_comando = "SELECT DISTINCT us.email 
								FROM gear_usuarios us 
								LEFT JOIN gear_acessos ac ON ac.idUsuario = us.id
								WHERE us.situacao = 'ATV' AND ac.grupo = 'ADM'";
					$_sql = $_conexao->prepare($_comando);
					if ($_sql->execute()) {
						if ($_sql->rowCount() > 0) {		
							$_registros = $_sql->fetchAll(PDO::FETCH_ASSOC);
							foreach ($_registros as $_dados) {
								$_mail->addAddress($_dados['email']);
							}
						} else {
							$_mail->addAddress($_mailDestino);
						}
					} else {
						$_mail->addAddress($_mailDestino);
					}
				} catch (PDOException $e) {
					$_mail->addAddress($_mailDestino);
				}
			break;
		}

		//Set the subject line
		$_mail->Subject = $_sistema.
						($_aeroporto != "" ? " - ".$_aeroporto : "").
						($_usuario != "" ? " - ".$_usuario : "").
						($_assunto != "" ? " - ".$_assunto : "");

		//====================================================
		// Monta o Corpo da mensagem
		//====================================================
		$_mail->Body = nl2br($_mensagem);
		//Replace the plain text body with one created manually
		$_mail->AltBody = $_mensagem;

		//====================================================
		// Monta a parte de anexo da mensagem 
		//====================================================
		if ($_anexo != '') {
			if (file_exists($_anexo)) {
				$_mail->addAttachment($_anexo);
			}
		}

		//Read an HTML message body from an external file, convert referenced images to embedded,
		//convert HTML into a basic plain-text alternative body
		//$_mail->msgHTML(file_get_contents('contents.html'), dirname(__FILE__));

		//Enviando o email
		$_retorno = $_mail->send(); 

	} catch (Exception $_e) {
		montarMensagem("danger",array($_mail->ErrorInfo));
	}
	return $_retorno;
} 

function enviarTitanEmail($_sistema, $_aeroporto, $_usuario, $_email, $_assunto, $_mensagem, $_anexo = ''){
	$_retorno = false;

    // Enviando o email
	try {
		//Create a new PHPMailer instance
		$_mail = new PHPMailer;

		//Tell PHPMailer to use SMTP
		$_mail->isSMTP();
		$_mail->CharSet = "UTF-8";
		$_mail->IsHTML(true);

		//Enable SMTP debugging
		// 0 = off (for production use)
		// 1 = client messages
		// 2 = client and server messages
		$_mail->SMTPDebug = 0;

		//Ask for HTML-friendly debug output
		//$_mail->Debugoutput = 'html';

		//Set the hostname of the mail server
		$_mail->Host = 'smtp.titan.email';
		// use
		// $_mail->Host = gethostbyname('smtp.gmail.com');
		// if your network does not support SMTP over IPv6

		//Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
		$_mail->Port = 587;
		//$_mail->Port = 465;

		//Set the encryption system to use - ssl (deprecated) or tls
		$_mail->SMTPSecure = 'tls';
		//$_mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;

		//Whether to use SMTP authentication
		//$_mail->SMTPAuth = true;

		//Username to use for SMTP authentication - use full email address for gmail
		$_mail->Username = 'encode.informatica@gmail.com';

		//Password to use for SMTP authentication
		$_mail->Password = 'bfgv jhtp jwmy rqij';

		//Set who the message is to be sent from
		$_mail->setFrom('encode.informatica@gmail.com', '');

		//Set an alternative reply-to address
		$_mail->addReplyTo($_email, '');

		//Set who the message is to be sent to
		//
		// Pegando todos os administradores do sistema
		try {
            $_conexao = conexao();
			$_comando = "SELECT DISTINCT us.email 
						FROM gear_usuarios us 
						LEFT JOIN gear_acessos ac ON ac.idUsuario = us.id
						WHERE us.situacao = 'ATV' AND ac.grupo = 'ADM'";
			$_sql = $_conexao->prepare($_comando);
			if ($_sql->execute()) {
				if ($_sql->rowCount() > 0) {		
					$_registros = $_sql->fetchAll(PDO::FETCH_ASSOC);
					foreach ($_registros as $_dados) {
						$_mail->addAddress($_dados['email']);
	                }
				} else {
					$_mail->addAddress($_mail->Username);
				}
            } else {
				$_mail->addAddress($_mail->Username);
			}
        } catch (PDOException $e) {
            $_mail->addAddress($_mail->Username);
        }

		//Set the subject line
		$_mail->Subject = $_sistema.
						($_aeroporto != "" ? " - ".$_aeroporto : "").
						($_usuario != "" ? " - ".$_usuario : "").
						($_assunto != "" ? " - ".$_assunto : "");

		//====================================================
		// Monta o Corpo da mensagem
		//====================================================
		$_mail->Body = nl2br($_mensagem);
		//Replace the plain text body with one created manually
		$_mail->AltBody = $_mensagem;

		//====================================================
		// Monta a parte de anexo da mensagem 
		//====================================================
		if ($_anexo != '') {
			if (file_exists($_anexo)) {
				$_mail->addAttachment($_anexo);
			}
		}

		//Read an HTML message body from an external file, convert referenced images to embedded,
		//convert HTML into a basic plain-text alternative body
		//$_mail->msgHTML(file_get_contents('contents.html'), dirname(__FILE__));

		//Enviando o email
		$_retorno = $_mail->send(); 

	} catch (PDOException $_e) {
		montarMensagem("danger",array($_e->getMessage()));
	}
	return $_retorno;
} 
?>