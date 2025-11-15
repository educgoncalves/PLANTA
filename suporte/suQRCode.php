<?php 
require_once("../ativos/qrcode/vendor/autoload.php");

function qrCode($_url) {
    // Gerar QRCode: instanciar a classe QRCode e enviar os dados para o render gerar o QRCode
    $_qrCode = (new \chillerlan\QRCode\QRCode())->render($_url);
    return $_qrCode;
}
?>