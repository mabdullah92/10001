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
use Zend\View\Model\JsonModel;

class AppController extends AbstractActionController
{

    /*
     * OPERATION CODE REFERENCE
     * LOGIN : 0
     * INSERT: 1
     * DELETE: 2
     * UPDATE: 3
     */
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
            $opp = 3; // OPCODE THREE FOR UPDATE
            $data[] = array(
                null, // 0
                null, // 1
                null, // 2
                $opp, // 3
                $eid, // 4
                $new
            ); // 5
            
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
            
            $login_user = $this->getReq()->getPost('login_name');
            $login_pwd = $this->getReq()->getPost('login_pwd');
            $opp = 0; // SET OPPCODE FOR LOGIN
            $data[] = array(
                null, // 0
                null, // 1
                null, // 2
                $opp, // 3
                null, // 4
                null, // 5
                $login_user, // 6
                $login_pwd
            ); // 7
            
            $data = json_encode($data);
            $connection = new AMQPConnection('localhost', 5672, 'guest', 'guest');
            $channel = $connection->channel();
            $channel->queue_declare('MsgQueue', false, false, false, false);
            $msg = new AMQPMessage($data);
            $channel->basic_publish($msg, '', 'MsgQueue');
            // echo " [x] Sent 'Message2!'\n";
            $channel->close();
            $connection->close();
            $user_session = new Container('user');
            $user_session->readykey = $login_user . $login_pwd;
            $user_session->username = $login_user;
        }
        
        return $viewModel;
    }

    public function deleteAction()
    {
        if (isset($_POST['delId'])) {
            
            $delId = $this->getReq()->getPost('delId');
            $opp = 2;
            $data[] = array(
                null,
                null,
                null,
                $opp,
                $delId
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
        $viewModel = new ViewModel(array(
            'foo' => 'bar'
        ));
        
        $viewModel->setTerminal(true);
        return $viewModel;
    }
public function koAction(){
    
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
            ->getQuery()
            ->execute();
        foreach ($qb as $row){
            $arr[]=array("name"=>$row->getName(),"pwd"=>$row->getPassword(),"id"=>$row->getId());
        }
     $json = json_encode($arr);
     $this->response->setContent($json);
     return $this->response;
    }

    public function prodAction()
    {
        $form = new addForm();
        
        if ($this->getReq()->isPost()) // or use $_POST['username'] for specific
{
            $name = $this->getReq()->getPost('username');
            $opp = 1; // see opperation code reference add top
            $pwd = $this->getReq()->getPost('add_pwd');
            $user_session = new Container('user');
            $key = $user_session->username;
            $data[] = array(
                $name,
                $pwd,
                $key,
                $opp
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
        $callback = function ($msg) // GET DATA FROM MSG QUEUE
        {
            $data = json_decode($msg->body); // DECODE TO RECIEVED MSG ARRAY
            echo "Recieved Data : ";
            var_dump($msg->body);
            echo "Data Sent to Response Queue ... \n";
            
            // INSERT DATA
            if ($data[0][3] == 1) // CHECKING OPP CODE 1 FOR INSERT
            {
                echo "Request for insert record ... \n";
                $user = new User();
                $user->setPassword($data[0][1]);
                $user->setName($data[0][0]);
                $this->getDm()->persist($user);
                $this->getDm()->flush();
                $uu = $user->getId();
                $data[0][] = $uu; // PUSH ID OF INSERTED RECORD TO ARRAY
                echo "Data Saved ... \n";
            }
            
            if ($data[0][3] == 0) // CHECKING OPP CODE 0 FOR LOGIN
            {
                echo "Request for authentication ... \n";
                $exists = $this->getDm()
                    ->createQueryBuilder('App\Document\User')
                    ->field('name')
                    ->equals($data[0][6])
                    ->field('password')
                    ->equals($data[0][7])
                    ->count()
                    ->getQuery()
                    ->execute();
                if ($exists) {
                    echo "Credentials Authenticated ... \n";
                    $data[0][] = $data[0][6].$data[0][7]; // PUSH KEY TO array at 8
                } 
                else {
                    $data[0][] = "false"; // PUSH KEY TO array at 8
                    echo "Incorrect Credentials \n";
                }
            }
            if ($data[0][3] == 2) // CHECKING OPP CODE 1 FOR DELETE
{
                echo "Request for delete ... \n";
                $delId = $data[0][4];
                $this->getDm()
                    ->createQueryBuilder('App\Document\User')
                    ->remove()
                    ->field('_id')
                    ->equals(new \MongoId($delId))
                    ->getQuery()
                    ->execute();
                echo "Record Deleted ... \n";
            }
            if ($data[0][3] == 3) // CHECKING OPP CODE 1 FOR UPDATE
{
                echo "Request for update ... \n";
                $this->getDm()
                    ->createQueryBuilder('App\Document\User')
                    ->update()
                    ->field('name')
                    ->set($data[0][5])
                    ->field('_id')
                    ->equals($data[0][4])
                    ->getQuery()
                    ->execute();
                echo "Record updated ... \n";
            }
            
            // var_dump($data);
            $data = json_encode($data);
            var_dump($data);
            
            $connection = new AMQPConnection('localhost', 5672, 'guest', 'guest');
            $channel = $connection->channel();
            $msg1 = new AMQPMessage($data);
            $channel->basic_publish($msg1, '', 'dataQ');
            $channel->close();
            $connection->close();
        };
        
        $channel->basic_consume('MsgQueue', '', false, true, false, false, $callback);
        while (count($channel->callbacks)) 
        { 
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

    public function compareAction()
    {
        $user_session = new Container('user');
        print_r($user_session->readykey);
        print_r($user_session->responsekey);
        if ($user_session->responsekey == $user_session->readykey) {
            $arr = array(
                "auth" => "true"
            );
        } else {
            $user_session->username = "";
            $arr = array(
                "auth" => "false"
            );
           
        }
        $viewModel = new ViewModel(array(
            'data' => $arr
        ));
        $viewModel->setTerminal(true);
        return $viewModel;
    }
}
