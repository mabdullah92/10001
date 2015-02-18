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

class AppController extends AbstractActionController
{

    public function init()
    {
        // $this->dm = $this->getServiceLocator ()->get ( 'doctrine.documentmanager.odm_default' );
    }

    private function getDm()
    {
        $dm = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        return $dm;
    }

    private function getReq()
    {
        $request = $this->getRequest();
        return $request;
    }

    public function indexAction()
    {
        $qb = $this->getDm()
            ->createQueryBuilder('App\Document\User')
            ->field('name')
            ->getQuery()
            ->execute();
        return new ViewModel(array(
            'qb' => $qb
        ));
    }

    public function addAction()
    {
        $form = new addForm();
        // $request = $this->getRequest ();
        if (isset($_POST['editId'])) // or use $_POST['username'] for specific
{
            $new = $this->getReq()->getPost('newName');
            $eid = $this->getReq()->getPost('editId');
            $job = $this->getDm()
                ->createQueryBuilder('App\Document\User')
                ->update()
                ->field('name')
                ->set($new)
                ->field('_id')
                ->equals($eid)
                ->getQuery()
                ->execute();
        }
        return array(
            'form' => $form
        );
    }

    public function seteditAction()
    {
        $id = $this->getReq()->getPost('userId');
        $row = "no record";
        $qb = $this->getDm()
            ->createQueryBuilder('App\Document\User')
            ->field('_id')
            ->equals(new \MongoId($id))
            ->getQuery()
            ->execute();
        foreach ($qb as $row) {
            
            $arr = array(
                'id' => $row->getId(),
                'name' => $row->getName(),
                'password' => $row->getPassword()
            );
        }
        
        $viewModel = new ViewModel(array(
            'foo' => 'bar',
            'data' => $arr
        ));
        $viewModel->setTerminal(true);
        return $viewModel;
    }

    public function editAction()
    {
        if (isset($_POST['editId'])) // or use $_POST['username'] for specific
{
            $new = $this->getReq()->getPost('newName');
            $eid = $this->getReq()->getPost('editId');
            $job = $this->getDm()
                ->createQueryBuilder('App\Document\User')
                ->update()
                ->field('name')
                ->set($new)
                ->field('_id')
                ->equals($eid)
                ->getQuery()
                ->execute();
        }
        
        $viewModel = new ViewModel(array(
            'foo' => 'bar'
        ));
        $viewModel->setTerminal(true);
        return $viewModel;
    }

    public function findAction()
    {
        $qb = $this->getDm()
            ->createQueryBuilder('App\Document\User')
            ->field('_id')
            ->equals($id)
            ->getQuery()
            ->execute();
        foreach ($qb as $row) {
            $data = json_encode($row);
        }
        return array(
            'qb' => $data
        );
    }

    public function loginAction()
    {
        $viewModel = new ViewModel(array(
            'foo' => 'bar'
        ));
        $viewModel->setTerminal(true);
        
        if (isset($_POST['login_name'])) {
            
            $name = $this->getReq()->getPost('login_name');
            $pwd = $this->getReq()->getPost('login_pwd');
            $exists = $this->getDm()
                ->createQueryBuilder('App\Document\User')
                ->field('name')
                ->equals($name)
                ->field('password')
                ->equals($pwd)
                ->count()
                ->getQuery()
                ->execute();
            if ($exists) {
                $user_session = new Container('user');
                $user_session->username = $name;
                echo "success";
                // return $this->redirect ()->toUrl ( 'app#nav/grid' );
            } else {
                echo "Incorrect Credentials";
            }
        }
        return $viewModel;
    }

    public function deleteAction()
    {
        if (isset($_POST['delId'])) {
            
            $delId = $this->getReq()->getPost('delId');
            $this->getDm()
                ->createQueryBuilder('App\Document\User')
                ->remove()
                ->field('_id')
                ->equals(new \MongoId($delId))
                ->getQuery()
                ->execute();
        }
        $viewModel = new ViewModel(array(
            'foo' => 'bar'
        ));
        
        $viewModel->setTerminal(true);
        return $viewModel;
    }

    public function gridAction()
    {}

    public function appAction()
    {
        $form = new addForm();
        $qb = $this->getDm()
            ->createQueryBuilder('App\Document\User')
            ->field('name')
            ->getQuery()
            ->execute();
        return new ViewModel(array(
            'qb' => $qb,
            'form' => $form
        ));
    }

    public function dataAction()
    {
        $qb = $this->getDm()
            ->createQueryBuilder('App\Document\User')
            ->field('name')
            ->getQuery()
            ->execute();
        return new ViewModel(array(
            'qb' => $qb
        ));
    }

    public function prodAction()
    {
        $form = new addForm();
        
        if ($this->getReq()->isPost()) // or use $_POST['username'] for specific
{
            $name = $this->getReq()->getPost('username');
            $pwd = $this->getReq()->getPost('add_pwd');
            $user_session = new Container('user');
            $key = $user_session->username;
            $data[] = array(
                $name,
                $pwd,
                $key
            );
            $data = json_encode($data);
            $connection = new AMQPConnection('localhost', 5672, 'guest', 'guest');
            $channel = $connection->channel();
            $channel->queue_declare('MsgQueue', false, false, false, false);
            $msg = new AMQPMessage($data);
            $channel->basic_publish($msg, '', 'MsgQueue');
            echo " [x] Sent 'Message2!'\n";
            $channel->close();
            $connection->close();
        }
        return array(
            'form' => $form
        );
    }

    public function consAction()
    {
        echo "Listening ... \n";
        $connection = new AMQPConnection('localhost', 5672, 'guest', 'guest');
        $channel = $connection->channel();
        $channel->queue_declare('MsgQueue', false, false, false, false);
        $callback = function ($msg) //GET DATA FROM MSG QUEUE
        {
            $data = json_decode($msg->body); //DECODE TO ARRAY FOR SAVING PURPOSE
            echo "Recieved Data : ";
            var_dump($msg->body);
            // $connection = new AMQPConnection ( 'localhost', 5672, 'guest', 'guest' );
            // $channel = $connection->channel ();
            // $msg1 = new AMQPMessage ( $msg->body );
            // $channel->basic_publish ( $msg1, '', 'dataQ' );
            // $channel->close ();
            // $connection->close ();
            echo "Data Sent to Response Queue ... \n";
            
            $user = new User(); 
            $user->setPassword($data[0][1]);
            $user->setName($data[0][0]);
            $this->getDm()->persist($user);
            $this->getDm()->flush();
            $uu = $user->getId();
            
            $data[0][] = $uu; //push id of inserted element to msg array
            // var_dump($data);
            $data = json_encode($data);
            var_dump($data);
            $connection = new AMQPConnection('localhost', 5672, 'guest', 'guest');
            $channel = $connection->channel();
            $msg1 = new AMQPMessage($data);
            $channel->basic_publish($msg1, '', 'dataQ');
            $channel->close();
            $connection->close();
            echo "Data Saved ... \n";
        };
        
        $channel->basic_consume('MsgQueue', '', false, true, false, false, $callback);
        while (count($channel->callbacks)) {
            
            $channel->wait();
        }
        $channel->close();
        $connection->close();
    }

    public function socketAction()
    {
        $form = new addForm();
        return array(
            'form' => $form
        );
    }
}
