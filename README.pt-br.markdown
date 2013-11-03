# Access Control

Componente para controlar o acesso de usuários para aplicativos em CakePHP 2.3+ com uma interface web para gerenciamento do controle de permissões de usuários.

## Plugin Admin
Este componente foi descontinuado e deu origem ao plugin:
http://github.com/ribafs/cakeadmin


## Compatibilidade

Compatível com CakePHP 2.3

## Introdução

Eu senti falta de uma interface web para gerenciar o controle de acesso e aproveitei para aprender a mexer com código no Cake e criar um controle com interface web. Mesmo que eu tenha encontrado os plugins Usermgmt e Admin, ainda assim resolvi criar um. Não é uma questão de reinventar a roda, mas eu queria ter um controle customizado, criar algo com código simples e que também possa ajudar outros iniciantes no Cake.

## Sugestões e Críticas

Este componente está em fase inicial e por isso gostaria de receber sugestões e/ou críticas para melhorá-lo.<br>
ribafs @ gmail.com

## Requisitos

O componente Access Control controla o acesso de usuários ao aplicativo. Ele controla para cada action de cada controller. Mas para isso nós precisamos preencher a tabela privileges através do controller de mesmo nome. Somente após cadastrar cada action de cada controller e seu respectivo usuário, somente então ele funcionará corretamente.

- Este componente requer que o aplicativo tenha implementado o componente Auth.
- Também precisa que o aplicativo tenha as tabelas users e groups. Users deve ter um campo chamado "group_id", que o relaciona a groups. O que mostra que é um componente dependente de outras coisas.
Sem estes requisitos o componente não irá funcionar.

## Instalação

Download - https://github.com/ribafs/acesso/archive/master.zip 

Importe o script abaixo para o banco do aplicativo.

<pre>
CREATE TABLE IF NOT EXISTS `privileges` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL,
  `controller` varchar(50) NOT NULL,
  `action` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `controller` (`controller`,`action`)
);
</pre>

O componente, como está em fase inicial, está amarrado com a tabela users e esta deve ter o campo group_id, mas não deve dar trabalho de realizar adaptações para que não altere um aplicativo existente.

Vamoms criar também ou adaptar o banco existente:

<pre>
CREATE TABLE IF NOT EXISTS `groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
);

INSERT INTO `groups` (`id`, `name`, `created`, `modified`) VALUES
(1, 'admins', '2013-06-22 17:05:29', '2013-06-22 17:05:29'),
(2, 'gerentes', '2013-06-23 10:57:28', '2013-06-23 10:57:28'),
(3, 'usuarios', '2013-06-24 07:47:48', '2013-06-24 07:47:48');

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` char(40) NOT NULL,
  `group_id` int(11) NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
);
</pre>

Obs.: os nomes admin, gerente e usuario são apenas sugestões.

## Implementando o Component Auth

Caso ainda não tenha implementado o componente Auth no seu aplicativo, implemente agora.
Se precisar de ajuda siga o tutorial oficial:
http://book.cakephp.org/2.0/en/tutorials-and-examples/blog-auth-example/auth.html
Ao final deste readme deixei uma versão resumida e adaptada para uso do Auth com o Acesso.

## Cadastrando as Permissões

Começe chamando o controller privileges:
http://localhost/seuapp/privileges/

E cadastrando todos os actions que deseja controlar e somente eles. 
Deixe de fora os actions que deseja permitir acesso público. Aqui estou deixando somente o menus, login e logout com acesso público.

Copie o arquivo do componente AcessoComponent.php para app/Controller/Component

## Configurações

Adicione a entrada do componente Acesso ao AppController:

    public $components = array( 
        'Session', 'Acesso',
		...
	}

Adicione o beforeFilter ao AppController, deixando de fora os actions que darão público:

	public function beforeFilter() { 
		parent::beforeFilter();
		$this->Auth->allow('menus'); // Liberado para o público 

		if($this->action != 'menus'){ 

			$controller=$this->params['controller']; 
			$action=$this->params['action']; 
			$this->Acesso->access($controller,$action); 
	 
			if($this->Acesso->redir==true){ 
				$this->redirect(array('controller' => 'users','action' => 'login')); 
			} 
		} 
	} 

Neste caso, estou configurando para que ele dê acesso ao público somente no action "menus".

Altere o método isAuthorized em AppController para ficar assim:

	public function isAuthorized($user) { 
	    return true; 
	} 

## Testando

http://localhost/seuapp/

Faça login como admin e cadastre os usuários: gerente no grupo gerentes e usuario no usuarios.
Então faça logout.
Faça login como gerente ou usuario e experimente testar os privilégios cadastrados em privileges.


## Copyright e Licença

Copyright 2013, Ribamar FS (http://ribafs.org/)

Licenciado pela Licença MIT (http://opensource.org/licenses/mit-license.php)

## Agradecimentos

* Primeiro à equipe que criou e mantém o Framework CakePHP
* Depois ao grupo de usuários do CakePHP em português no Google, que me ajudaram a solucionar problemas encontrados


## Implementando o componente Auth

Este tutorial é uma versão resumida do tutorial oficial, pois quem fará o principal trabalho de controle de acesso será o componente Acesso.

Alerta: a tabela users não deve ter nenhum usuário cadastrado no início, deve estar vazia.
Os usuários devem ser cadastrados após a implementação do Auth e através do controller users.

Caso receba a mensagem:
Erro: Table useres for model User was not found in datasource default. Isso aconteceu comigo sempre que instalei o o plugin Cakept_br. Então adicione a variável $useTable ao model User
public $useTable = 'users';

Adaptado do tutorial oficial:
http://book.cakephp.org/2.0/en/tutorials-and-examples/blog-auth-example/auth.html

Ajustar AppController.php para isso: 

class AppController extends Controller { 
    public $components = array( 
        'Session', 'Acesso',
        'Auth' => array( 
			'loginAction' => array('controller'=>'users','action'=>'login'),
            'loginRedirect' => array('controller' => 'posts', 'action' => 'index'), 
            'logoutRedirect' => array('controller' => 'users', 'action' => 'login'), 
			'authorize' => array('Controller') // Added this line 
        ) 
    ); 

    public function beforeFilter() { 
        $this->Auth->allow('index','view'); // Estes terão acesso público 
    } 

	public function isAuthorized($user) { 
	    return true; 
	} 
} 

Adicionar ao controller Users: 

	public function login() { 
		if ($this->request->is('post')) { 
		    if ($this->Auth->login()) { 
		        $this->redirect($this->Auth->redirect()); 
		    } else { 
		        $this->Session->setFlash(__('Invalid username or password, try again')); 
		    } 
		} 
	} 

	public function logout() { 
		$this->redirect($this->Auth->logout()); 
	} 

Criar a view Users/login.ctp: 

<div class="users form"> 
<?php echo $this->Session->flash('auth'); ?> 
<?php echo $this->Form->create(null, array('url' => '/posts/index')); ?> 
    <fieldset> 
        <legend><?php echo __('Please enter your username and password'); ?></legend> 
        <?php echo $this->Form->input('username'); 
        echo $this->Form->input('password'); 
    ?> 
    </fieldset> 
<?php echo $this->Form->end(__('Login')); ?> 
</div> 

No model User.php: 

Após
App::uses('AppModel', 'Model'); 

Adicione:
App::uses('AuthComponent', 'Controller/Component');

class User extends AppModel { 

	public function beforeSave($options = array()) { 
		if (isset($this->data[$this->alias]['password'])) { 
		    $this->data[$this->alias]['password'] = AuthComponent::password($this->data[$this->alias]['password']); 
		} 
		return true; 
	} 

Estas são as alterações a serem feitas. 

Testando
Agora tente adicionar um cliente, funcionario, produto ou pedido ou editar. 
Verá que será redirecionado para o login com a mensagem de que não tem autorização. 

Adicionar Usuário
Agora precisamos permitir, por enquanto, que o público use a view "add" em AppController para adicionar um usuário admin. 
$this->Auth->allow('index','add'); 
 
Acesse então 
http://localhost/seuapp/users/ 

E adicione um usuário "admin" no grupo "admins". 

Desfaça então a alteração no AppController:
$this->Auth->allow('index'); // Deixando para o acesso público somente 'menus'.

Faça o login como user "admin" em: 
http://localhost/seuapp/users/login 

Agora terá direito a qualquer operação como admin.
Lembre que para fazer logout pode chamar:
http://localhost/seuapp/users/logout 

Prontinho, temos nosso aplicativo com controle de acesso implementado via componente Auth.


# Aplicativo de Exemplo

O arquivo auth_acesso.zip [https://github.com/ribafs/acesso/blob/master/auth_acesso.zip] contém um aplicativo com o Cake 2.3.7 e os componentes Auth e Acesso implementados para uma desmonstração rápida do funcionamento do componente Acesso.
Basta descompactar no diretório web, criar o banco e importar o script sql do raiz e testar. Sugestões e críticas serão bem vindas.

login - admin<br>
senha - admin

Para gerenciar as permissões abra o controller privileges.

http://localhost/auth_acesso/privileges
