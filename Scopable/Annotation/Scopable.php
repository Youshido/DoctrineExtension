<?php
/*
* This file is a part of landing project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 1/12/16 10:28 PM
*/

namespace Youshido\DoctrineExtension\Scopable\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Annotation\Target({"CLASS"})
 */
class Scopable
{

    /**
     * @var array<string>
     */
    private $fields;

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

    /**
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }

    public function __construct($options)
    {
        if (!empty($options['models'])) {
            $this->models = $options['models'];
        }
        if (!empty($options['fields'])) {
            $this->fields = $options['fields'];
        }
    }



}