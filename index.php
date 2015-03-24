<?php
	$conn = '{imap.gmail.com:993/imap/ssl}';
	$login = 'usuario';
	$passwd = 'senha';

	// Open connection
	$mailbox = imap_open( $conn, $login, $passwd );
	if ( !$mailbox )
		die( 'Erro ao conectar: ' . imap_last_error() );

	$check = imap_check( $mailbox );
	echo 'Total Mensagens: '. $check->Nmsgs . '<br>';

	// Listando marcadores da conta do Gmail
	$marks = imap_getmailboxes( $mailbox, $conn, '*');

	if ( is_array( $marks ) )
	{
		foreach ( $marks as $mark )
		{
			$name = str_replace( $conn, '', $mark->name );
			$pos = strpos( $name, $mark->delimiter );
			if ( $pos !== false )
				$name = substr( $name, $pos + 1 );
			echo $name . '<br>';
		}
	}
	else
	{
		echo imap_last_error();
	}

	imap_close( $mailbox );
?>