<?php 
// Verificando se login é válido para o aeroporto, sistema e quantidade de conexões permitidas
//
function loginUsuarioSenha($_usuario, $_senha){
    $_retorno = ['status' => 'ERRO', 'msg' => 'loginUsuarioSenha'];
    try {
        // Preparando chamada da API apiLogins
        $_token = gerarToken($_usuario);
        $_dados = ['usuario'=>$_usuario,'senha'=>$_senha];
        $_post = ['token'=>$_token,'funcao'=>'UsuarioSenha','dados'=>$_dados];
		$_retorno = executaAPIs('apiLogins.php', $_post);
		if ($_retorno['status'] == 'OK') {
            // Salva os dados encontados nas variáveis de sessão
            $_registros = $_retorno['dados'];
            foreach ($_registros as $_dados) {
                $_SESSION['plantaUsuario'] = $_dados['usuario'];
                $_SESSION['plantaSenha'] = $_senha;
                $_SESSION['plantaIDUsuario'] = $_dados['id'];
                $_SESSION['plantaIDAeroporto'] = $_dados['idAeroporto'];
                $_SESSION['plantaSistema'] = $_dados['sistema'];
            }
            switch ($_dados['qtdAcessos']) {
                case 0 :
                    throw new PDOException("Login sem acesso definido!");
                break;
                case 1 :
                    $_retorno = loginCompleto($_SESSION['plantaIDAeroporto'], $_SESSION['plantaSistema'], $_SESSION['plantaUsuario'], $_SESSION['plantaSenha']);
                break;
                default :
                    $_retorno = ['status' => 'OK', 'msg' => "Escolher site"];
            }
        }
    } catch (PDOException $e) {
        $_retorno = ['status' => 'ERRO', 'msg' => traduzPDO($e->getMessage())];
    }  
    return $_retorno;              
}

function loginCompleto($_aeroporto, $_sistema, $_usuario, $_senha){
    $_retorno = ['status' => 'ERRO', 'msg' => 'loginCompleto'];
    try {
        // Pegando IP
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $_ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $_ip = $_SERVER['REMOTE_ADDR'];
        }
        //caso utilize o cloudflare pode adicionar a linha abaixo para pegar o ip reverso
        //$_ip = (isset($_SERVER["HTTP_CF_CONNECTING_IP"])?$_SERVER["HTTP_CF_CONNECTING_IP"]:$_SERVER['REMOTE_ADDR']);

        // Preparando chamada da API apiLogins
        $_token = gerarToken('GEAR');
        $_dados = ['usuario'=>$_usuario,'senha'=>$_senha,'aeroporto'=>$_aeroporto,'sistema'=>$_sistema,'identificacao'=>$_ip];
        $_post = ['token'=>$_token,'funcao'=>'Completo','dados'=>$_dados];
        $_retorno = executaAPIs('apiLogins.php', $_post);
        if ($_retorno['status'] == 'OK') {
            // Salva os dados encontados nas variáveis de sessão
            $_registros = $_retorno['dados'];
            foreach ($_registros as $_dados) {
                if ($_dados['grupo'] == 'ADM' || 
                    $_dados['conexoesPermitidas'] > $_dados['conexoesAtivas']) {
                    $_SESSION['plantaUsuario'] = $_dados['usuario'];
                    $_SESSION['plantaIDUsuario'] = $_dados['id'];
                    $_SESSION['plantaNome'] = $_dados['nome'];
                    $_SESSION['plantaGrupo'] = $_dados['grupo'];
                    $_SESSION['plantaNivel'] = $_dados['nivel'];
                    $_SESSION['plantaEMail'] = $_dados['email'];
                    $_SESSION['plantaAeroporto'] = $_dados['aeroporto'];
                    $_SESSION['plantaIDAeroporto'] = $_dados['idAeroporto'];
                    $_SESSION['plantaNomeAeroporto'] = $_dados['nomeAeroporto'];
                    $_SESSION['plantaLocalidadeAeroporto'] = $_dados['localidade'];
                    $_SESSION['plantaUTCAeroporto'] = $_dados['utc'];
                    $_SESSION['plantaServidor'] = $_SERVER["REQUEST_SCHEME"]."://".$_SERVER["HTTP_HOST"].($_SERVER["HTTP_HOST"] == "localhost" ? "/PLANTA" : "");
                    $_SESSION['plantaSistema'] = $_dados['sistema'];
                    $_SESSION['plantaIPCliente'] = $_ip;

                    // Configurações
                    $_SESSION['plantaDebug'] = $_dados['debug'];
                    $_SESSION['plantaRegPorPagina'] = $_dados['regPorPagina'];
                    $_SESSION['plantaRefreshPagina'] = $_dados['tmpRefreshPagina'];
                    $_SESSION['plantaRefreshTela'] = $_dados['tmpRefreshTela'];
                    $_SESSION['plantaTaxiG1'] = $_dados['tmpTaxiG1'];
                    $_SESSION['plantaTaxiG2'] = $_dados['tmpTaxiG2'];

                    // Ativa conexão
                    $_retorno = ativarConexao($_SESSION['plantaIDAeroporto'], $_SESSION['plantaSistema'], $_SESSION['plantaUsuario'], $_SESSION['plantaGrupo'], $_SESSION['plantaIPCliente']);
                    if ($_retorno['status'] == "OK"){
                        $_SESSION['plantaIDConexao'] = $_retorno['idConexao'];
                    }
                } else {
                    throw new PDOException("Login Usuário - Número de conexões para este Aeroporto foram excedidas! Favor entrar em contato com o suporte. [".$_dados['grupo']."] [". $_dados['conexoesPermitidas']. "] [". $_dados['conexoesAtivas']);
                }
            }
        }
    } catch (PDOException $e) {
        $_retorno = ['status' => 'ERRO', 'msg' => traduzPDO($e->getMessage())];
    }
    return $_retorno;
}

// Verificando se login é válido pelo email
//
function loginUsuarioEmail($_usuario, $_email){
    try {
        // Preparando chamada da API apiLogins
        $_token = gerarToken($_usuario);
        $_dados = ['usuario'=>$_usuario,'email'=>$_email];
        $_post = ['token'=>$_token,'funcao'=>'UsuarioEmail','dados'=>$_dados];
		$_retorno = executaAPIs('apiLogins.php', $_post);
		if ($_retorno['status'] == 'OK') {
            $_registros = $_retorno['dados'];
            foreach ($_registros as $_dados) {
                $_usuario = $_dados['usuario'];
            }
            $_retorno = ['status' => 'OK', 'msg' => "Escolher site", 'usuario' => $_usuario];
        }
    } catch (PDOException $e) {
        $_retorno = ['status' => 'ERRO', 'msg' => traduzPDO($e->getMessage()), 'usuario' => ''];
    }  
    return $_retorno;              
}

// Enviar email para recuperação de senha
//
function enviarEmailRecuperarSenha($_usuario, $_email, $_senha){
    $_sistema = 'GEAR - Gerenciamento de Aeroportos';
    $email_conteudo = "";
    $email_conteudo .= "Prezado usuário ".$_usuario.", \n\n"; 
    $email_conteudo .= "Abaixo sua nova credencial para acesso: \n\n";
    $email_conteudo .= "E-mail: ".$_email." \n"; 
    $email_conteudo .= "Senha: ".$_senha." \n\n"; 
    $email_conteudo .= "Retorne ao site utilizando as novas credenciais. \n\n"; 
    if (enviarEmail($_sistema, '', $_usuario, $_email, 'Recuperação de Senha', $email_conteudo)) {
        $_retorno = ['status' => 'OK', 'msg' => "Email enviado!"];
    } else {
        $_retorno = ['status' => 'OK', 'msg' => "Email não pode ser enviado!"];
    }	
    return $_retorno; 
}

// Criar senha randomica para o usuario
//
function criarSenhaRandomica($_usuario, $_email) {
    try {
        // Preparando chamada da API apiLogins
        $_token = gerarToken($_usuario);
        $_dados = ['usuario'=>$_usuario,'email'=>$_email];
        $_post = ['token'=>$_token,'funcao'=>'SenhaRandomica','dados'=>$_dados];
		$_retorno = executaAPIs('apiManterUsuarios.php', $_post);
		if ($_retorno['status'] == 'OK') {
            $_retorno = ['status' => 'OK', 'msg' => "Escolher site", 'senha' => $_retorno['senhaRandomica']];
        }
    } catch (PDOException $e) {
        $_retorno = ['status' => 'ERRO', 'msg' => traduzPDO($e->getMessage()), 'senha' => ''];
    }  
    return $_retorno;              
}

// Ativa conexão do IP
//
function ativarConexao($__aeroporto, $__sistema, $__usuario, $__grupo, $__identificacao) {
    $__retorno = ['status' => 'ERRO', 'msg' => 'ativarConexao'];

    try {
		// Desativa qualquer conexão que esteja ativada com as mesmas informações
        desativarConexao($__aeroporto, $__sistema, $__usuario, $__identificacao);

        // Ativa a conexão com as informações
		$__conexao = conexao();
        $__comando = "INSERT INTO gear_conexoes(idAeroporto, sistema, usuario, grupo, identificacao, entrada, cadastro) VALUES (".
                    $__aeroporto.", '".$__sistema."', '".$__usuario."', '".$__grupo."', '".
                    $__identificacao."', UTC_TIMESTAMP(), UTC_TIMESTAMP())";
        $__sql = $__conexao->prepare($__comando);
        if ($__sql->execute()) {
			if ($__sql->rowCount() > 0) {
                $__retorno = ['status' => 'OK', 'msg' => 'Conectar', 'idConexao' => $__conexao->lastInsertId()];
            } else {
                throw new PDOException("Não consegui ativar a conexão para a Identificação ".$__identificacao."!");
            }
        } else {
            throw new PDOException("Ativação da conexão não pode ser executada! [".$__sql->errorInfo()."]");
        }
    } catch (PDOException $e) {
        $__retorno = ['status' => 'ERRO', 'msg' => traduzPDO($e->getMessage()), 'idConexao' => ''];
    }
    return $__retorno;
}

// Desativa conexão do IP
//
function desativarConexao($__aeroporto, $__sistema, $__usuario, $__identificacao) {
    $__retorno = ['status' => 'OK', 'msg' => 'desativarConexao'];
    try {
		// Pegando usuário do login
		$__conexao = conexao();
        $__comando = "UPDATE gear_conexoes SET saida = UTC_TIMESTAMP, situacao = 'INA' WHERE situacao = 'ATV' ".
                    ($__aeroporto != "" ? " AND idAeroporto = ".$__aeroporto : "").
                    ($__sistema != "" ? " AND sistema = '".$__sistema."'" : "").
                    ($__usuario != "" ? " AND usuario = '".$__usuario."'" : "").
                    ($__identificacao != "" ? " AND identificacao = '".$__identificacao."'" : ""); 
        $__sql = $__conexao->prepare($__comando);
        if ($__sql->execute()) {
			if ($__sql->rowCount() > 0) {
            } else {
                throw new PDOException("Não consegui desativar sua conexão!");
            }
        } else {
            throw new PDOException("Desativação da sua conexão não pode ser executada! [".$__sql->errorInfo()."]");
        }
    } catch (PDOException $e) {
        $__retorno = ['status' => 'ERRO', 'msg' => traduzPDO($e->getMessage())];
    }
    return $__retorno;
}
?>