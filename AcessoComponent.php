<?php
/**
 * Component Acesso
 * Versão 2 para Cake 2.x
 * Autor: Ribamar FS
 *
 * Este componente tem como finalidade controlar o acesso dos usuários ao
 * aplicativo através de uma interface web.
 *
 * Licenciado sob The MIT License
 * Redistribuições deste arquivo precisa reter o aviso de copyright.
 *
 * @copyright     Copyright (c) Ribamar FS (http://ribafs.org)
 * @link          http://ribafs.org
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Component', 'Controller');

class AcessoComponent extends Component{
	public $uses = array('Privilege');
	public $components = array('Session','Auth');
	public $redir=false;

	public function startup(Controller $controller) {
		// Em Component redirect requer o Controller: 
		//http://stackoverflow.com/questions/16697723/cakephp-how-to-redirect-in-a-component
		$this->Controller = $controller;
	}

	public function admin() {
		// Capturar id do usuário logado
		$valor = $this->Session->read('Auth.User');  //Retorna o array com o id, nome do usuário e 
		$userauth=$valor['group_id'];//$valor['id'];
		if(isset($userauth) && $userauth == 1){
			return true;
		}else{
			return false;
		}
	}	

	public function adminAcesso() {
		if(!$this->admin()){
			$this->Session->setFlash(__('Acesso Negado!'));
			//$this->Controller->redirect(array('controller'=>'users','action' => 'login'));
	        //$this->log($this->Auth->redirect());
	        //$this->redirect($this->Auth->redirect());
			$this->redir = true;
			return $this->redir;
		}
	}

	public function gerente() {
		$valor = $this->Session->read('Auth.User');
		$userauth=$valor['group_id'];
		if((isset($userauth) && $userauth == 2) || $userauth == 1)	{
			return true;
		}else{
			return false;
		}
	}	

	public function gerenteAcesso() {
		if(!$this->gerente()){
			$this->Session->setFlash(__('Acesso Negado!'));
			//$this->Controller->redirect(array('controller'=>'users','action' => 'login'));
			$this->redir = true;
			return $this->redir;
		}
	}

	public function usuario() {
		$valor = $this->Session->read('Auth.User');
		$userauth=$valor['group_id'];
		if((isset($userauth) && $userauth == 3) || $userauth == 2 || $userauth == 1)	{
			return true;
		}else{
			return false;
		}
	}	

	public function usuarioAcesso() {
		if(!$this->usuario()){
			$this->Session->setFlash(__('Acesso Negado!'));
			//$this->Controller->redirect(array('controller'=>'users','action' => 'login'));
			$this->redir = true;
			return $this->redir;
		}
	}
	// Dica sobre o código de $userauth em: https://groups.google.com/forum/#!topic/cakephp-pt/jzEDyhPHG1I

	public function acessoUser($controller,$action){
		//$this->Cliente = ClassRegistry::init('Cliente');// Permitir uso de model em component
		$this->Privilege = ClassRegistry::init('Privilege');// Permitir uso de model em component

		$usuario = $this->Privilege->query("select group_id from privileges where controller='".$controller."' and action='".$action."'");
//pr($usuario);
		if(isset($usuario[0]['privileges']['group_id'])){
			return $usuario[0]['privileges']['group_id'];
		}else{
			return false;		
		}
	}

	public function access($controller,$action){
		if($this->acessoUser($controller,$action) == 1) {
			$this->adminAcesso();
		}elseif($this->acessoUser($controller,$action) == 2){
			$this->gerenteAcesso();
		}elseif($this->acessoUser($controller,$action) == 3){	
			$this->usuarioAcesso();
		}else{
			$this->Session->setFlash(__('Área Restrita!'));//Permissão não cadastrada!
			return $this->redir;
		}
	}
}
