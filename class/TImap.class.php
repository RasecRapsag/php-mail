<?php
	/**
	 *	Classe para gerenciar recursos de correio eletrônico por meio do protocolo Imap.
	 *	Esta classe foi criada no dia 23/03/2015.
	 *	
	 *	@author Cesar A. Gaspar <rasec.rapsag@gmail.com>
	 *	@version v 0.1
	 *	@copyright Copyright (c) 2015, Cesar A. Gaspar
	 *	@license http://opensource.org/licenses/gpl-license.php
	 *	@access public
	 *	@package Imap
	 *	@subpackage TImap
	 *	@example connect.php
	 */
	final class TImap
	{
		/**
		 *	Endereço e protocolo do servidor IMAP.
		 *	@access private
		 *	@var string
		 */
		private $server;

		/**
		 *	Usuário da conta de email.
		 *	@access private
		 *	@var string
		 */
		private $login;

		/**
		 *	Senha da conta de email.
		 *	@access private
		 *	@var string
		 */
		private $passwd;

		/**
		 *	Recurso com informações de IMAP.
		 *	@access private
		 *	@var resource
		 */
		private $imap;

		/**
		 *	Caixa de correio atual.
		 *	@access private
		 *	@var string
		 */
		private $mailbox;

		/**
		 *	Informa se o último comando teve erro.
		 *	@access private
		 *	@var bool
		 */
		private $error;

		/**
		 *	Mensagem do último erro.
		 *	@access private
		 *	@var string
		 */
		private $error_msg;

		/**
		 *	Mensagem ocorridas no protocolo IMAP.
		 *	@access private
		 *	@var array
		 */
		private $error_imap;

		/**
		 *	Construtor da classe.
		 *	Inicializa os atributos e abre a conexão com o servidor IMAP.
		 *	
		 *	@access public
		 *	@param string $server
		 *	@param string $login
		 *	@param string $passwd
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
		
		/**
		 *	Destructor da classe.
		 *	Finalizando conexão com o servidor IMAP.
		 *	
		 *	@access public
		 */
		public function __destruct()
		{
			echo 'Finalizando conexão imap...<br>';
			$this->disconnect();
		}

		/**
		 *	Método de conexão.
		 *	Faz a conexão com o servidor IMAP.
		 *	
		 *	@access private
		 *	@throws Exceção se não conseguir conectar no servidor.
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
			$check = imap_mailboxmsginfo( $this->imap );
			echo '<br>';
			echo 'Date: ' . $check->Date . '<br>';
			echo 'Driver: ' . $check->Driver . '<br>';
			echo 'Mailbox: ' . $check->Mailbox . '<br>';
			echo 'Messages: ' . $check->Nmsgs . '<br>';
			echo 'Recent: ' . $check->Recent . '<br>';
			echo 'Unread: ' . $check->Unread . '<br>';
			echo 'Deleted: ' . $check->Deleted . '<br>';
			echo 'Size: ' . $check->Size . '<br>';			
			echo '<br>';
		}

		/**
		 *	Método de desconexão.
		 *	Fecha a conexão com o servidor IMAP.
		 *	
		 *	@access private
		 */
		private function disconnect()
		{
			@imap_close( $this->imap );
		}

		/**
		 *	Método que retorna status. 
		 *	Retorna status do último comando.
		 *	
		 *	@access public
		 *	@return bool Retorna status.
		 */
		public function getStatus()
		{
			return !$this->error;
		}

		/**
		 *	Método que retorna o erro.
		 *	Retorna última mensagem de erro.
		 *	
		 *	@access public
		 *	@return string Retorna a mensagem do erro.
		 */
		public function getError()
		{
			return $this->error_msg;
		}

		/**
		 *	Método que retorna erros.
		 *	Retorna mensagens de erro gerados pelo IMAP.
		 *	
		 *	@access public
		 *	@return array Retorna mensagens de erro.
		 */
		public function getErrors()
		{
			return $this->error_imap;
		}

		/**
		 *	Método de checagem de caixa de correio.
		 *	Faz a checagem da caixa de correio atual.
		 *	
		 *	@todo Repensar a forma de utilização deste método, o atributo é mailbox.
		 *	@access private
		 *	@throws Exceção se não conseguir checar o correio.
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

		/**
		 *	Método que conta mensagens.
		 *	Retorna a quantidade de e-mails da caixa de correio atual.
		 *	
		 *	@access public
		 *	@return integer Retorna a quantidade de mensagens.
		 */
		public function getNumMsgs()
		{
			return @imap_num_msg( $this->imap );
		}

		/**
		 *	Método que conta mensagens recentes.
		 *	Retorna a quantidade de e-mails recentes da caixa de correio atual.
		 *	
		 *	@access public
		 *	@return integer Retorna quantidade de mensagens recentes.
		 */
		public function getNumRecent()
		{
			return @imap_num_recent( $this->imap );
		}

		/**
		 *	Método altera caixa de correio.
		 *	Seleciona uma caixa de correio específica.
		 *	
		 *	@access public
		 *	@param string $folder
		 */
		public function changeMailbox( $folder )
		{
			//$this->folder = $folder;
			@imap_reopen( $this->imap, $this->server . $folder, OP_READONLY );
		}
	}
?>
