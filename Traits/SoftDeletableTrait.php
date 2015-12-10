<?php
/**
 * Date: 10.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\DoctrineExtensionBundle\Traits;


trait SoftDeletableTrait
{

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deletedAt", type="datetime", nullable=true)
     */
    private $deletedAt;

    /**
     * @var boolean
     *
     * @ORM\Column(name="deleted", type="boolean")
     */
    private $deleted = false;

    /**
     * @return \DateTime
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    /**
     * @param \DateTime $deletedAt
     *
     * @return SoftDeletableTrait
     */
    public function setDeletedAt($deletedAt)
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isDeleted()
    {
        return $this->deleted;
    }

    /**
     * Get deleted
     *
     * @return boolean
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * @param boolean $deleted
     *
     * @return SoftDeletableTrait
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;

        return $this;
    }


}