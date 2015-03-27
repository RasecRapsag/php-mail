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
		 *	Recurso de stream com informações de IMAP.
		 *	@access private
		 *	@var resource
		 */
		private $imap;

		/**
		 *	Pastas de correio eletronico.
		 *	@access private
		 *	@var array
		 */
		private $folders = array();

		/**
		 *	Caixa de correio atual.
		 *	@access private
		 *	@var string
		 */
		private $mailbox = null;

		/**
		 *	Informa se o último comando teve erro.
		 *	@access private
		 *	@var bool
		 */
		private $error = false;

		/**
		 *	Mensagem do último erro.
		 *	@access private
		 *	@var string
		 */
		private $error_msg = null;

		/**
		 *	Construtor da classe.
		 *	Inicializa os atributos e abre a conexão com o servidor IMAP.
		 *	
		 *	@access public
		 *	@param string $server
		 *	@param string $login
		 *	@param string $passwd
		 *	@return void
		 */
		public function __construct( $server = 'localhost', $login = null, $passwd = null )
		{
			echo 'Iniciando conexão imap...<br>';
			$this->server = $server;
			$this->login = $login;
			$this->passwd = $passwd;
			$this->connect();
			$this->mailboxes();
		}
		
		/**
		 *	Destructor da classe.
		 *	Finalizando conexão com o servidor IMAP.
		 *	
		 *	@access public
		 *	@return void
		 */
		public function __destruct()
		{
			echo 'Finalizando conexão imap...<br>';
			$this->disconnect();
		}

		/**
		 *	Método que faz a conexão com o servidor IMAP.
		 *	
		 *	@access private
		 *	@throws Exceção se não conseguir conectar no servidor.
		 *	@return void
		 */
		private function connect()
		{
			try
			{
				$this->imap = @imap_open( $this->server, $this->login, $this->passwd, OP_HALFOPEN );
				if( !$this->imap )
					throw new Exception( 'Não foi possível estabelecer conexão com o servidor IMAP.', 101 );
			}
			catch( Exception $e )
			{
				$this->error = true;
				$this->error_msg = $e->getCode() . ' - ' . $e->getMessage();
				@imap_errors();
			}
		}

		/**
		 *	Método que fecha a conexão com o servidor IMAP.
		 *	
		 *	@access private
		 *	@return void
		 */
		private function disconnect()
		{
			@imap_close( $this->imap );
		}

		/**
		 *	Método que busca as pastas do servidor de email.
		 *	
		 *	@access private
		 *	@return void
		 */
		private function mailboxes()
		{
			if( !$this->error )
			{
				try
				{
					$folders = @imap_list( $this->imap, $this->server, '*' );
					if( !$folders )
						throw new Exception( 'Não foi possível resgatar as pastas do servidor.', 200 );
					else
						foreach( $folders as $folder )
							array_push( $this->folders, str_replace( $this->server, '', imap_utf7_decode( $folder ) ) );
				}
				catch( Exception $e )
				{
					$this->error = true;
					$this->error_msg = $e->getCode() . ' - ' . $e->getMessage();
				}
			}
		}

		/**
		 *	Método que seleciona uma caixa de correio específica.
		 *	
		 *	@access public
		 *	@param string $folder
		 *	@return string|void Retorna o nome da pasta ou não retorna nada.
		 */
		public function selectMailbox( $folder )
		{
			try
			{
				$resource = @imap_reopen( $this->imap, $this->server . $folder, OP_READONLY );
				if( !$resource )
					throw new Exception( 'Não foi possível selecionar a pasta.', 201 );
				else
					return $this->mailbox = $folder;
			}
			catch( Exception $e )
			{
				$this->error = true;
				$this->error_msg = $e->getCode() . ' - ' . $e->getMessage();
			}
		}

		/**
		 *	Método que retorna o status do último comando.
		 *	
		 *	@access public
		 *	@return bool Retorna status.
		 */
		public function getStatus()
		{
			return !$this->error;
		}

		/**
		 *	Método que retorna a última mensagem de erro.
		 *	
		 *	@access public
		 *	@return string Retorna a mensagem do erro.
		 */
		public function getError()
		{
			return $this->error_msg;
		}

		/**
		 *	Método que retorna as pastas existentes no servidor de emaul.
		 *
		 *	@access private
		 *	@return array Retorna as pastas do email.
		 */
		public function getFolders()
		{
			return $this->folders;
		}

		/**
		 *	Método que retorna a pasta atual selecionada.
		 *	
		 *	@access public
		 *	@return string Retorna a pasta atual.
		 */
		public function getFolder()
		{
			return $this->mailbox;
		}

		/**
		 *	Método que retorna a quantidade de pastas no servidor de email.
		 *	
		 *	@access public
		 *	@return integer Retorna a quantidade de pastas.
		 */
		public function getNumFolders()
		{
			return count( $this->folders );
		}

		/**
		 *	Método que faz a checagem da caixa de correio atual.
		 *	
		 *	@todo Repensar a forma de utilização deste método, o atributo é mailbox.
		 *	@access private
		 *	@throws Exceção se não conseguir checar o correio.
		 *	@return void
		 */
		private function check()
		{
			try
			{
				$this->mailbox = @imap_check( $this->imap );
				if( !$this->mailbox )
					throw new Exception( 'Não foi possível checar caixa de correio atual.', 300 );
			}
			catch( Exception $e )
			{
				$this->error = true;
				$this->error_msg = $e->getMessage();
			}
			echo '<pre>';
			print_r( $this->mailbox );
			echo '</pre>';
		}

		/**
		 *	Método que retorna a quantidade de emails da caixa de correio atual.
		 *	
		 *	@access public
		 *	@return integer|null Retorna a quantidade de mensagens ou vazio.
		 */
		public function getNumMessages()
		{
			if( !$this->error )
				return @imap_num_msg( $this->imap );
			return null;
		}

		/**
		 *	Método que retorna a quantidade de emails da caixa de correio atual que não foram lidos.
		 *	
		 *	@access public
		 *	@return integer|null Retorna a quantidade de mensagens não lidas ou vazio.
		 */
		public function getNumUnreadMessages()
		{
			if( !$this->error )
			{
				$unread = @imap_search( $this->imap, 'UNSEEN', SE_UID );
				if( !$unread )
					return 0;
				return count( $unread );
			}
			return null;
		}
	}
?>
