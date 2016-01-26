<?php

namespace Youshido\DoctrineExtensionBundle;

use Doctrine\DBAL\Types\Type;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Youshido\DoctrineExtensionBundle\AesEncrypt\Type\AesEncryptedType;
use Youshido\DoctrineExtensionBundle\Scopable\Filter\ScopableFilter;

class YoushidoDoctrineExtensionBundle extends Bundle
{

    public function boot()
    {
        parent::boot();
        $config = $this->container->getParameter('youshido_doctrine_extension_config');
        $em = $this->container
            ->get('doctrine.orm.entity_manager');

        // scopable
        if (!empty($config[ScopableFilter::NAME]) && $config[ScopableFilter::NAME]) {
            $em->getConfiguration()
               ->addFilter(ScopableFilter::NAME, 'Youshido\DoctrineExtensionBundle\Scopable\Filter\ScopableFilter');
            $em->getFilters()->enable(ScopableFilter::NAME);
        }

//        $configuration
//            ->setDefaultQueryHint(
//                Query::HINT_CUSTOM_OUTPUT_WALKER,
//                'Youshido\DoctrineExtensionBundle\AesEncrypt\Walker\EncryptWalker'
//            );
        // aes_encrypt
        if (!empty($config[AesEncryptedType::NAME]) && $config[AesEncryptedType::NAME]) {
            Type::addType(AesEncryptedType::NAME, 'Youshido\DoctrineExtensionBundle\AesEncrypt\Type\AesEncryptedType');
            $this->container->get('aes_encrypt_service')->setKey($config[AesEncryptedType::NAME]['key']);
            $em->getConnection()
               ->getDatabasePlatform()
               ->registerDoctrineTypeMapping(AesEncryptedType::NAME, 'aes_encrypted');

        }
    }

}
