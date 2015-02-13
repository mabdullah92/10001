<?php
namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\EmbeddedDocument
 */
class Child
{

    /**
     * @ODM\Id
     */
    private $id;

    /**
     * @ODM\Field(type="string")
     */
    private $child;

    public function __construct($child)
    {
        $this->child = (string) $child;
    }

    /**
     *
     * @return the $id
     *        
     */
    public function getChildId()
    {
        return $this->id;
    }

    /**
     *
     * @return the $name
     */
    public function getChildName()
    {
        return $this->child;
    }

    /**
     *
     * @param field_type $id            
     */
    public function setChildId($id)
    {
        $this->id = $id;
    }

    /**
     *
     * @param field_type $name            
     */
    public function setChildName($name)
    {
        $this->child = $name;
    }
}

/**
 * @ODM\Document(collection="user")
 */
class User
{

    /**
     * @ODM\EmbedOne(targetDocument="child")
     */
    private $child;

    /**
     * @ODM\Id
     */
    private $id;

    /**
     * @ODM\Field(type="string")
     */
    private $name;

    /**
     * @ODM\Field(type="string")
     */
    private $password;

    /**
     *
     * @return the $id
     *        
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     *
     * @param field_type $id            
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     *
     * @return the $password
     *        
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     *
     * @param field_type $password            
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     *
     * @return the $name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     *
     * @param field_type $name            
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     *
     * @param field_type $child            
     */
    public function setChildName($name)
    {
        $this->child = $name;
    }
}



