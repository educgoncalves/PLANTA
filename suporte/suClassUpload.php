<?php
// É comum acharmos na internet procedures para upload de arquivos e imagens. 
// Mas, em casos especiais, apenas o upload não resolve. 
// Podemos precisar salvar as imagens com um tamanho máximo específico. 
// Não é nada prático redimensionar as imagens manualmente para depois fazermos o upload. 
// Vamos ver então, neste artigo, como resolver esse problema.

// Além de uma pasta chamada upload, faremos apenas dois arquivos neste exemplo:

// classupload.php: arquivo que contém a classe UploadImagem, que salva a imagem, 
// verifica o tamanho e, se necessário, redimensiona a imagem salva, 
// retornando uma mensagem de erro ou sucesso das operações.
// index.php: arquivo que contém o formulário com o input file, que será o responsável 
// por enviar nossa imagem. Recebemos a imagem também neste arquivo, 
// chamamos nele a classe UploadImagem e exibimos uma mensagem de acordo 
// com o resultado da operação.
//  -->
class UploadImagem{
    public $width; // Definida no arquivo index.php, será a largura máxima da nossa imagem
    public $height; // Definida no arquivo index.php, será a alturamáxima da nossa imagem
    protected $tipos = array("jpg", "jpeg", "png", "gif"); // Nossos tipos de imagem disponíveis para este exemplo

    // Função que irá redimensionar nossa imagem
    protected function redimensionar($caminho, $nomearquivo){
        // Determina as novas dimensões
        $width = $this->width;
        $height = $this->height;

        // Pegamos a largura e altura originais, além do tipo de imagem
        list($width_orig, $height_orig, $tipo, $atributo) = getimagesize($caminho.$nomearquivo);

        // Se largura é maior que altura, dividimos a largura determinada pela original e 
        // multiplicamos a altura pelo resultado, para manter a proporção da imagem
        if($width_orig > $height_orig){
            $height = ($width/$width_orig)*$height_orig;
        // Se altura é maior que largura, dividimos a altura determinada pela original e 
        // multiplicamos a largura pelo resultado, para manter a proporção da imagem
        } elseif($width_orig < $height_orig) {
            $width = ($height/$height_orig)*$width_orig;
        } // -> fim if
        
        // Criando a imagem com o novo tamanho
        $novaimagem = imagecreatetruecolor($width, $height);
        switch($tipo){
            // Se o tipo da imagem for gif
            case 1:
                // Obtém a imagem gif original
                $origem = imagecreatefromgif($caminho.$nomearquivo);
                // Copia a imagem original para a imagem com novo tamanho
                imagecopyresampled($novaimagem, $origem, 0, 0, 0, 0, $width,
                                    $height, $width_orig, $height_orig);
                // Envia a nova imagem gif para o lugar da antiga
                imagegif($novaimagem, $caminho.$nomearquivo);
                break;

            // Se o tipo da imagem for jpg
            case 2:
                // Obtém a imagem jpg original
                $origem = imagecreatefromjpeg($caminho.$nomearquivo);
                // Copia a imagem original para a imagem com novo tamanho
                imagecopyresampled($novaimagem, $origem, 0, 0, 0, 0, $width,
                                    $height, $width_orig, $height_orig);

                // Verifica rotação
                $rotation = null;
                $exif = exif_read_data($caminho.$nomearquivo);
                if (!empty($exif['Orientation'])) {
                    switch ($exif['Orientation']) {
                        case 3:
                            $rotation = 180;
                            break;
                        case 6:
                            $rotation = -90;
                            break;
                        case 8:
                            $rotation = 90;
                            break;
                    }
                }
                if ($rotation !== null) {
                    $novaimagem = imagerotate($novaimagem, $rotation, 0);
                }
                // Envia a nova imagem jpg para o lugar da antiga
                imagejpeg($novaimagem, $caminho.$nomearquivo);
                break;

            // Se o tipo da imagem for png
            case 3:
                // Obtém a imagem png original
                $origem = imagecreatefrompng($caminho.$nomearquivo);
                // Copia a imagem original para a imagem com novo tamanho
                imagecopyresampled($novaimagem, $origem, 0, 0, 0, 0, $width,
                                    $height, $width_orig, $height_orig);
                // Envia a nova imagem png para o lugar da antiga
                imagepng($novaimagem, $caminho.$nomearquivo);
                break;
        } // -> fim switch

        // Destrói a imagem nova criada e já salva no lugar da original
        imagedestroy($novaimagem);
        // Destrói a cópia de nossa imagem original
        imagedestroy($origem);
    } // -> fim function redimensionar()
    
    protected function tirarAcento($texto){
        // array com letras acentuadas
        $com_acento = array('à','á','â','ã','ä','å','ç','è','é','ê','ë','ì','í',
        'î','ï','ñ','ò','ó','ô','õ','ö','ù','ü','ú','ÿ','À','Á','Â','Ã','Ä','Å',
        'Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ñ','Ò','Ó','Ô','Õ','Ö','Ù','Ü','Ú',
        'Ÿ',);
        // array com letras correspondentes ao array anterior, porém sem acento
        $sem_acento = array('a','a','a','a','a','a','c','e','e','e','e','i','i',
        'i','i','n','o','o','o','o','o','u','u','u','y','A','A','A','A','A','A',
        'C','E','E','E','E','I','I','I','I','N','O','O','O','O','O','U','U','U',
        'Y',);
	    // procuramos no nosso texto qualquer caractere do primeiro array e
	    // substituímos pelo seu correspondente presente no 2º array
	    $final = str_replace($com_acento, $sem_acento, $texto);
	    // array com pontuação e acentos
	    $com_pontuacao = array('´','`','¨','^','~',' ','-');
	    // array com substitutos para o array anterior
	    $sem_pontuacao = array('','','','','','_','_');
	    // procuramos no nosso texto qualquer caractere do primeiro array e
	    // substituímos pelo seu correspondente presente no 2º array
	    $final = str_replace($com_pontuacao, $sem_pontuacao, $final);
	    // retornamos a variável com nosso texto sem pontuações, acentos e
        //letras acentuadas
	    return $final;
    } // -> fim function tirarAcento()

    // Função que irá fazer o upload da imagem
    public function salvar($caminho, $file, $fileNovo){
        // Retiramos acentos, espaços e hífens do nome da imagem
        $file['name'] = $this->tirarAcento(($file['name']));
        // Atribuímos caminho e nome da imagem a uma variável apenas
        if (array_search(substr($fileNovo,-3), $this->tipos) === false) { 
            $fileNovo .= strtolower(substr($file['name'],-4));
        }
        $uploadfile = $caminho.$fileNovo;

        // Guardamos na variável tipo o formato do arquivo enviado
        $partes = pathinfo($file['name']);
        $tipo = strtolower($partes['extension']);

        // Verifica se a imagem enviada é do tipo jpeg, png ou gif
        if (array_search($tipo, $this->tipos) === false) {
            $mensagem = array('tipo'=> 'danger','mensagem'=> 'Envie apenas imagens no formato jpg, jpeg, png ou gif!');
        }

        // Se a imagem temporária não for movida para onde a variável com caminho
        // e nome indica, exibiremos uma mensagem de erro
        else if (!move_uploaded_file($file['tmp_name'], $uploadfile)) {
            switch($file['error']){
                case 1:
                    $mensagem = array('tipo'=> 'danger','mensagem'=> 'O tamanho do arquivo é maior que o tamanho permitido.');
                    break;
                case 2:
                    $mensagem = array('tipo'=> 'danger','mensagem'=> 'O tamanho do arquivo é maior que o tamanho permitido.');
                    break;
                case 3:
                    $mensagem = array('tipo'=> 'danger','mensagem'=> 'O upload do arquivo foi feito parcialmente.');
                    break;
                case 4:
                    $mensagem = array('tipo'=> 'danger','mensagem'=> 'Não foi feito o upload de arquivo.');
                    break;
            } // -> fim switch
        }
        
        // Se a imagem temporária for movida
        else {
            // Pegamos sua largura e altura originais
            list($width_orig, $height_orig) = getimagesize($uploadfile);
            //Comparamos sua largura e altura originais com as desejadas
            if($width_orig > $this->width || $height_orig > $this->height){
                // Chamamos a função que redimensiona a imagem
                $this->redimensionar($caminho, $fileNovo);
            } // -> fim if
            // Exibiremos uma mensagem de sucesso
            $mensagem = array('tipo'=> 'success','mensagem'=> 'Upload realizado com sucesso!');
        } // -> fim else

        // Retornamos a mensagem com o erro ou sucesso
        return $mensagem;
    } // -> fim function salvar()
} // -> fim classe
?>

<!-- 
Listagem 1. Arquivo classupload.php
Veremos nesse artigo as expressões public e protected. 
Vamos a uma breve explicação sobre elas e sobre a expressão private, 
que não está presente neste exemplo.

Public: quando uma variável ou função (método) é declarada como public, 
        a mesma poderá ser acessada de qualquer lugar.
Protected: quando uma variável ou função (método) é declarada como protected, 
            a mesma só poderá ser acessada de dentro de sua classe ou por classes filhas.
Private: quando uma variável ou função (método) é declarada como private, 
            a mesma só poderá ser acessada de dentro de sua classe.

            Vamos ver a lista de variáveis e funções utilizadas neste exemplo. 

Variáveis:
public $width – Variável que contém a largura máxima da nossa imagem. 
                É definida no arquivo index.php.
public $height – Variável que contém a altura máxima da nossa imagem. 
                É definida no arquivo index.php.
protected $extensoes = array(jpg, png, gif) – Array que contém as extensões permitidas 
                neste exemplo.
Notem que as variáveis $width e $height estão declaradas como public. 
Portanto, podem ser acessadas e/ou determinadas de qualquer lugar. 
Já o array $extensoes foi declarado como protected. 
Isso significa que ele só pode ser acessado por funções (métodos) 
presentes na classe UploadImagem ou por uma classe filha desta, caso houvesse.

Funções:
protected function redimensionar() – é chamada na função salvar(). 
    Recebe o nome da imagem que foi salva e utiliza-se das variáveis $width e $height 
    para calcular e redimensionar a imagem recebida, mantendo a proporção da mesma.
protected function tirarAcento() – também é chamada na função salvar(). 
    Recebe uma variável com texto e procura nele caracteres que possam ser substituídos, 
    de acordo com alguns arrays presentes dentro da função.
public function salvar() – é chamada quando o formulário presente no arquivo index.php
     é enviado. Recebe a superglobal $_FILES que contém a imagem enviada pelo formulário 
     e o caminho onde a imagem será salva. Se a imagem for salva, a função obtém a 
     largura e altura da imagem e compara com a largura e altura máxima 
     permitida (determinadas também no arquivo index.php). 
     Se a largura ou altura forem maiores que o permitido, chama a função redimensionar(). 
     Retorna uma mensagem de sucesso ou erro do envio da imagem.
Notem que as funções tirarAcento() e redimensionar() foram declaradas como protected, 
sendo restritas apenas à classe e classes filhas, caso houvessem. 
Já a função salvar() foi declarada como public, podendo ser chamada de qualquer arquivo.

Notemos agora este bloco de código:

$tipo = strtolower(end(explode('/', $file['type'])));
if (array_search($tipo, $this->tipos) === false) {
$mensagem = "<font color='#F00'>Envie apenas imagens no formato jpeg, png ou gif!</font>";
return $mensagem;
}
-->

<!-- 
<form method="post" name="form1" enctype="multipart/form-data" action="index.php">
<fieldset>
<legend>Upload de Imagem</legend>
<strong>Fotografia:</strong>
<input type="file" name="img" id="img" />
<input type="submit" value="Enviar" />
-->
<!-- Determinamos via HTML um tamanho máximo para a nossa imagem
-->
<!--
<input type="hidden" name="MAX_FILE_SIZE" value="1024000" />
</fieldset>
</form>
-->
<!-- index.php
if(!empty($_FILES)){ // Se o array $_FILES não estiver vazio
// Incluímos o arquivo com a classe
include 'classupload.php';
// Associamos a classe à variável $upload
$upload = new UploadImagem();
// Determinamos nossa largura máxima permitida para a imagem
$upload->width = 250;
// Determinamos nossa altura máxima permitida para a imagem
$upload->height = 250;
// Exibimos a mensagem com sucesso ou erro retornada pela função salvar.
//Se for sucesso, a mensagem também é um link para a imagem enviada.
echo $upload->salvar("upload/", $_FILES['img']);
}
-->

<!-- 
Listagem 3. Arquivo index.php, dentro da tag body
Formulário de upload de imagem
Figura 1. Formulário de upload de imagem
Neste arquivo, temos o formulário que contém o input file que usaremos para procurar e 
enviar nossa imagem () e o botão submit (), responsável por enviar o formulário. 
Notem que, no formulário, determinamos um valor para o atributo enctype 
(enctype="multipart/form-data"). O padrão para enctype é application/x-www-form-urlencoded, 
mas alteramos esse valor para multipart/form-data porque estamos enviando um arquivo pelo 
formulário. Sem esta alteração, nosso arquivo não seria enviado.

Temos também um campo oculto (), que é responsável por informar ao browser o tamanho máximo 
permitido. É fácil burlar esta limitação, por isso, no arquivo php.ini há uma 
limitação de tamanho de upload. Alguns podem perguntar: já que esta limitação é falha, 
por que utilizá-la? A resposta é simples: informando ao browser um tamanho máximo, 
o usuário não precisará esperar o arquivo ser enviado para o servidor para depois 
descobrir que ele era grande demais.

Depois, verificamos se a superglobal $_FILES não está vazia (if(!empty($_FILES))). 
Caso não esteja, incluímos o arquivo que contém a classe 
UploadImagem (include 'classupload.php';), 
chamamos a classe ($upload = new UploadImagem();) , 
determinamos a largura ($upload->width = 250;) e altura ($upload->height = 250;) 
máximas da nossa imagem e mostramos a mensagem retornada da 
função salvar (echo $upload->salvar("upload/", $_FILES['img']);), 
passando como parâmetro, o caminho onde nossa imagem será salva (upload/) e a 
superglobal ($_FILES['img']) enviada pelo formulário que a contém. 
-->