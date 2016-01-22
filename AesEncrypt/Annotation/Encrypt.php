<?php
/*
* This file is a part of landing project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 1/12/16 10:28 PM
*/

namespace Youshido\DoctrineExtensionBundle\AesEncrypt\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Annotation\Target({"CLASS"})
 */
class Encrypt
{

    /**
     * @var array<string>
     */
    private $fields;


    /**
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }

    public function __construct($options)
    {
        if (!empty($options['fields'])) {
            $this->fields = $options['fields'];
        }
    }



}