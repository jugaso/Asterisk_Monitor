<?php
/*substitua ----yourmail@domain.com----- */
$gmtDate = gmdate("D, d M Y H:i:s"); 
header("Expires: {$gmtDate} GMT"); 
header("Last-Modified: {$gmtDate} GMT"); 
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache");
header("Access-Control-Allow-Origin: *");/*aceita post de todas os sites*/
header('Content-Type: text/json; charset=utf-8');

function enviaemail($assunto,$msg,$destino)
{
    $mensagem = "Informações do Asterisk.\n".$msg."\n";
    $mailer = new PHPMailer();
    $mailer->IsSMTP();
    $mailer->SMTPAuth = true;
    $mailer->Host = 'smtp.gmail.com';
    $mailer->Port = 465;
    $mailer->SMTPDebug = 1;
    $mailer->SMTPSecure = 'ssl';
    $mailer->IsHTML(false);
    $mailer->Username = '----yourmail@domain.com-----';
    $mailer->Password = '************YOUR-PASSWORD************';
    $mailer->FromName = $assunto;
    $mailer->From = '----yourmail@domain.com-----'; /*não obrigatório*/
    $mailer->AddAddress($destino);
    $mailer->Subject = $assunto;
    $mailer->Body = $mensagem;
          if($mailer->Send()){
                return 1;

          }else{
              return 0;
          }
}
if(! is_null($argv[1]))
{
  if(! is_null($argv[1]))
  {
  require_once('class.phpmailer.php');
  $sip = $argv[1];
  $status = $argv[2];
  $assunto = "$sip - $status";
  $message = "$sip - $status";
  $destino = array("----yourmail@domain.com-----","outro@email.com");
  for($i=0;$i<2;$i++) {
      $retorno = enviaemail($assunto,$message,$destino[$i]);
            if($retorno==1)
        {
            $msg2 = array("msg"=>"1");
        }else{
            $msg2 = array("msg"=>"0");
        }
  }
  }else{
       $msg = array("msg"=>"OK");
  }
}else{
       $msg = array("msg"=>"conecta");
}
?>
