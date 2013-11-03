# Access Control

Component to control user access to applications in CakePHP 2.3+ with a web interface for management control user permissions.

## Plugin Admin

This component has descontinued and start plugin: http://github.com/ribafs/cakeadmin

## Compatibility

Compatible with CakePHP 2.3

# Introduction

I missed a web interface to manage access control and decided to learn how to work with code in Cake and create a control with web interface. Even though I found the plugins Usermgmt and Admin and still decided to create one. It is not a matter of reinventing the wheel, but I wanted to have a custom control, creating something with simple code and that can also help other beginners in Cake.

# Requirements

The component Access Control control users to access to the application. It controls every action of each controller. But for that we need to fill the table privileges via the controller of the same name. Only after registering each action of each controller and its respective user, only then Access Control will work properly.

- This component requires that the application has implemented the Auth component.
- Also need the application to have the tables users and groups. Users must have a field called "group_id", which relates to groups. This shows that a component is dependent on other things.
Without these requirements, the component will not work.

# Installation

Download - https://github.com/ribafs/acesso/archive/master.zip 

Import the script below to database

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

The component, as it is in its initial phase, is tied to the users table, which should have the group_id field, but should not give work to make adaptations that do not change an existing application.

We will also create or adapt existing bank:

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

# Implementing Auth Component

If you have not implemented the Auth component in your application, implement now.
If you need help follow the official tutorial:
http://book.cakephp.org/2.0/en/tutorials-and-examples/blog-auth-example/auth.html

# Registering Permissions

Start by calling the controller privileges:
http://localhost/seuaplicativo/privileges/

And enrolling all actions you want to control, and only them.
Leave out the actions you want to allow public access. Here I am leaving only the menus, login and logout with public access.

Copy the file component AcessoComponent.php to app/Controller/Component

# # Settings

Add the component Access Control to AppController:

    public $components = array( 
        'Session', 'Acesso',
		...
	}

Add to AppController beforeFilter, leaving out the actions that will give the public:

	public function beforeFilter() { 
		$this->Auth->allow('menus');

		if($this->action != 'menus'){ 

			$controller=$this->params['controller']; 
			$action=$this->params['action']; 
			$this->Acesso->access($controller,$action); 
	 
			if($this->Acesso->redir==true){ 
				$this->redirect(array('controller' => 'users','action' => 'login')); 
			} 
		} 
	} 

In this case, I'm setting up for it give access to the public only in action "menus".

Change the method in AppController isAuthorized to read:

	public function isAuthorized($user) { 
	    return true; 
	} 

# Using

http://localhost/yourapp/

Log in as admin and register users: manager at the User Group managers and users.
Then log out.
Log in as manager and try testing or User privileges on registered privileges.


Copyright and License #

Copyright 2013 Ribamar FS (http://ribafs.org/)

Licensed by MIT License (http://opensource.org/licenses/mit-license.php)

## Thanks

* First the team that created and maintains the CakePHP Framework
* After the user group CakePHP in portuguese in Google, which helped me to solve problems encountered

