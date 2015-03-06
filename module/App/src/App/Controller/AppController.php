<?php

namespace App\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use App\Form\addForm;
use App\Document\User;
use Zend\View\Model\ViewModel;
use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Zend\Session\Container;
use Zend\View\Model\JsonModel;
use App\Model\userModel;

class AppController extends AbstractActionController
{
    
    /*
     * OPERATION CODE REFERENCE
     * LOGIN : 0
     * INSERT: 1
     * DELETE: 2
     * UPDATE: 3
     * SEARCH: 4
     */
    public function init() {}
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
    private function sendMsg($data)
    {
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
    //Perform signed In validation
    private function  isValid()
    {
        $user_session = new Container('user');
        $username=$user_session->username;
        if( $username !== null)
        {
            return $username;
        }
        else
        {
            return "false";
        }
    }

    //Index Action is Still Unused
    public function indexAction(){ }

    //For Basic layout of Application
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

    //Basic Login Operation
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
                "opp"=>  $opp,
                "loginU"=> $login_user,
                "loginP"=> $login_pwd
            );
            $user_session = new Container('user');
            //$user_session->readykey = $login_user . $login_pwd;
            $user_session->username = $login_user;
            $this->sendMsg($data);

        }

        return $viewModel;
    }

    //CHECK IF LOGED IN
    public function isloggedinAction(){
        echo $this->isValid();
        return $this->getResponse();
    }
    //logout
    public function logoutAction(){
        $user_session = new Container('user');
        $user_session->username = null;
        echo "logout";
        return $this->getResponse();
    }

    //populates the initial shown grid using knockout
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

    //Sends Insert Request along with data
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
            $data=null;
            $data[0]=array("key"=>$key,
                "opp"=>$opp,
                "loginAuth"=>$this->isValid());
            $data[1] = array(
                "name"=>$name,
                "pwd"=>$pwd,
            );
            $this->sendMsg($data);
        }
        return array(
            'form' => $form
        );
    }

    //Sets update fields
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

    //Send edit Request to consumer along with updated params and Id
    public function editAction()
    {
        if (isset($_POST['id'])) // or use $_POST['username'] for specific
        {
            $new = $this->getReq()->getPost('name');
            $pwd = $this->getReq()->getPost('password');
            $eid = $this->getReq()->getPost('id');
            $opp = 3; // OPPCODE THREE FOR UPDATE
            $data[] = array(
                "opp"=>  $opp,
                "editId"=>$eid,
                "newName"=>$new,
                "pwd"=>$pwd
            );

            $this->sendMsg($data);
        }

        $viewModel = new ViewModel(array(
            'foo' => 'bar'
        ));
        $viewModel->setTerminal(true);
        return $viewModel;
    }

    //Sends Delete Request with delete Id
    public function deleteAction()
    {
        if (isset($_POST['delId'])) {

            $delId = $this->getReq()->getPost('delId');
            $opp = 2;
            $data[] = array(
                "opp"=>$opp,
                "dellId"=> $delId
            );
            $this->sendMsg($data);
        }
        $viewModel = new ViewModel(array(
            'foo' => 'bar'
        ));

        $viewModel->setTerminal(true);
        return $viewModel;
    }

    //search Action
    public function searchAction() {

        if($this->isValid()=="false"){
            echo "false";
        }
        else
        {
            if (isset($_POST['search'])) {

                $search = $this->getReq()->getPost('search');
                $opp = 4;
                $data[] = array(
                    "loginAuth"=>$this->isValid(),
                    "opp"=>$opp,
                    "search"=> $search
                );
                $this->sendMsg($data);
            }

        }
        return $this->getResponse();
    }

    //Main Consumer for processing queue data
    public function consAction()
    {
        echo "Listening ... \n";
        $connection = new AMQPConnection('localhost', 5672, 'guest', 'guest');
        $channel = $connection->channel();
        $channel->queue_declare('MsgQueue', false, false, false, false);
        $callback = function ($msg) // GET DATA FROM MSG QUEUE
        {
            $method = new userModel();
            $data = json_decode($msg->body,true); // DECODE TO RECEIVED MSG ARRAY
            echo "Received Data : ";
            var_dump($msg->body);
            echo "Data Sent to Response Queue ... \n";

            //Login and Update, Delete, Insert Start here
            if ($data[0]["opp"] == 0) // CHECKING OPP CODE 0 FOR LOGIN
            {
                echo "Request for authentication ... \n";
                $exists = $method->findC($this->getDm(),$data);
                if ($exists) {
                    echo "Credentials Authenticated ... \n";
                    $qb = $this->getDm()
                        ->createQueryBuilder('App\Document\User')
                        ->getQuery()
                        ->execute();
                    foreach ($qb as $row){
                        $arr[]=array("name"=>$row->getName(),"pwd"=>$row->getPassword(),"id"=>$row->getId());
                    }
                    $json = json_encode($arr);
                    $data[0]["opp"]=0;
                    $data[0]["loginAuth"] = $data[0]["loginU"]; // PUSH KEY TO array at 8
                    $data[1]["data"]=$arr;
                    $data[2]["cols"]=array("id","name","password");
                }
                else {
                    $data[0]["loginAuth"] = "false"; // PUSH KEY TO array at 8
                    echo "Incorrect Credentials \n";
                }
            }
            if ($data[0]["opp"] == 1) // CHECKING OPP CODE 1 FOR INSERT
            {
                
                $data= $method->insertC($this->getDm(),$data);
            }
            if ($data[0]["opp"] == 2) // CHECKING OPP CODE 1 FOR DELETE
            {
                echo "Request for delete ... \n";
                $data= $method->deleteC($this->getDm(),$data);
                echo "Record Deleted ... \n";
            }
            if ($data[0]["opp"] == 3) // CHECKING OPP CODE 1 FOR UPDATE
            {
                echo "Request for update ... \n";
                $data= $method->updateC($this->getDm(),$data);
                echo "Record updated ... \n";
            }
            if ($data[0]["opp"] == 4) // CHECKING OPP CODE 1 FOR SEARCH
            {
                echo "Request for search ... \n";
                $data= $method->searchC($this->getDm(),$data);
                echo "Return Records ... \n";
               
            }

            $data = json_encode($data);
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

}
