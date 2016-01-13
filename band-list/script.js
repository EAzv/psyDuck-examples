/* 
 * ***********************************************************************
 * ***********************************************************************
 * *****  Arquivo com as funções javascript do mini-app Band-List  *******
 * ***********************************************************************
 */

/**
 * Adiciona um novo campo para o formulário de campos de membros
 */
function add_membersField () {
	var cadfield_members = document.getElementById('cadfield_members');
	var cadtable_members = document.getElementById('cadtable_members');
	var newRow = cadtable_members.insertRow( cadtable_members.rows.length-1 );
	var tr_id = uniqid();
	newRow.id = tr_id;
	newRow.innerHTML = cadfield_members.innerHTML.replace(/tr_id/g, tr_id);
}

/**
 * Adiciona novos campos para registro de albuns
 */
function add_albumsField () {
	var cadfield_albums = document.getElementById('cadfield_albums');
	var cadtable_bands = document.getElementById('cadtable_bands');
	var newRow = cadtable_bands.insertRow( cadtable_bands.rows.length-1 );
	var tr_id = uniqid();
	newRow.id = tr_id;
	newRow.innerHTML = cadfield_albums.innerHTML.replace(/tr_id/g, tr_id);
}

/**
 * Adiciona um novo campo para musicas
 */
function add_musicsField (tr) {
	var cadfield = document.getElementById( 'tr_' + tr );
	var cadtable = document.getElementById( 'mustb_' + tr );
	var newRow = cadtable.insertRow( cadtable.rows.length-1 );
	var tr_id = uniqid();
	newRow.id = tr_id;
	newRow.innerHTML = cadfield.innerHTML.replace(/blank_space_/g, '').replace(/tr_id/g, tr_id);
}

/**
 * Remove um elemento DOM HTML pelo ID
 */
function rem_Field (elm) {
	var tr_elm = document.getElementById( elm );
	tr_elm.parentNode.removeChild(tr_elm);
}

/**
 * Solicita a exclusão de uma banda
 */
function rem_band (id) {
	if (confirm('Deseja realmente excluir essa banda?')) {
		window.location.href = '?delete='+ id;
	} else {
		return false;
	}
}

/**
 * Solicita a edição de uma banda
 */
function edit_band (id) {
	window.location.href = '?edit='+ id + '#_edit_band';
}


/** Função para gerar ids unicos */
function uniqid(){
	return ((new Date()).getTime()*Math.random()).toString(36).replace('.','');
}

/**
 * Exibe/Oculta elementos
 */
function toggleElement (id) {
	elm = document.getElementById(id);
	elm.style.display = elm.style.display=='block'?'none':'block';
}
