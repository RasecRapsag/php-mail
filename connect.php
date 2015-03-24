<?php
	include( 'class/TImap.class.php' );

	header( 'Content-type: text/html; charset=UTF-8' );

	$mailbox = new TImap( '{imap.gmail.com:993/imap/ssl}', 'usuario', 'senha' );

	echo '<br>';
	echo 'Erro: ' . $mailbox->getError() . '<br>';
	echo '<pre>';
	print_r( $mailbox->getErrors() );
	echo '</pre>';
?>