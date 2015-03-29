<?php
	include( 'class/TImap.class.php' );

	header( 'Content-type: text/html; charset=UTF-8' );

	$imap = '{imap.gmail.com:993/imap/ssl}';
	$usuario = 'usuario';
	$senha = 'senha';

	$mailbox = new TImap( $imap, $usuario, $senha );
	// $mailbox = new TImap();
	// $mailbox->renameFolder( 'Steam3', 'Steam2' );
	// exit;
	// $mailbox->removeFolder( 'Steam2' );
	// $mailbox->addFolder( 'Steam2' );
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
		// echo '<br>';
		// echo 'Emails: ' . $mailbox->getNumMsgs();
		// echo '<br>';
		// echo 'Recentes: ' . $mailbox->getNumRecent();
		// $mailbox->changeMailbox( 'Steam' );
		// echo 'Emails: ' . $mailbox->getNumMsgs();		
		// echo '<br>';
		// echo 'Qde de pastas: ' . $mailbox->getNumFolders();
		$pastas = $mailbox->getFolders();
		// echo '<pre>';
		// print_r( $pastas );
		// echo '</pre>';

		echo '[ ' . $mailbox->selectMailbox( $pastas[4] ) . ' ]<br>';
		// echo 'Nome: ' . $mailbox->getFolder() . '<br>';
		// echo 'Emails: ' . $mailbox->getNumMessages() . '<br>';
		// echo 'Novos: ' . $mailbox->getNumUnreadMessages() . '<br>';
		$headers = $mailbox->getMessages();

		// foreach( $headers as $header )

		// if ( $mailbox->createFolder( 'Steam3' ) )
		// if ( $mailbox->removefolder( 'Steam3' ) )
		// if ( $mailbox->renameFolder( 'Steam3', 'Steam2' ) )
		// 	echo '<br>Sucesso!<br>';
		// else
		// 	echo '<br>'. $mailbox->getError() . '<br>';
	}
	else
	{
		echo 'Erro: ' . $mailbox->getError();
	}

	echo '<br><br>';

?>