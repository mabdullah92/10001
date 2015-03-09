<?php

namespace App\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use App\Form\addForm;
use Zend\View\Model\ViewModel;
use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Zend\Session\Container;

class AppController extends AbstractActionController
{
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
    //CHECK IF LOGGED IN
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

    //Index Action is Still Unused
    public function indexAction(){
        $form = new addForm();
        return new ViewModel(array(
            'form' => $form
        ));}

    //Basic Login Operation
    public function loginAction()
    {
        if (isset($_POST['login_name'])) {
            $login_user = $this->getReq()->getPost('login_name');
            $data=$this->getReq()->getPost();
            $user_session = new Container('user');
            $user_session->username = $login_user;
            $this->sendMsg($data);
        }
        return $this->getResponse();
    }
    //send to producer the received post
    public function submitAction(){
        if($this->isValid()=="false"){
            echo "false";
        }
        else{
            $data = $this->getReq()->getPost();
            $data["loginAuth"]=$this->isValid();
            $this->sendMsg($data);
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
            echo "Received Data : ";
            var_dump($msg->body);
            $data = json_decode($msg->body,true); // DECODE TO RECEIVED MSG ARRAY

            //logic for calling respective model
            $class='App\Model\\'.$data["tableName"].'Model';
            $method=$data["operation"];
            $model = new $class();
            $data=$model->$method($this->getDm(),$data);
            //end here
            echo "Data Sent to Response Queue ... \n";
            //Send Response to dataQ for node js Service
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
