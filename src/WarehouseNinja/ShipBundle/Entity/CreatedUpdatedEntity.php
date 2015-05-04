<?php

namespace WarehouseNinja\ShipBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\MappedSuperclass
 */
class CreatedUpdatedEntity
{
    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="last_updated", type="datetime")
     */
    protected $lastUpdated;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="date_created", type="datetime")
     */
    protected $dateCreated;


    /**
     * Set lastUpdated
     *
     * @param \DateTime $updated
     * @return $this
     */
    public function setLastUpdated($updated)
    {
        $this->lastUpdated = $updated;

        return $this;
    }

    /**
     * Get lastUpdated
     *
     * @return \DateTime
     */
    public function getLastUpdated()
    {
        return $this->lastUpdated;
    }

    /**
     * Set dateCreated
     *
     * @param \DateTime $created
     * @return $this
     */
    public function setDateCreated($created)
    {
        $this->dateCreated = $created;

        return $this;
    }

    /**
     * Get dateCreated
     *
     * @return \DateTime
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

}
