<?php 

/**
 * Band-List - mini app para exemplo
 *
 * ** Arquivo Principal *
 * Neste arquivo de index é feita todas as inclusões, instanciações e chamadas aos metodos dos objetos
 *
 *  Por este ser apenas um aplicativo de exemplo, feito para ser bem pequeno e simples
 * todas as incusões, tratamento das requisições, instanciações das classes e exibições HTML são feitas neste unico arquivo
 */

// apelido para o separador de diretório padrão do sistema
if (!defined('DS'))
	define('DS', DIRECTORY_SEPARATOR, true);

/* inclui as classes manualmente, o que poderia ser feito por uma função 'autoload' */
require_once 'BandList'. DS .'MainClass.php';
require_once 'BandList'. DS .'DataClass.php';
require_once 'psyDuck.php';


// instancia a classe principal para o gerenciamanto dos dados das bandas
$BandList = new BandList\MainClass();


// se recebeu algum dado pelo post
if (isset($_POST['band'])) {
	// se tem id, trata como uma edição
	// se não, trata como um cadastro
	if (isset($_POST['band']['id'])) {
		$BandList->update( $_POST['band'] );
	} else {
		$BandList->insert( $_POST['band'] );
	}
}

// se recebeu um id para edição, carrega uma variavel com os dados, 
// se não, prepara uma variavel vazia para evitar erros
if (isset($_GET['edit'])) {
	$editBand = $BandList->setBand( $_GET['edit'] )->band();
} else {
	$editBand = [];
}

// se recebeu requisição para deletar
//  e faz um redirecionamento em javascript para liberar a url
if (isset($_GET['delete'])) {
	$BandList->delete( $_GET['delete'] );
	echo " <", "script type=\"text/javascript\"> ",
		" alert('Requisição para deletar recebida'); ",
		" window.location.href = '?'; ",
		" </script> ";
	die();
}


###############################################################################
###################### A partir daqui começa a exibição HTML ##################

?><!DOCTYPE html>
<html>
<head>
	<title> Band-List | Mini aplicativo de exemplo </title>
	<!-- Carrega o bootstrap externamente -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
	<script type="text/javascript" src="script.js" ></script>
	<style type="text/css">
		.container { max-width: 950px; border: 2px solid #eee; } hr {max-width: 70% }
		table, td, th { text-align: center; border: 1px solid #AFAFAF !important; } input[type=text] { width: 100%; }
	</style>
</head>
<body>

	<div class="container">
		<header>
			<h2> Band-List | Mini aplicativo de exemplo </h2>
			<a class="btn btn-xs btn-info" style="float:left; margin-left: 4rem" href="../" > Return to apps list </a>
		</header>
		<button onclick="toggleElement('treeBands')" style="display:block;margin:auto;" >Mostrar arvore</button>
		<pre id="treeBands" style="display:none" >
			<?php
				print ("\n\tTodos os dados de todas as bandas registradas.\n");
				/* Exibe a arvore de elementos dentro de cada array retornado pela função geradora  */
				foreach ( $BandList->bands() as $band ):
					print ( PHP_EOL . print_r( $band, true) );
				endforeach;
			?>
		</pre>
		<br><br><hr>


		<div class="col-lg-12">
			<table class="table table-bordered" >
				<tr>
					<th > Banda </th><th> Álbuns </th><th> Músicas </th>
				</tr>
	<?php 
			/* Inicia e exibição dos dados retornados pela função geradora */
			foreach ( $BandList->bands() as $band ):
	?>
		<tr>
			<td rowspan="<?=($band['count_music'] + $band['count_albums']+1)?>" >
				<button onclick="rem_band('<?=$band['id']?>')" style="float:right" >Excluir</button>
				<button onclick="edit_band('<?=$band['id']?>')" style="float:right" >Editar</button>
					<br><br>
				<h3><?=$band['name']?></h3> <!-- Nome -->
					<hr>
				<table class=" table-bordered " > <!-- Tabela de integrantes -->
					<tr><th colspan="2">Integrantes</th></tr>
				<?php foreach ($band['members'] as $member ): ?>
					<tr><td> &nbsp; <?=$member['name']?> &nbsp; </td><td> &nbsp; <?=$member['instrument']?> &nbsp; </td></tr>
				<?php endforeach; ?>
				</table>
			</td>
		</tr>
	<?php 	
				/* Lista os albuns */
				foreach ( $band['albums'] as $album):
	?>
			<tr>
				<td rowspan="<?=$album['count_music']+1?>" >
					<br>
					<h4><?php print_r($album['name']) ?></h4>
				</td>
			</tr>
	<?php 
					/* Lista as musicas de cada album */
					foreach ( $album['musics'] as $music):
	?>
				<tr>
					<td style="margin:0;padding:0" >
						<small style="margin:0;padding:0" ><?php print_r($music['name']) ?></small>
					</td>
				</tr>
	<?php 
					endforeach;
				endforeach;
	?>
				<tr><td colspan="3" style="margin:0;padding:0" > &nbsp; </td></tr>
	<?php 
			endforeach;
	?>
			</table>
		</div>


<!-- 
		#################################################################################
		########## A partir daqui começam os formulários de cadastro e edição de bandas
		#################################################################################
-->

		<div class="col-lg-12">
			<div class="jumbotron">
		<?php
				/** Se não foi carregado nenhum dado, trata como um novo cadastro */
				if ( ! $editBand ):
		?>
			<button onclick="toggleElement('cadBandForm')" class="btn btn-primary" style="display:block;margin:auto" >Cadastrar Banda</button><hr><br>
			<!-- Formulario de cadastro de bandas -->
			<form method="post" style="display:none" id="cadBandForm" >
				<table class="table table-bordered" id="cadtable_bands" >
					<tr>
						<td >
							<label> Nome da banda <input type="text" name="band[name]" > </label>
						</td >
						<td >
							<table class="table table-bordered" id="cadtable_members">
								<tr><th colspan="3">Integrantes</th></tr>
								<tr id="cadfield_members" style="display:none" >
									<td><input type="text" name="band[members][tr_id][name]"></td><td><input type="text" name="band[members][tr_id][instrument]"></td><td><input type="button" value="X" onclick="rem_Field('tr_id')" ></td>
								</tr>
								<tr><td colspan="3"><input type="button" value="Adicionar integrante" onclick="add_membersField()" ></td></tr>
							</table>
							<script type="text/javascript"> add_membersField(); /* adiciona o primeiro campo de integrantes para acertar o layout */ </script>
						</td >
					</tr>
					<tr id="cadfield_albums" style="display:none" >
						<td>
							<label> Nome da álbum <input type="text" name="band[albums][tr_id][name]" > </label>
							<br><input type="button" value="Remover álbum" onclick="rem_Field('tr_id')" >
						</td>
						<td>
							<table class="table table-bordered" id="mustb_tr_id">
								<tr><th colspan="2">Músicas</th></tr>
								<tr id="tr_tr_id" style="display:none" >
									<td><input type="text" name="band[albums][tr_id][musics][]"></td>
									<td><input type="button" value="X" onclick="rem_Field('tr_blank_space_id')" ></td>
								</tr>
								<tr><td colspan="2"><input type="button" value="Adicionar música" onclick="add_musicsField('tr_id')" ></td></tr>
							</table>
						</td>
					</tr>
					<tr><td colspan="2"><input type="button" value="Adicionar Álbum" onclick="add_albumsField()" ></td></tr>
				</table>
				<input type="submit" value="Cadastrar" >
			</form>

<?php 
		/** se a variavel de edição contem dados, se suposto que sejam para edição, então prepara o formulario */
		else :
?>
			<!-- Formulario de edição de banda -->
			<h2>Editar Banda</h2>
			<button onclick="window.location.href='?'" class="btn btn-sm btn-danger" style="float:right;margin-top:-2rem" >Cancelar</button>
			<a  name="_edit_band" > &nbsp; </a>
			<!-- Formulário de edição -->
			<form method="post" id="cadBandForm" >
				<input type="hidden" name="band[id]" value="<?=$editBand['id']?>" >
				<table class="table table-bordered" id="cadtable_bands" >
					<tr>
						<td >
							<label> Nome da banda <input type="text" name="band[name]" value="<?=$editBand['name']?>" > </label>
						</td >
						<td >
							<table class=" table-bordered" id="cadtable_members">
								<tr><th colspan="3">Integrantes</th></tr>
								<tr id="cadfield_members" style="display:none" >
									<td><input type="text" name="band[members][tr_id][name]"></td><td><input type="text" name="band[members][tr_id][instrument]"></td><td><input type="button" value="X" onclick="rem_Field('tr_id')" ></td>
								</tr>
						<?php 
							foreach ($editBand['members'] as $editMembers):
								$editMembers['id'] = uniqid();
						?>
							<tr id="<?=$editMembers['id']?>"  >
								<td><input type="text" name="band[members][<?=$editMembers['id']?>][name]" value="<?=$editMembers['name']?>" ></td><td><input type="text" name="band[members][<?=$editMembers['id']?>][instrument]" value="<?=$editMembers['instrument']?>" ></td><td><input type="button" value="X" onclick="rem_Field('<?=$editMembers['id']?>')" ></td>
							</tr>
						<?php 
							endforeach;
						?>
								<tr><td colspan="3"><input type="button" value="Adicionar integrante" onclick="add_membersField()" ></td></tr>
							</table>
						
						</td >
					</tr>
					<tr id="cadfield_albums" style="display:none" >
						<td>
							<label> Nome da álbum <input type="text" name="band[albums][tr_id][name]" > </label>
							<br><input type="button" value="Remover álbum" onclick="rem_Field('tr_id')" >
						</td>
						<td>
							<table class="table table-bordered" id="mustb_tr_id">
								<tr><th colspan="2">Músicas</th></tr>
								<tr id="tr_tr_id" style="display:none" >
									<td><input type="text" name="band[albums][tr_id][musics][]"></td>
									<td><input type="button" value="X" onclick="rem_Field('tr_blank_space_id')" ></td>
								</tr>
								<tr><td colspan="2"><input type="button" value="Adicionar música" onclick="add_musicsField('tr_id')" ></td></tr>
							</table>
						</td>
					</tr>
			<?php 
				foreach ($editBand['albums'] as $editAlbums):
			?>
				<tr id="<?=$editAlbums['id']?>" >
					<td>
						<label> Nome da álbum <input type="text" name="band[albums][<?=$editAlbums['id']?>][name]" value="<?=$editAlbums['name']?>" > </label>
						<br><input type="button" value="Remover álbum" onclick="rem_Field('<?=$editAlbums['id']?>')" >
					</td>
					<td>
						<table class="table table-bordered" id="mustb_<?=$editAlbums['id']?>">
							<tr><th colspan="2">Músicas</th></tr>
							<tr id="tr_<?=$editAlbums['id']?>" style="display:none" >
								<td><input type="text" name="band[albums][<?=$editAlbums['id']?>][musics][]"></td>
								<td><input type="button" value="X" onclick="rem_Field('tr_blank_space_id')" ></td>
							</tr>
					<?php 
						foreach ($editAlbums['musics'] as $editMusics):
							$editMusics['id'] = uniqid();
					?>
						<tr id="tr_tr_<?=$editMusics['id']?>" >
							<td><input type="text" name="band[albums][<?=$editAlbums['id']?>][musics][]" value="<?=$editMusics['name']?>" ></td>
							<td><input type="button" value="X" onclick="rem_Field('tr_tr_<?=$editMusics['id']?>')" ></td>
						</tr>
					<?php 
						endforeach;
					?>
							<tr><td colspan="2"><input type="button" value="Adicionar música" onclick="add_musicsField('<?=$editAlbums['id']?>')" ></td></tr>
						</table>
					</td>
				</tr>
			<?php 
				endforeach;
			?>
					<tr><td colspan="2"><input type="button" value="Adicionar Álbum" onclick="add_albumsField()" ></td></tr>
				</table>
				<input type="submit" value="Enviar Alterações"  >
			</form>
<?php 
		endif;
?>
			</div> <!-- Fim da div.jumbotron com os formulários -->
		</div>

	</div> <!-- /container -->
<br><br><br><hr><hr><hr><hr><hr><hr><hr><hr><hr>
</body>
<script type="text/javascript">
	// pequeno script para evitar que as variaveis do POST fiquem armazenadas e cadastrando dados duplicados durante os recarregamentos
	//     (está comentada para não ser lida pelo Linter javascript do Sublime Text)
	//<?php if (isset($_POST['band'])) print ( "\n window.location.href = '?'; \n" ); ?>	
	if(window.location.href.indexOf('/?') === -1)
		window.location.href = window.location.href + '/?';
</script>
</html>