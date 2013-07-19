Componente Acesso

Testado em:
CakePHP 2.3.5 até 2.3.8
PHP 5.4.6
Provavelmente funciona em PHP 5.3 ou superior e Cake 2.x.

Este componente tem como objetivo controlar o acesso dos usuários a cada um dos actions.
Ele não usa ACL, mas trabalha de forma parecida, pelo menos em relação aos resultados.

Esclarecimento: o componente Acesso controla o acesso de usuários ao aplicativo. Ele controla para cada action de cada controller. Mas para isso nós precisamos preencher a tabela privileges através do controller de mesmo nome. Somente após cadastrar cada action de cada controller e seu respectivo usuário, somente então ele funcionará corretamente.

Começe chamando o controller privileges:
http://localhost/nomeaplicativo/privileges/

E cadastrando todos os actions que deseja controlar. Deixe de fora somente os que deseja deixar com acesso ao público. Aqui estou deixando somente o menus, login e logout com acesso ao público.

Instalação

- Requisitos:
	- Este componente requer que o aplicativo tenha implementado o componente Auth.
	- Também precisa que o aplicativo tenha as tabelas users e groups. Users deve ter um campo chamado
	 "group_id", que o relaciona a groups. O que mostra que é um componente dependente de outras coisas.
	Sem estes requisitos o componente não irá funcionar. Veja o script componente_acesso.sql
	 aqui junto com o componente para ajudar.
	Sugestão: idealmente criar as tabelas do zero e depois gerar os CRUDs com o bake.

- Copiar o componente Acesso em app/Controller/Component

- Habilitar o componente no início do AppController.php (apenas adicione, caso já tenha):
	public $components = array('Acesso');

- Importar para o banco do aplicativo o script da tabela privileges, groups e users, caso não existam 
	(veja o script componente_acesso.sql)

- Gerar o código para o CRUD previleges e groups e users, caso não existam, acessando o terminal e executando:
cd /var/www/minhaaplicacao/app/Console
./cake bake all

Adicionar ao AppController

	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('menus'); // Precisa ser liberado para o público

		if($this->action != 'menus'){ // Caso precise deixar menus com acesso público
			$controller=$this->params['controller'];
			$action=$this->params['action'];
			$this->Acesso->access($controller,$action);
			if($this->Acesso->redir==true){
				$this->redirect(array('controller'=>'users','action' => 'login'));
			}
		}
	}

O componente está configurado para trabalhar apenas com grupos, portanto deve criar os usuários dentro dos respectivos grupos para que tenham os devidos privilégios.

Como estão as permissões no componente:

O componente apenas prevê 3 grupos e com seus ids: 1(admins), 2(gerentes) e 3(usuarios).

Um detalhe importante:
- Quando permitir que o grupo 3 tenha acesso a um action, também terão acesso o 2 e o 1.
- Quando permitir que o grupo 2 tenha acesso a um action, também terá acesso o 1.
- Quando permitir que o grupo 1 tenha acesso a um action, somente ele terá acesso.
Vale lembrar que alguns actions devem ficar disponíveis para acesso ao público, como é o caso do menus, login e logout.

Privilégios
Lembre que somente poderá testar corretamente os privilégios de todos os usuários após cadastrar todos os privilégios através do controller "privileges", já que o controle do componente é efetuado de acordo com a respectiva tabela "privileges".

Importante:
Altere o método isAuthorized() para que fique como abaixo (alterar a linha do if):

	public function isAuthorized($user) {
	    return true;
	}

