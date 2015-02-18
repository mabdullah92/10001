<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/App for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace App\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use App\Form\addForm;
use App\Document\User;
use Zend\View\Model\ViewModel;
use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Zend\Session\Container;

class AppController extends AbstractActionController {
	public function init() {
		// $this->dm = $this->getServiceLocator ()->get ( 'doctrine.documentmanager.odm_default' );
	}
	private function getDm() {
		$dm = $this->getServiceLocator ()->get ( 'doctrine.documentmanager.odm_default' );
		return $dm;
	}
	private function getReq() {
		$request = $this->getRequest ();
		return $request;
	}
	public function indexAction() {
		$qb = $this->getDm ()->createQueryBuilder ( 'App\Document\User' )->field ( 'name' )->getQuery ()->execute ();
		return new ViewModel ( array (
				'qb' => $qb 
		) );
	}
	public function addAction() {
		$form = new addForm ();
		$request = $this->getRequest ();
		if (isset ( $_POST ['editId'] )) 		// or use $_POST['username'] for specific
		{
			$new = $request->getPost ( 'newName' );
			$eid = $request->getPost ( 'editId' );
			$job = $this->getDm ()->createQueryBuilder ( 'App\Document\User' )->update ()->field ( 'name' )->set ( $new )->field ( '_id' )->equals ( $eid )->getQuery ()->execute ();
		}
		return array (
				'form' => $form 
		);
	}
	public function seteditAction() {
		$request = $this->getRequest ();
		$id = $request->getPost ( 'userId' );
		$row = "no record";
		$qb = $this->getDm ()->createQueryBuilder ( 'App\Document\User' )->field ( '_id' )->equals ( new \MongoId ( $id ) )->getQuery ()->execute ();
		foreach ( $qb as $row ) {
			
			$arr = array (
					'id' => $row->getId (),
					'name' => $row->getName (),
					'password' => $row->getPassword () 
			);
		}
		
		$viewModel = new ViewModel ( array (
				'foo' => 'bar',
				'data' => $arr 
		) );
		$viewModel->setTerminal ( true );
		return $viewModel;
	}
	public function editAction() {
		$request = $this->getRequest ();
		
		if (isset ( $_POST ['editId'] )) 		// or use $_POST['username'] for specific
		{
			$new = $request->getPost ( 'newName' );
			$eid = $request->getPost ( 'editId' );
			$job = $this->getDm ()->createQueryBuilder ( 'App\Document\User' )->update ()->field ( 'name' )->set ( $new )->field ( '_id' )->equals ( $eid )->getQuery ()->execute ();
		}
		
		$viewModel = new ViewModel ( array (
				'foo' => 'bar' 
		) );
		$viewModel->setTerminal ( true );
		return $viewModel;
	}
	public function findAction() {
		$request = $this->getRequest ();		
		$qb = $this->getDm ()->createQueryBuilder ( 'App\Document\User' )->field ( '_id' )->equals ( $id )->getQuery ()->execute ();
		foreach ( $qb as $row ) {
			$data = json_encode ( $row );
		}
		return array (
				'qb' => $data 
		);
	}
	public function loginAction() {
		$viewModel = new ViewModel ( array (
				'foo' => 'bar' 
		) );
		$viewModel->setTerminal ( true );
		$request = $this->getRequest ();
		if (isset ( $_POST ['login_name'] )) {
			
			$name = $request->getPost ( 'login_name' );
			$pwd = $request->getPost ( 'login_pwd' );
			$exists = $this->getDm()->createQueryBuilder ( 'App\Document\User' )->field ( 'name' )->equals ( $name )->field ( 'password' )->equals ( $pwd )->count ()->getQuery ()->execute ();
			if ($exists) {
				$user_session = new Container ( 'user' );
				$user_session->username = $name;
				echo "success";
				// return $this->redirect ()->toUrl ( 'app#nav/grid' );
			} else {
				echo "Incorrect Credentials";
			}
		}
		return $viewModel;
	}
	public function deleteAction() {
		if (isset ( $_POST ['delId'] )) {
			
			$delId = $this->getReq ()->getPost ( 'delId' );
			$this->getDm ()->createQueryBuilder ( 'App\Document\User' )->remove ()->field ( '_id' )->equals ( new \MongoId ( $delId ) )->getQuery ()->execute ();
		}
		$viewModel = new ViewModel ( array (
				'foo' => 'bar' 
		) );
		
		$viewModel->setTerminal ( true );
		return $viewModel;
	}
	public function gridAction() {
	}
	public function appAction() {
		$form = new addForm ();
		$qb = $this->getDm ()->createQueryBuilder ( 'App\Document\User' )->field ( 'name' )->getQuery ()->execute ();
		return new ViewModel ( array (
				'qb' => $qb,
				'form' => $form 
		) );
	}
	public function dataAction() {
		$qb = $this->getDm ()->createQueryBuilder ( 'App\Document\User' )->field ( 'name' )->getQuery ()->execute ();
		return new ViewModel ( array (
				'qb' => $qb 
		) );
	}
	public function prodAction() {
		$form = new addForm ();
		$request = $this->getRequest ();
		if ($request->isPost ()) 		// or use $_POST['username'] for specific
		{
			$name = $request->getPost ( 'username' );
			$pwd = $request->getPost ( 'login_pwd' );
			$user_session = new Container ( 'user' );
			$key = $user_session->username;
			$data [] = array (
					$name,
					$pwd,
					$key 
			);
			$data = json_encode ( $data );
			$connection = new AMQPConnection ( 'localhost', 5672, 'guest', 'guest' );
			$channel = $connection->channel ();
			$channel->queue_declare ( 'MsgQueue', false, false, false, false );
			$msg = new AMQPMessage ( $data );
			$channel->basic_publish ( $msg, '', 'MsgQueue' );
			echo " [x] Sent 'Message2!'\n";
			$channel->close ();
			$connection->close ();
		}
		return array (
				'form' => $form 
		);
	}
	public function consAction() {
		echo "Listening ... \n";
		$connection = new AMQPConnection ( 'localhost', 5672, 'guest', 'guest' );
		$channel = $connection->channel ();
		$channel->queue_declare ( 'MsgQueue', false, false, false, false );
		$callback = function ($msg) {
			$data = json_decode ( $msg->body );
			echo "Recieved Data : ";
			var_dump ( $msg->body );
			$connection = new AMQPConnection ( 'localhost', 5672, 'guest', 'guest' );
			$channel = $connection->channel ();
			$msg1 = new AMQPMessage ( $msg->body );
			$channel->basic_publish ( $msg1, '', 'dataQ' );
			$channel->close ();
			$connection->close ();
			echo "Data Sent to Response Queue ... \n";
			
			$user = new User ();
			$user->setPassword ( $data [0] [1] );
			$user->setName ( $data [0] [0] );
			$this->getDm ()->persist ( $user );
			$this->getDm ()->flush ();
			echo "Data Saved ... \n";
		};
		
		$channel->basic_consume ( 'MsgQueue', '', false, true, false, false, $callback );
		while ( count ( $channel->callbacks ) ) {
			
			$channel->wait ();
		}
		$channel->close ();
		$connection->close ();
	}
	public function socketAction() {
		$form = new addForm ();
		return array (
				'form' => $form 
		);
	}
}
