<?php
namespace Profile\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\Document(collection="academic")
 */
class Academic
{


    /**
     * @ODM\Id
     */
    private $id;

    /**
     * @ODM\Field(type="string")
     */
    private $city;

    /**
     * @ODM\Field(type="string")
     */
    private $institue;

    /**
     *
     * @return the $city
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
     * @return the $institue
     *
     */
    public function getInstitue()
    {
        return $this->institue;
    }

    /**
     *
     * @param field_type $institute
     */
    public function setInstitue($institue)
    {
        $this->institue = $institue;
    }

    /**
     *
     * @return the $city
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     *
     * @param field_type $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }
}



