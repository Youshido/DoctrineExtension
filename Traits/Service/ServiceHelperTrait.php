<?php
/**
 * Date: 10.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\DoctrineExtensionBundle\Traits\Service;


use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Youshido\DoctrineExtensionBundle\Traits\SoftDeletableTrait;

/**
 * @property-read ContainerInterface $container
 */
trait ServiceHelperTrait
{

    /**
     * @param bool|true $throwException
     *
     * @return null|object
     *
     * @throws \Exception
     */
    public function getUser($throwException = true)
    {
        /** @var TokenInterface $token */
        $token = $this->getContainer()->get('security.token_storage')->getToken();

        if ($token) {
            $user = $token->getUser();

            if (is_object($user)) {
                return $user;
            }
        }

        if ($throwException) {
            throw new \Exception('Access denied', 403);
        }

        return null;
    }

    /**
     *
     * @throws \Exception
     * @return ContainerInterface
     */
    public function getContainer()
    {
        if ($this->container) {
            return $this->container;
        }

        throw new \Exception('Container not set for class: ' . __CLASS__);
    }

    /**
     * @param null|string $objectName
     */
    public function clearCache($objectName = null)
    {
        $this->getDoctrine()->getManager()->clear($objectName);
        gc_collect_cycles();
    }

    /**
     * @return \Doctrine\Bundle\DoctrineBundle\Registry
     */
    public function getDoctrine()
    {
        return $this->container->get('doctrine');
    }

    public function flush()
    {
        $this->getDoctrine()->getManager()->flush();
    }

    /**
     * @param $entity object
     */
    public function remove($entity)
    {
        if (in_array('Youshido\DoctrineExtensionBundle\Traits\SoftDeletableTrait', class_uses($entity))) {
            /** @var $entity SoftDeletableTrait */
            $entity
                ->setDeleted(true)
                ->setDeletedAt(new \DateTime());

            $this->persist($entity);
        } else {
            $this->getDoctrine()->getManager()->remove($entity);
        }
    }

    /**
     * @param $entity object
     */
    public function persist($entity)
    {
        $this->getDoctrine()->getManager()->persist($entity);
    }

}