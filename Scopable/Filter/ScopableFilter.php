<?php
/*
* This file is a part of landing project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 1/12/16 8:57 PM
*/

namespace Youshido\DoctrineExtension\Scopable\Filter;


use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;
use Youshido\DoctrineExtension\Scopable\Annotation\Scopable;
use Youshido\DoctrineExtension\Scopable\ScopableSubscriber;

class ScopableFilter extends SQLFilter
{

    protected $listener;
    protected $entityManager;

    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
    {
        $class  = $targetEntity->getName();

        /** @var Scopable $config */
        $config = $this->getListener()->getConfiguration($this->getEntityManager(), $targetEntity->name);
        if (!$config) return '';
        $res = '';

        try {
            $clauses = [];
            if ($config->getFields()) {
                foreach ($config->getFields() as $field) {
                    $paramName = substr($field, 0, -3);
                    if (!$this->getParameter($paramName) || ($this->getParameter($paramName) == "''")) continue;

                    $clauses[] = $targetTableAlias . '.' . $field . ' = ' . $this->getParameter($paramName);
                }
            }
            $res = implode(' AND ', $clauses);
        } catch (\Exception $e) {
            echo "problem with " . $class .' '.$paramName.' ';
        }

        return $res;
    }

    protected function getListener()
    {
        if ($this->listener === null) {
            $em  = $this->getEntityManager();
            $evm = $em->getEventManager();

            foreach ($evm->getListeners() as $listeners) {
                foreach ($listeners as $listener) {
                    if ($listener instanceof ScopableSubscriber) {
                        $this->listener = $listener;

                        break 2;
                    }
                }
            }

            if ($this->listener === null) {
                throw new \RuntimeException('Listener "ScopableListener" was not added to the EventManager!');
            }
        }

        return $this->listener;
    }

    /**
     * Hack for private entity manager
     */
    protected function getEntityManager()
    {
        if ($this->entityManager === null) {
            $refl = new \ReflectionProperty('Doctrine\ORM\Query\Filter\SQLFilter', 'em');
            $refl->setAccessible(true);
            $this->entityManager = $refl->getValue($this);
        }

        return $this->entityManager;
    }
}