<?php
namespace App\Model;

use App\Document\User;

class userModel
{

    public function insertC($dm, $insert)
    {
        $data = $insert;
        $user = new User();
        $auth = $data["loginAuth"];
        $user->setPassword($data["add_pwd"]);
        $user->setName($data["username"]);
        $dm->persist($user);
        $dm->flush();
        $id = $user->getId();
        $name = $user->getName();
        $pwd = $user->getPassword();
        $colhead = array(
            "id",
            "name",
            "password"
        );
        $arr = array(
            "id" => $id,
            "name" => $name,
            "password" => $pwd
        );
        $data = null;
        $data[0]["loginAuth"] = $auth;
        $data[0]["opp"] = 1;
        $data["data"] = $arr; // PUSH ID OF INSERTED RECORD TO ARRAY
        $data[2]["cols"] = $colhead;
        return $data;
    }

    public function updateC($dm, $update)
    {
        $data = $update;
        $dm->createQueryBuilder('App\Document\User')
            ->update()
            ->field('name')
            ->set($data["name"])
            ->field('password')
            ->set($data["password"])
            ->field('_id')
            ->equals($data["id"])
            ->getQuery()
            ->execute();
        $dm->flush();
        return $data;
    }

    public function deleteC($dm, $delete)
    {
        $data = $delete;
        $delId = $data["delId"];
        $dm->createQueryBuilder('App\Document\User')
            ->remove()
            ->field('_id')
            ->equals(new \MongoId($delId))
            ->getQuery()
            ->execute();
        return $data;
    }

    public function searchC($dm, $s)
    {
        $data = $s;
        $search=null;
        $auth=null;
        if(array_key_exists("search",$data)) {
            $search = $data["search"];
        }
        if(array_key_exists("loginAuth",$data)) {
            $auth = $data["loginAuth"];
        }
        if ($search == null) {
            $arr = null;
            $qb = null;
            $qb = $dm->createQueryBuilder('App\Document\User')
                ->refresh()->skip(5)->limit(5)
                ->getQuery()
                ->execute();
        } else {
            $arr = null;
            $qb = null;
            $qb = $dm->createQueryBuilder('App\Document\User')
                ->field('name')->skip(5)->limit(5)
                ->equals(new \MongoRegex('/' . $search . ' */'))
                ->getQuery()
                ->execute();
        }
        $colhead = array(
            "id",
            "name",
            "password"
        );
        foreach ($qb as $row) {
            $arr[] = array(
                "id" => $row->getId(),
                "name" => $row->getName(),
                "password" => $row->getPassword()
            );
            // var_dump($arr);
        }
        $data = null;
        $data[0]["opp"] = 4;
        $data[0]["loginAuth"] = $auth;
        $data[1]["data"] = $arr;
        $data[2]["tableName"] = "user";
        $data[2]["cols"] = $colhead;
        return $data;
    }

    public function findC($dm, $find)
    {
        $data = $find;
        $data = $dm->createQueryBuilder('App\Document\User')
            ->field('name')
            ->equals($data["login_name"])
            ->field('password')
            ->equals($data["login_pwd"])
            ->count()
            ->getQuery()
            ->execute();

        $arr[0]["loginAuth"]=$find["login_name"];
        $arr[0]["opp"]=0;
        $arr[2]["tableName"]="user";
       // $arr=json_encode($arr);
        return $arr;
    }
}