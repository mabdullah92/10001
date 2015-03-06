<?php
namespace App\Model;

use App\Document\User;

class userModel
{

    public function insertC($dm, $insert)
    {
        $data = $insert;
        $user = new User();
        $auth = $data[0]["loginAuth"];
        $user->setPassword($data[1]["pwd"]);
        $user->setName($data[1]["name"]);
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
            ->set($data[0]["newName"])
            ->field('password')
            ->set($data[0]["pwd"])
            ->field('_id')
            ->equals($data[0]["editId"])
            ->getQuery()
            ->execute();
        $dm->flush();
        return $data;
    }

    public function deleteC($dm, $delete)
    {
        $data = $delete;
        $delId = $data[0]["dellId"];
        $dm->createQueryBuilder('App\Document\User')
            ->remove()
            ->field('_id')
            ->equals(new \MongoId($delId))
            ->getQuery()
            ->execute();
        return $data;
    }

    public function searchC($dm, $search)
    {
        $data = $search;
        $search = $data[0]["search"];
        $auth = $data[0]["loginAuth"];
        // var_dump($auth);
        if ($search == null) {
            $arr = null;
            $qb = null;
            $qb = $dm->createQueryBuilder('App\Document\User')
                ->refresh()
                ->getQuery()
                ->execute();
        } else {
            $arr = null;
            $qb = null;
            $qb = $dm->createQueryBuilder('App\Document\User')
                ->field('name')
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
        $data[2]["cols"] = $colhead;
        return $data;
    }

    public function findC($dm, $find)
    {
        $data = $find;
        $data = $dm->createQueryBuilder('App\Document\User')
            ->field('name')
            ->equals($data[0]["loginU"])
            ->field('password')
            ->equals($data[0]["loginP"])
            ->count()
            ->getQuery()
            ->execute();
        return $data;
    }
}