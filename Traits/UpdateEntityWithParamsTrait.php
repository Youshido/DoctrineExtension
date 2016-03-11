<?php
/**
 * @author Serzh Yevtushenko <s.evtyshenko@gmail.com>
 * Date: 3/11/16
 */

namespace Youshido\DoctrineExtensionBundle\Traits;


use Symfony\Component\PropertyAccess\PropertyAccess;

Trait UpdateEntityWithParamsTrait
{

    public function updateEntityWithParams($fields, $params)
    {
        $p = PropertyAccess::createPropertyAccessor();

        foreach ($fields as $field) {
            if (isset($params[$field])) {
                $p->setValue($this, $field, $params[$field]);
            }
        }

        return $this;
    }
}