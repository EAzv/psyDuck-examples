
		Band-List - Pequeno aplicativo de exemplo
	funciona como um registro de bandas álbuns e musicas e seus integrantes.
		
	A intenção deste aplicativo é além de executar as tarefas da prova,
	 mas támbem mostrar o uso de alguns recursos novos do php como geradores e espaço de nomes.

	Como classe principal, decidi usar uma estrutura de namespaces com duas classes, uma de controle e outra para acesso aos dados armazenados.
	
	Para armazenar os dados usei a biblioteca psyDuck (github.com/EduhAzvdo/psyDuck) que usa arquivos JSON, mas a aplicação pode facilmente ser portada para qualquer outro sistema de banco de dados alterando a classe de acesso aos dados.
	
	Os 3 niveis da matriz podem ser vistos como "BANDA" > "ÁLBUM" > "MUSICAS".

	Para rodar a aplicação é necessário usar php 5.6 ou superior, pois utiliza recursos não suportados por versões antigas.
	
	Também é muito importante que a pasta DATA e seus arquivos tenham permissão de leitura e escrita no sistema.
	
	Para desenvolver utilizei o servidor interno do php, pelo comando "php -S localhost:8000" no diretório da aplicação,
	 mas também testei com apache (pode ser necessário verificar as permissões de escrita na pasta DATA durante a troca de servidores).
	
	Testei a aplicação nos navegadores Chrome e Firefox, como estou usando linux não testei no IE (mas provavelmente não funcionaria).
