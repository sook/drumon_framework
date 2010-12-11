<?php
/**
 * Drumon Framework: Build fast web applications
 * Copyright (C) 2010 Sook - Desenvolvendo inovações (http://www.sook.com.br)
 * Licensed under GNU General Public License.
 */

/**
 * Classe responsável pela verificação e processamento de downloads.
 *
 * @package class
 * @author Sook contato@sook.com.br
 */
class Download {
	/**  
	 * Lista com os tipos de arquivos permitidos.
	 *
	 * @access private
	 * @var array
	 */
	private $allowed_ext = array (
		  // archives
		  'zip' => 'application/zip',

			// documents
		  'pdf' => 'application/pdf',
		  'doc' => 'application/msword',
		  'xls' => 'application/vnd.ms-excel',
		  'ppt' => 'application/vnd.ms-powerpoint',

		  // executables
		  'exe' => 'application/octet-stream',

		  // images
		  'gif' => 'image/gif',
		  'png' => 'image/png',
		  'jpg' => 'image/jpeg',
		  'jpeg' => 'image/jpeg',

		  // audio
		  'mp3' => 'audio/mpeg',
		  'wav' => 'audio/x-wav',

		  // video
		  'mpeg' => 'video/mpeg',
		  'mpg' => 'video/mpeg',
		  'mpe' => 'video/mpeg',
		  'mov' => 'video/quicktime',
		  'avi' => 'video/x-msvideo'
		);
	
	/**  
	 * Armazena o arquivo de log dos downloads.
	 *
	 * @access private
	 * @var string
	 */
	private $logFile = "downloads.log";
	
	/**  
	 * Armazena a situação de utilização do log.
	 *
	 * @access private
	 * @var boolean
	 */
	private $hasLog = false;

	/**
	 * Verifica se arquivo existe ou estão no padrão definido na variável $allowed_ext.
	 *
	 * @access public
	 * @param string $filePath - Nome do arquivo a ser verificado.
	 * @return string - Status da situação do arquivo a ser verificado.
	 */
	public function file($filePath) {
		set_time_limit(0);
		if (!is_file($filePath)) {
			return "File does not exist. Make sure you specified correct file name.";
		}

		if ($mimeType = $this->get_mime_type($filePath) === false) {
			return "Not allowed file type.";
		}

		$this->set_headers($mimeType, basename($filePath));

		$file = @fopen($filePath, "rb");
		if ($file) {
  			while(!feof($file)) {
    			print(fread($file, 1024*8));
    			flush();
    			if (connection_status()!=0) {
      				@fclose($file);
      				die();
    			}
  			}
  			@fclose($file);
		}

		$this->log($filePath);
		die();
	}

	/**
	 * Seta os valores de cabeçalho para o arquivo.
	 *
	 * @access private
	 * @param string $fileName - Nome do arquivo.
	 * @param string $mimeType - Extensão do arquivo.
	 * @return void
	 */
	private function set_headers($mimeType, $fileName) {
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: public");
		header("Content-Description: File Transfer");
		header("Content-Type: $mimeType");
		header("Content-Disposition: attachment; filename=\" $fileName \"");
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: " . filesize($filePath));
	}

	/**
	 * Pega extensão do arquivo.
	 *
	 * @access private
	 * @param string $filePath - Diretório do arquivo.
	 * @return mixed - False se a extensão estiver incluida na lista de permitidas / String com a extensão.
	 */
	private function get_mime_type($filePath) {
		// check if allowed extension
		$fileExt = strtolower(substr(strrchr(basename($filePath),"."),1));
		if (!array_key_exists($fileExt, $this->allowed_ext)) {
		  return false;
		}
		if (empty($this->allowed_ext[$fileExt])) {
		    return mime_content_type($filePath);
		}
		return $this->allowed_ext[$fileExt];
	}

	/**
	 * Gera Log do arquivo.
	 *
	 * @access private
	 * @param string $filePath - Nome do arquivo a ser cadastrado no log.
	 * @return void
	 */
	private function log($filePath) {
		if (!$this->hasLog) return;
		$f = @fopen($this->logFile, 'a+');
		if ($f) {
		  @fputs($f, date("m.d.Y g:ia")."  ".$_SERVER['REMOTE_ADDR']."  ".$filePath."\n");
		  @fclose($f);
		}
	}
}
?>
