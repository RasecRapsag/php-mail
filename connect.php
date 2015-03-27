<?php
	include( 'class/TImap.class.php' );

	header( 'Content-type: text/html; charset=UTF-8' );

	$imap = '{imap.gmail.com:993/imap/ssl}';
	$usuario = 'usuario';
	$senha = 'senha';

	$mailbox = new TImap( $imap, $usuario, $senha );

	echo '<br>';

	if ( $mailbox->getStatus() )
	{
		echo 'OK';
		echo '<br>';
		//echo 'Emails: ' . $mailbox->getNumMsgs();
		//echo '<br>';
		//echo 'Recentes: ' . $mailbox->getNumRecent();
		//$mailbox->changeMailbox( 'Steam' );
		//echo 'Emails: ' . $mailbox->getNumMsgs();		
		//echo '<br>';
		echo 'Qde de pastas: ' . $mailbox->getNumFolders();
		echo '<pre>';
		print_r( $mailbox->getFolders() );
		echo '</pre>';
	}
	else
	{
		echo 'Erro: ' . $mailbox->getError();
	}

	echo '<br><br>';

?>