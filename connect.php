<?php
	include( 'class/TImap.class.php' );

	header( 'Content-type: text/html; charset=UTF-8' );

	$imap = '{imap.gmail.com:993/imap/ssl}';
	$usuario = 'usuario';
	$senha = 'senha';

	$mailbox = new TImap( $imap, $usuario, $senha );
	//$mailbox = new TImap();
	// $mailbox->selectMailbox( 'Pessoal' );
	// echo $mailbox->getNumUnreadMessages();
	// echo '<br>';
	// echo $mailbox->getError();
	// echo '<br>';
	// exit;

	echo '<br>';

	if ( $mailbox->getStatus() )
	{
		echo 'OK';
		//echo '<br>';
		//echo 'Emails: ' . $mailbox->getNumMsgs();
		//echo '<br>';
		//echo 'Recentes: ' . $mailbox->getNumRecent();
		//$mailbox->changeMailbox( 'Steam' );
		//echo 'Emails: ' . $mailbox->getNumMsgs();		
		//echo '<br>';
		//echo 'Qde de pastas: ' . $mailbox->getNumFolders();
		$pastas = $mailbox->getFolders();
		echo '<pre>';
		print_r( $pastas );
		echo '</pre>';

		echo '[ ' . $mailbox->selectMailbox( $pastas[1] ) . ' ]<br>';
		echo 'Caixa atual: ' . $mailbox->getFolder() . '<br>';
		echo 'Total emails: ' . $mailbox->getNumMessages() . '<br>';
		echo 'Emails novos: ' . $mailbox->getNumUnreadMessages() . '<br>';
	}
	else
	{
		echo 'Erro: ' . $mailbox->getError();
	}

	echo '<br><br>';

?>