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
class AppController extends AbstractActionController {
	
	public function init() {
	}
	public function indexAction() {
		$dm = $this->getServiceLocator ()->get ( 'doctrine.documentmanager.odm_default' );
		$qb = $dm->createQueryBuilder ( 'App\Document\User' )->field ( 'name' )->getQuery ()->execute ();
		return new ViewModel ( array (
				'qb' => $qb 
		) );
	}
	public function addAction() {
		$form = new addForm ();
		$request = $this->getRequest ();
		if ($request->isPost ()) 		// or use $_POST['username'] for specific
		{
			$name = $request->getPost ( 'username' );
			$pwd = $request->getPost ( 'login_pwd' );
			$dm = $this->getServiceLocator ()->get ( 'doctrine.documentmanager.odm_default' );
			$user = new User ();
			$user->setPassword ( $pwd );
			$user->setName ( $name );
			$dm->persist ( $user );
			$dm->flush ();
		}
		return array (
				'form' => $form 
		);
	}
	public function editAction() {
		
		$request = $this->getRequest ();
		$id = $this->params ()->fromRoute ( 'id' );
		$form->get ( 'state' )->setValue ( $id );
		$dm = $this->getServiceLocator ()->get ( 'doctrine.documentmanager.odm_default' );
		$qb = $dm->createQueryBuilder ( 'App\Document\User' )->field ( '_id' )->equals($id)->getQuery ()->execute ();
		foreach ( $qb as $row ) {
			$form->get ( 'username' )->setValue ( $row->getName () );
		}
		if (isset ( $_POST ['username'] )) 		// or use $_POST['username'] for specific
		{
			$new = $request->getPost ( 'username' );
			$eid = $request->getPost ( 'state' );
			$job = $dm->createQueryBuilder ( 'App\Document\User' )->update ()->field ( 'name' )->set ( $new )->field ( '_id' )->equals ( $eid )->getQuery ()->execute ();
		}
		return array (
				'qb' => $form 
		);
	}
	public function findAction() {
	
		$request = $this->getRequest ();
		//$id = $this->params ()->fromRoute ( 'id' );
		//$form->get ( 'state' )->setValue ( $id );
		$dm = $this->getServiceLocator ()->get ( 'doctrine.documentmanager.odm_default' );
		$qb = $dm->createQueryBuilder ( 'App\Document\User' )->field ( '_id' )->equals($id)->getQuery ()->execute ();
		foreach ( $qb as $row ) {
		$data=json_encode($row);
		}
		return array (
				'qb' => $data
		);
	}
	
	public function loginAction() {
		$form = new addForm ();
		$request = $this->getRequest ();
		if (isset ( $_POST ['login_name'] )) {
			$dm = $this->getServiceLocator ()->get ( 'doctrine.documentmanager.odm_default' );
			$name = $request->getPost ( 'login_name' );
			$pwd = $request->getPost ( 'login_pwd' );
			$exists = $dm->createQueryBuilder ( 'App\Document\User' )->field ( 'name' )->equals ( $name )->field ( 'password' )->equals ( $pwd )->count ()->getQuery ()->execute ();
			// echo $qb;
			if ($exists) {
				return $this->redirect ()->toUrl ( 'app#nav/grid' );
			} else {
				echo "Incorrect Credentials";
			}
		}
		return array (
				'form' => $form 
		);
	}
	public function deleteAction() {
		return array ();
	}
	public function gridAction() {
		return array ();
	}
	public function appAction() {
		$form = new addForm ();
		$dm = $this->getServiceLocator ()->get ( 'doctrine.documentmanager.odm_default' );
		$qb = $dm->createQueryBuilder ( 'App\Document\User' )->field ( 'name' )->getQuery ()->execute ();
		return new ViewModel ( array (
				'qb' => $qb,
				'form' => $form 
		) );
	}
	public function dataAction() {
		$dm = $this->getServiceLocator ()->get ( 'doctrine.documentmanager.odm_default' );
		$qb = $dm->createQueryBuilder ( 'App\Document\User' )->field ( 'name' )->getQuery ()->execute ();
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
			$data[]=array($name,$pwd);
			$data=json_encode($data);
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
		$data=json_decode($msg->body);
		echo "Recieved Data : ";
		var_dump($msg->body);
		$connection = new AMQPConnection ( 'localhost', 5672, 'guest', 'guest' );
		$channel = $connection->channel ();
		$msg1 = new AMQPMessage ( $msg->body );
		$channel->basic_publish ( $msg1, '', 'dataQ' );
		$channel->close ();
		$connection->close ();
		echo "Data Sent to Response Queue ... \n";
			$dm = $this->getServiceLocator ()->get ( 'doctrine.documentmanager.odm_default' );
			$user = new User ();
			$user->setPassword ( $data[0][1] );
			$user->setName (  $data[0][0] );
			$dm->persist ( $user );
			$dm->flush ();
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
