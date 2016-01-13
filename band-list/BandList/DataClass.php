<?php 

namespace BandList;

/**
* Classe responsavel por registrar e retornar os dados das bandas
*  Todos os metodos devem ser acessados da forma estática.
*  
*  ** Esta implementação está usando a classe psyDuck para armazenar os dados em formato JSON
*  **  mas pode facilmente ser portada para um banco SQL
*/
class DataClass
{

	/**
	 * armazena a instancia da classe psyDuck
	 * @var obj
	 */
	private static $psy; 

	/**
	 * Prepara o banco
	 * 	 neste caso, instancia e define o diretório de armazenamento para psyDuck
	 */
	public static function create ( )
	{
		self::$psy = new \psyDuck( 'DATA' );
	}

	/**
	 * Retorna um array com o id de todas as bandas cadastradas
	 * @return array
	 */
	public static function getBands()
	{
		self::prepare('bands');
		$all_ids = self::$psy->node(function($data){
				return $data['id'];
			});
		return $all_ids;
	}

	/**
	 * Retorna todos os dados registrados de uma banda pelo id
	 * @param  string id id da banda
	 * @return array dados da banda
	 */
	public static function getBand( $id )
	{
		self::prepare('bands');
		return self::getdata( $id );
	}

	/**
	 * Retorna todos os dados registrados de um certo album pelo Id
	 * @param  string $bandId id do album
	 * @return array
	 */
	public static function getBandAlbums( $bandId )
	{
		self::prepare('albums');
		$albums = self::$psy->node(function ($data) use ($bandId) {
				if ( $data['band'] == $bandId )
					return $data;
			});
		return $albums;
	}

	/**
	 * Retorna todas as musicas de um certo album pelo Id 
	 * @param  string $albumId 
	 * @return array
	 */
	public function getAlbumMusics( $albumId )
	{
		self::prepare('musics');
		$musics = self::$psy->node(function ($data) use ($albumId) {
				if ( $data['album'] == $albumId )
					return $data;
			});
		return $musics;	
	}

	/**
	 * Retorna a contagem de musicas de uma banda
	 * @param  string $bandId 
	 * @return int
	 */
	public function countBandMusics( $bandId )
	{
		self::prepare('musics');
		$musics = self::$psy->node(function ($data) use ($bandId) {
				if ( $data['band'] == $bandId )
					return $data;
			});
		return count($musics);
	}

	/**
	 * Retorna a contagem de albuns de uma banda
	 * @param  string $bandId 
	 * @return integer
	 */
	public function countBandAlbums( $bandId )
	{
		self::prepare('albums');
		$musics = self::$psy->node(function ($data) use ($bandId) {
				if ( $data['band'] == $bandId )
					return $data;
			});
		return count($musics);
	}

	/**
	 * Retorna a contagem de musicas de um album
	 * @param  string $albumId 
	 * @return integer
	 */
	public function countAlbumMusics( $albumId )
	{
		self::prepare('musics');
		$musics = self::$psy->node(function ($data) use ($albumId) {
				if ( $data['album'] == $albumId )
					return $data;
			});
		return count($musics);
	}

	/**
	 * Cadastra uma nova banda
	 * @param  array $data array com todos os dados (albuns e suas musicas)
	 */
	public static function cadBand ( $data )
	{
		self::prepare('bands');
		$newId = uniqid(); // id para registro
		$name = $data['name'];
		$members = [];
		foreach ( $data['members'] as $key => $value) {
			if ( $value['name'] ) $members[] = $value;
		}

		// insere os dados principais
		self::insert( array(
				'id' => $newId,
				'name' => $name,
				'members' => $members
			) );
		// registra os albuns e suas musicas
		self::cadAlbums( $data['albums'], $newId );
	}

	/**
	 * Registra os albuns de uma banda
	 * 	(é suposto que seja chamado apenas dentro do metodo cadBand)
	 * @param  array $albums array com todos os albuns e suas musicas
	 * @param  string $bandId id da banda
	 */
	private static function cadAlbums( $albums, $bandId )
	{
		$counter = -1; // evita que o primeiro indice (geralmente vazio ou com alguma coisa estranha) seja registrado
		foreach ($albums as $key => $album ) {
			$counter++;	if ($counter==0) continue;
			$albumId = uniqid();
			self::prepare('albums');
			self::insert( array(
					'id' => $albumId,
					'band' => $bandId,
					'name' => $album['name']
				) );
			// prepara para gravar as musicas do album
			self::cadMusics( $album['musics'], $bandId, $albumId );
		}
	}

	/**
	 * Registra as musicas de um album
	 * 	(é esperado que seja chamado apenas dentro do metodo cadAlbum)
	 * @param  array $musics  musicas do album
	 * @param  string $bandId  
	 * @param  string $albumId 
	 */
	private static function cadMusics ( $musics, $bandId, $albumId )
	{
		$counter = -1;
		foreach ($musics as $music ) {
			$counter++;	if ($counter==0) continue;
			self::prepare('musics');
			self::insert( array(
					'album' => $albumId,
					'band' => $bandId,
					'name' => $music
				) );
		}
	}

	/**
	 * Deleta o registro de uma banda pelo Id
	 * @param  string $bandId 
	 */
	public static function delBand ( $bandId )
	{
		self::prepare('bands');
		return self::$psy->delete(function ($data) use ($bandId) {
				if ($data['id'] == $bandId)
					return true;
			});
	}

	/**
	 * Deleta os registros de albuns de uma banda pelo Id
	 * @param  string $bandId 
	 */
	public static function delAlbums ( $bandId )
	{
		self::prepare('albums');
		return self::$psy->delete(function ($data) use ($bandId) {
				if ($data['band'] == $bandId)
					return true;
			});
	}
	
	/**
	 * Deleta os registros de musicas de uma banda pelo Id
	 * @param  string $bandId 
	 */
	public static function delMusics ( $bandId )
	{
		self::prepare('musics');
		return self::$psy->delete(function ($data) use ($bandId) {
				if ($data['band'] == $bandId)
					return true;
			});
	}

	/**
	 * seta a tabela de registro, que no caso é um arquivo JSON para a classe psyDuck
	 * @param  string $tb
	 */
	private static function prepare ( $tb )
	{
		self::$psy->in($tb);
	}

	/**
	 * Retorna todos os dados de um registro pelo id
	 * @param  string $id  id do registro
	 * @return array
	 */
	private static function getdata ( $id )
	{
		return self::$psy->get(function ($data) use ($id){
				if( $data['id'] == $id )
					return $data;
			});
	}

	/**
	 * Cadastra u novo registro
	 * @param  array $data
	 * @return array
	 */
	private static function insert ( $data )
	{
		return self::$psy->insert( $data );
	}

}

