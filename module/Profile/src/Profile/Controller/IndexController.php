<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/Profile for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Profile\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Profile\Document\Academic;
use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;

class IndexController extends AbstractActionController
{

    private function getDm()
    {
        $dm = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        
        return $dm;
    }

    private function sendMsg($data)
    {
        $data = json_encode($data);
        $connection = new AMQPConnection('localhost', 5672, 'guest', 'guest');
        $channel = $connection->channel();
        $msg = new AMQPMessage($data);
        $channel->basic_publish($msg, '', 'dataQ');
        $channel->close();
        $connection->close();
    }

    public function indexAction()
    {
        return array();
    }

    public function fooAction()
    {
        // This shows the :controller and :action parameters in default route
        // are working when you browse to /index/index/foo
        return array();
    }

    public function insertAction()
    {
        $academic = new Academic();
        $academic->setCity("new");
        $academic->setInstitue("new");
        $this->getDm()->persist($academic);
        $this->getDm()->flush();
        return $this->getResponse();
    }

    public function gridAction()
    {      
        $qb = $this->getDm()
            ->createQueryBuilder('Profile\Document\Academic')
            ->refresh()
            ->getQuery()
            ->execute();
        $colhead = array(
            "id",
            "city",
            "institute"
        );
        foreach ($qb as $row) {
            $arr[] = array(
                "id" => $row->getId(),
                "city" => $row->getCity(),
                "institute" => $row->getInstitue()
            );
            // var_dump($arr);
        }
        $data = null;
        $data[0]["opp"] = 4;
        $data[0]["loginAuth"] = 'ali';
        $data[1]["data"] = $arr;
        $data[2]["cols"] = $colhead;
        $data[2]["tableName"] = "academic";
        $this->sendMsg($data);
        return $this->getResponse();
    }
}
