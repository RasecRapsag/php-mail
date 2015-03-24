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
		private $server;		/* Endereço e protocolo do servidor IMAP */
		private $login;			/* Usuário da conta de email */
		private $passwd;		/* Senha da conta de email */
		private $imap;			/* Recurso com informações de IMAP */
		private $mailbox;		/* Caixa de correio atual */
		private $error;			/* Informa se o último comando teve erro */
		private $error_msg;		/* Mensagem do último erro */
		private $error_imap;	/* Mensagem ocorridas no protocolo IMAP */

		/*
		 *	Método: __construct()
		 *	Descrição: Inicializa os atributos e abre a conexão com o servidor imap.
		 */
		public function __construct( $server, $login, $passwd )
		{
			echo 'Iniciando conexão imap...<br>';
			$this->server = $server;
			$this->login = $login;
			$this->passwd = $passwd;
			$this->mailbox = null;
			$this->error = false;
			$this->error_msg = null;
			$this->error_impa = null;
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
				$this->imap = @imap_open( $this->server, $this->login, $this->passwd, OP_READONLY );
				if ( !$this->imap )
					throw new Exception( 'Não foi possível estabelecer conexão com o servidor IMAP!', 1 );
			}
			catch ( Exception $e )
			{
				$this->error = true;
				$this->error_msg = $e->getMessage();
				$this->error_imap = @imap_errors();
			}
		}

		/*
		 *	Método: disconnect()
		 *	Descrição: Fecha a conexão com o servidor imap.
		 */
		private function disconnect()
		{
			@imap_close( $this->imap );
		}

		/*
		 *	Método: getStatus()
		 *	Descrição: Retorna status do último comando.
		 *	@return = Bool.
		 */
		public function getStatus()
		{
			return !$this->error;
		}

		/*
		 *	Método: getError()
		 *	Descrição: Retorna última mensagem de erro.
		 *	@return = String.
		 */
		public function getError()
		{
			return $this->error_msg;
		}

		/*
		 *	Método: getErrors()
		 *	Descrição: Retorna mensagens de erro do imap.
		 *	@return = Array.
		 */
		public function getErrors()
		{
			return $this->error_imap;
		}

		/*
		 *	Método: check()
		 *	Descrição: Faz a checagem da caixa de correio atual.
		 */
		private function check()
		{
			try
			{
				$this->mailbox = @imap_check( $this->imap );
				if( !$this->mailbox )
					throw new Exception("Não foi possível checar caixa de correio atual.", 2 );
			}
			catch ( Exception $e )
			{
				$this->error = true;
				$this->error_msg = $e->getMessage();
				$this->error_imap = @imap_errors();
			}
			echo '<pre>';
			print_r( $this->mailbox );
			echo '</pre>';
		}

		/*
		 *	Método: getNumMsgs()
		 *	Descrição: Retorna a quantidade de e-mails da caixa de correio atual.
		 *	@return = Integer. 
		 */
		public function getNumMsgs()
		{
			return @imap_num_msg( $this->imap );
		}

		/*
		 *	Método: getNumRecent()
		 *	Descrição: Retorna a quantidade de e-mails recentes da caixa de correio atual.
		 *	@return = Integer
		 */
		public function getNumRecent()
		{
			return @imap_num_recent( $this->imap );
		}

		/*
		 *	Método: changeMailbox()
		 *	Descrição: Altera pasta atual do email. 
		 *	@param $folder = caixa de correio
		 */
		public function changeMailbox( $folder )
		{
			//$this->folder = $folder;
			@imap_reopen( $this->imap, $this->server . $folder, OP_READONLY );
		}
	}
?>
