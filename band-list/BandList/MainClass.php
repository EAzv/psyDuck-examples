<?php 

namespace BandList;

/**
*  Classe principal,
*  		responsavel por receber, manipular e retornar os dados
* essa seria uma classe apropriada para por validações, ligações, conexões, mensagens de erro entre outras operações do tipo
*
* o principal metodo desta classe é a função "bands()"
* 	que funciona como um gerador que para cada laço retorna um array com todos dados da banda corrente,
* 	na ordem Banda > Álbuns > Músicas
* 
*/
class MainClass
{
	
	// armazena os ids correntes dos elementos da arvore durante a listagem
	private $_curr_band; // id da banda sendo manipulada
	private $_curr_album; // id de um album da banda sendo manipulada
	private $_curr_music; // id de uma musica da banda sendo manipulada

	function __construct()
	{
		// prepara a classe estática responsavel pelo armazenamento dos dados
		// e carrega uma váriavel com os ids de todas as bandas registradas
		DataClass::create();
		$this->bands = DataClass::getBands();
	}

	/**
	 * Define uma banda pelo id para ter seus dados manipulados
	 * @param string $id id da banda para ser trabalhada pela classe
	 * @return obj
	 */
	public function setBand ( $id )
	{
		$this->_curr_band = $id;
		return $this; // para acessar os metodos mais rapidamente
	}

	/**
	 * Retorna um gerador com os dados das bandas
	 * ao atravessar por todos os ids das bandas já armazenados, os dados carregados e retornados dinamicamente,
	 *  sem a nescessidade de se armazenar todos na memoria
	 *  
	 * @return generator
	 */
	public function bands ()
	{
		foreach ($this->bands as $bd_id)
		{
			yield $this->setBand( $bd_id )->band( );
		}
		$this->setBand( null );
	}

	/**
	 * Retona todos os dados da banda corrente
	 * carregando o array com os albuns e suas musicas e suas contagens
	 * @return array dados da banda
	 */
	public function band (  )
	{
		$band = DataClass::getBand( $this->_curr_band );
		$band['albums'] = $this->albums( );
		$band['count_music'] = DataClass::countBandMusics( $this->_curr_band );
		$band['count_albums'] = DataClass::countBandAlbums( $this->_curr_band );
		return ( $band );
	}
	
	/**
	 * Retorna os albuns com suas musicas da banda corrente
	 * @return array
	 */
	public function albums ( )
	{
		$albums = [];
		foreach ( DataClass::getBandAlbums( $this->_curr_band ) as $album ) {
			$this->_curr_album = $album['id'];
			$album['musics'] = $this->musics();
			$album['count_music'] = DataClass::countAlbumMusics( $this->_curr_album );
			$albums[] = $album;
		}
		$this->_curr_album = null;
		return $albums;
	}

	/**
	 * Retorna as musicas do album corrente
	 * @return array
	 */
	public function musics ()
	{
		return DataClass::getAlbumMusics( $this->_curr_album );
	}

	/**
	 * Registra os dados de uma banda
	 * @param  array lista com os dados completos (albuns e musicas)
	 * @todo este seria um bom lugar para começar a fazer validações
	 */
	public function insert ($data)
	{
		DataClass::cadBand( $data );
	}

	/**
	 * Atualiza os dados de uma banda
	 *  de forma bem grosseira exclui e cadastra tudo de novo, poderia ser melhor
	 * @param  array lista com os dados completos (albuns e musicas)
	 */
	public function update ($data)
	{
		$this->delete( $data['id'] );
		DataClass::cadBand( $data );
	}

	/**
	 * Deleta todos os dados de uma banda
	 * @param  string id da banda para ser deletada
	 */
	public function delete ( $bandId )
	{
		DataClass::delBand ( $bandId );
		DataClass::delAlbums ( $bandId );
		DataClass::delMusics ( $bandId );
	}
}