<?php
	/**
	 *	Classe para gerenciar recursos de correio eletrônico por meio do protocolo Imap.
	 *	Criado no dia 23/03/2015.
	 *	
	 *	@author Cesar A. Gaspar <rasec.rapsag@gmail.com>
	 *	@version v 0.1
	 *	@copyright Copyright (c) 2015, Cesar A. Gaspar
	 *	@license GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
	 *	@see https://github.com/RasecRapsag/php-mail
	 *	@access public
	 *	@package Mail
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
		private $headers = array();

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
		 *	Método que cria uma pasta no servidor de email.
		 *	
		 *	@access public
		 *	@param string $name
		 *	@return bool Retorna verdadeiro ou falso para criação da pasta.
		 */
		public function createFolder( $name )
		{
			if( !$this->error )
			{
				try
				{
					$result = @imap_createmailbox( $this->imap, imap_utf7_encode( $this->server . $name ) );
					if( !$result )
					{
						$error = @imap_errors();
						if( substr_count( strtoupper( $error[0] ), 'ALREADYEXISTS' ) )
							throw new Exception( 'Não foi possível criar a pasta. A pasta já existe.', 202 );
						else
							throw new Exception( 'Não foi possível criar a pasta.', 297 );
					}
					else
						return true;
				}
				catch( Exception $e )
				{
					$this->error = true;
					$this->error_msg = $e->getCode() . ' - ' . $e->getMessage();
				}
			}
			return false;
		}

		/**
		 *	Método que remove uma pasta no servidor de email.
		 *	
		 *	@access public
		 *	@param string $name
		 *	@return bool Retorna verdadeiro ou falso para exclusão da pasta.
		 */
		public function removeFolder( $name )
		{
			if( !$this->error )
			{
				try
				{
					$result = @imap_deletemailbox( $this->imap, imap_utf7_encode( $this->server . $name ) );
					if( !$result )
					{
						$error = @imap_errors();
						if( substr_count( strtoupper( $error[0] ), 'NONEXISTENT' ) )
							throw new Exception( 'Não foi possível remover a pasta. A pasta não existe.', 203 );
						else
							throw new Exception( 'Não foi possível remover as pasta.', 298 );
					}
					else
						return true;
				}
				catch( Exception $e )
				{
					$this->error = true;
					$this->error_msg = $e->getCode() . ' -'  . $e->getMessage();
				}
			}
			return false;
		}

		/**
		 *	Método que renomeia uma pasta no servidor de email.
		 *	
		 *	@access public
		 *	@param string $old_name
		 *	@param string $new_name
		 *	@return bool Retorna verdadeiro ou falso se renomeou a pasta.
		 */
		public function renameFolder( $old_name, $new_name )
		{
			if( !$this->error )
			{
				try
				{
					$result = @imap_renamemailbox( $this->imap, $this->server . $old_name, $this->server . $new_name );
					if( !$result )
					{
						$error = @imap_errors();
						if( substr_count( strtoupper( $error[0] ), 'UNKNOWN' ) )
							throw new Exception( 'Não foi possível renomear a pasta. A pasta não foi encontrada.', 204 );
						elseif ( substr_count( strtoupper( $error[0] ), 'ALREADYEXISTS' ) )
							throw new Exception( 'Não foi possível renomear a pasta. Nome duplicado.', 205 );
						else
							throw new Exception( 'Não foi possível renomear a pasta.', 299 );
					}
					else
						return true;
				}
				catch( Exception $e )
				{
					$this->error = true;
					$this->error_msg = $e->getCode() . ' - ' . $e->getMessage();
				}
			}
			return false;
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

		/**
		 *	Método que consulta os cabeçalhos dos emails.
		 *	
		 *	@access private
		 *	@param integer $quantity
		 *	@return void
		 */
		private function getHeaders( $quantity )
		{
			try
			{
				if( !$this->mailbox )
					throw new Exception( 'Nenhuma caixa de correio está selecionada.', 301 );
				else
				{
					if( !$quantity )
						$headers = imap_fetch_overview( $this->imap, "1:{$this->getNumMessages()}", FT_UID );
					else
						$headers = imap_fetch_overview( $this->imap, "1:{$quantity}", FT_UID );

					foreach( $headers as $header => $value)
					{
						$email['subject'] = $value->subject;
						$email['from'] = $value->from;
						$email['to'] = $value->to;
						$email['date'] = $value->date;
						$email['answered'] = $value->answered;
						$email['seen'] = $value->seen;
						$emails[$value->uid] = ( object ) $email;
					}
					$this->headers = $emails;
				}					
			}
			catch( Exception $e )
			{
				$this->error = true;
				$this->error_msg = $e->getCode() . ' - ' . $e->getMessage();
			}
			$this->mailbox = null;
		}

		public function getMessages( $quantity = 50, $mailbox = null )
		{
			// $headers = imap_fetch_overview( $this->imap, "1:{$this->getNumMessages()}", FT_UID );
			// $headers = imap_fetch_overview( $this->imap, 2, FT_UID );

			// foreach( $headers as $header => $value)
			// {
			// 	$aux['subject'] = $value->subject;
			// 	$aux['from'] = $value->from;
			// 	$aux['to'] = $value->to;
			// 	$aux['date'] = $value->date;
			// 	$aux['answered'] = $value->answered;
			// 	$aux['seen'] = $value->seen;
			// 	$emails[$value->uid] = ( object ) $aux;
			// }

			$this->getHeaders( $quantity );
			$this->log( $this->headers );
			return $this->headers;
		}
		/**
		 *
		 *
		 *
		 */
		public function searchMessages( $string = 'ALL')
		{
			$emails = imap_search( $this->imap, $string, SE_UID );
			$this->log( $emails );
		}

		public function log( $var )
		{
			echo '<pre>';
			print_r( $var );
			echo '</pre>';
		}
	}
?>
