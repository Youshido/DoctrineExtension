<?php

namespace Youshido\DoctrineExtensionBundle;

use Doctrine\DBAL\Types\Type;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class YoushidoDoctrineExtensionBundle extends Bundle
{

    public function boot()
    {
        parent::boot();
        $em = $this->container
            ->get('doctrine.orm.entity_manager');

        $em->getConfiguration()
           ->addFilter('scopable', 'Youshido\DoctrineExtensionBundle\Scopable\Filter\ScopableFilter');
        $em->getFilters()->enable('scopable');
//        $configuration
//            ->setDefaultQueryHint(
//                Query::HINT_CUSTOM_OUTPUT_WALKER,
//                'Youshido\DoctrineExtensionBundle\AesEncrypt\Walker\EncryptWalker'
//            );
        Type::addType('aes_encrypted', 'Youshido\DoctrineExtensionBundle\AesEncrypt\Type\AesEncryptedType');
        $this->container->get('aes_encrypt_service')->setKey();
        $em->getConnection()
           ->getDatabasePlatform()
           ->registerDoctrineTypeMapping('aes_encrypted', 'aes_encrypted');
    }

}
