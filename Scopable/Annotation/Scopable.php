<?php
/*
* This file is a part of landing project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 1/12/16 10:28 PM
*/

namespace Youshido\DoctrineExtensionBundle\Scopable\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Annotation\Target({"CLASS"})
 */
class Scopable
{

    /**
     * @var array<\Doctrine\ORM\Mapping\Entity>
     */
    private $models;


    /**
     * @return array
     */
    public function getModels()
    {
        return $this->models;
    }

    public function __construct($options)
    {
        if (!empty($options['models'])) {
            $this->models = $options['models'];
        }
    }

}