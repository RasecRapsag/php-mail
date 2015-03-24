<?php
	/*
	 *	Classe: TImap
	 *	Descrição: Gerencia conexões imap
	 *	Autor: Cesar A. Gaspar
	 *	Criação: 23/03/2015
	 *	Alteração: 
	 */
	final class TImap
	{
		private $server;
		private $login;
		private $passwd;
		private $mailbox;
		private $error;
		private $errors;

		/*
		 *	Método: __construct()
		 *	Descrição: Inicializa os atributos e abre a conexão com o servidor imap.
		 */
		public function __construct( $server = '{imap.gmail.com:993/imap/ssl}', $login = 'teste', $passwd = '123456' )
		{
			echo 'Iniciando conexão imap...<br>';
			$this->server = $server;
			$this->login = $login;
			$this->passwd = $passwd;
			$this->connect();
		}
		
		/*
		 *	Método: __destruct()
		 *	Descrição: Finalizando conexão com o servidor imap.
		 */
		public function __destruct()
		{
			echo 'Finalizando conexão imap...<br>';
			$this->disconnect();
		}

		/*
		 *	Método: connect()
		 *	Descrição: Faz a conexão com o servidor imap.
		 */
		private function connect()
		{
			try
			{
				$this->mailbox = @imap_open( $this->server, $this->login, $this->passwd );			
				if ( !$this->mailbox )
					throw new Exception( 'Não foi possível estabelecer conexão com o servidor imap!' );
			}
			catch ( Exception $e )
			{
				$this->error = $e->getMessage();
				$this->errors = imap_errors();
			}
		}

		/*
		 *	Método: disconnect()
		 *	Descrição: Fecha a conexão com o servidor imap.
		 */
		private function disconnect()
		{
			@imap_close( $this->mailbox );
		}

		/*
		 *	Método: getError()
		 *	Descrição: Retorna a última mensagem de erro.
		 *	@return = String.
		 */
		public function getError()
		{
			return $this->error;
		}

		/*
		 *	Método: getErrors()
		 *	Descrição: Retorna mensagens de erro do imap.
		 *	@return = Array.
		 */
		public function getErrors()
		{
			return $this->errors;
		}

	}
?>
