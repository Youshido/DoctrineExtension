<?php
/*
* This file is a part of landing project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 1/12/16 10:08 PM
*/

namespace Youshido\ScopeBundle\Service;


use Symfony\Component\DependencyInjection\ContainerAware;

class ScopableService extends ContainerAware
{
    private $_data = [];

    public function setParameter($name, $value)
    {
        $this->_data[$name] = $value;
        $this->container->get('doctrine')
            ->getEntityManager()->getFilters()
            ->getFilter('scopable')->setParameter($name, $value);
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
        return array_key_exists($name, $this->_data)
            ? $this->_data['name']
            : $default;
    }

    public function getParameters()
    {
        return $this->_data;
    }
}