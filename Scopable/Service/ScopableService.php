<?php
/*
* This file is a part of landing project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 1/12/16 10:08 PM
*/

namespace Youshido\DoctrineExtensionBundle\Scopable\Service;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Youshido\DoctrineExtensionBundle\Scopable\Filter\ScopableFilter;

class ScopableService implements ContainerAwareInterface
{

    use ContainerAwareTrait;

    private $_data = [];

    public function setParameter($name, $value)
    {
        $this->_data[$name] = $value;
        $filters            = $this->container->get('doctrine')->getEntityManager()->getFilters();
        if ($filters->isEnabled(ScopableFilter::NAME)) {
            $filters->getFilter(ScopableFilter::NAME)->setParameter($name, $value);
        }

        return $this;
    }

    public function setParameters($params)
    {
        foreach ($params as $key => $value) {
            $this->setParameter($key, $value);
        }

        return $this;
    }

    public function getParameter($name, $default = null)
    {
        return array_key_exists($name, $this->_data) ? $this->_data['name'] : $default;
    }

    public function getParameters()
    {
        return $this->_data;
    }
}