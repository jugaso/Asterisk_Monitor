<?php
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
    $mailer->Username = 'YOUR@MAIL.com';
    $mailer->Password = '************YOUR-PASSWORD************';
    $mailer->FromName = $assunto;
    $mailer->From = 'YOUR@MAIL.com';
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
  if( is_numeric($argv[1]))
  {
  require_once('class.phpmailer.php');
  $sip   =$argv[1];
  $status   =$argv[2];
  $assunto   ="$sip - $status";
  $message   ="$sip - $status";
  $destino = array("YOUR@MAIL.com","outros@emails.com");
  for($i=0;$i<2;$i++) {
      $retorno = enviaemail($assunto,$message,$destino[$i]);
            if($retorno==1)
        {
            $msg2 = array("msg"=>"1");
        }else{
            $msg2 = array("msg"=>"0");
        }
  }
  echo(json_encode($msg2));
  }else{
       $msg = array("msg"=>"OK");
               echo(json_encode($msg));
  }
}else{
       $msg = array("msg"=>"conecta");
          echo(json_encode($msg));
}
?>
*/
