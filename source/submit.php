<?php

/* config start */

$emailAddress = $_POST['email'];

/* config end */


require "phpmailer/class.phpmailer.php";

session_name("recoverypassword");
session_start();


foreach($_POST as $k=>$v)
{
	if(ini_get('magic_quotes_gpc'))
	$_POST[$k]=stripslashes($_POST[$k]);
	
	$_POST[$k]=htmlspecialchars(strip_tags($_POST[$k]));
}


$err = array();

if(!checkLen('email'))
	$err[]='Email troppo corta o vuota!';
else if(!checkEmail($_POST['email']))
	$err[]='Your email is not valid!';
        
if((int)$_POST['captcha'] != $_SESSION['expect'])
	$err[]='Somma errata!';


if(count($err))
{
	if($_POST['ajax'])
	{
		echo '-1';
	}

	else if($_SERVER['HTTP_REFERER'])
	{
		$_SESSION['errStr'] = implode('<br />',$err);
		$_SESSION['post']=$_POST;
		
		header('Location: '.$_SERVER['HTTP_REFERER']);
	}

	exit;
}

$indirizzo= "sales@romec.it";
        
        $z=false;
        $lettura_utenti = fopen("../dat/utenti.txt", "r") or exit("Impossibile aprire il file!");
        while(!feof($lettura_utenti))
        {
            $riga_utente = fgets($lettura_utenti);
            $pieces_riga_utente = explode("£/£", $riga_utente);
            if($emailAddress!="")
            {
                if(strtoupper($pieces_riga_utente[2]) == strtoupper($emailAddress))
                {
                    $msg="Gentile <b>".nl2br($pieces_riga_utente[1])."</b>,<br />La tua password &egrave;: ".nl2br($pieces_riga_utente[6])."<br /><br />Grazie per averci contattato,<br />Cordiali Saluti<br/><br />Ro.Mec. S.r.l.";
                    $mail = new PHPMailer();
                    $mail->IsSMTP();
                    $mail->SMTPDebug = 0;  // debug: 1 = solo messaggi, 2 = errori e messaggi
                    $mail->SMTPAuth = true;  // abilitiamo l'autenticazione
                    $mail->SMTPSecure = 'ssl'; // abilitiamo il protocollo ssl richiesto per Gmail
                    $mail->Host = 'smtp.gmail.com'; // ecco il server smtp di google
                    $mail->Port = 465; // la porta che dobbiamo utilizzare
                    $mail->Username = 'inforomec@gmail.com'; //email del nostro account gmail
                    $mail->Password = 'cristoforocolombo9'; //password del nostro account gmail
                    

                    $mail->AddReplyTo($indirizzo, "Ro.Mec. S.r.l.");
                    $mail->AddAddress($emailAddress);
                    $mail->SetFrom($indirizzo, "Ro.Mec. S.r.l.");
                    $mail->Subject = "Recupero Password | WebSite Romec.it";

                    $mail->MsgHTML($msg);

                    $mail->Send();
                    unset($_SESSION['post']);

                    if($_POST['ajax'])
                    {
                            echo '1';
                    }
                    else
                    {
                            $_SESSION['sent']=1;
                            
                            if($_SERVER['HTTP_REFERER'])
                                    header('Location: '.$_SERVER['HTTP_REFERER']);
                            
                            exit;
                    }
                    $z=true;
                }
            }
            else
            {
                echo "<error>Passaggio parametri errato!</error>";
            }
        }
        
        if(!$z)
        {
            if($_POST['ajax'])
            {
                    echo '-2';
            }

            else if($_SERVER['HTTP_REFERER'])
            {
                    $_SESSION['errStr'] = implode('<br />',$err);
                    $_SESSION['post']=$_POST;
                    
                    header('Location: '.$_SERVER['HTTP_REFERER']);
            }

            exit;
        }
        fclose($lettura_utenti);





function checkLen($str,$len=2)
{
	return isset($_POST[$str]) && mb_strlen(strip_tags($_POST[$str]),"utf-8") > $len;
}

function checkEmail($str)
{
	return preg_match("/^[\.A-z0-9_\-\+]+[@][A-z0-9_\-]+([.][A-z0-9_\-]+)+[A-z]{1,4}$/", $str);
}

?>
