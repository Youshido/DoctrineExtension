<?php
/*
 * This file is a part of Trill - Landing project.
 *
 * @author Alexandr Viniychuk <a@viniychuk.com>
 * created: 6:54 PM 1/25/16
 */

namespace Youshido\DoctrineExtensionBundle\Command;


use Doctrine\ORM\Mapping\ClassMetadata;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Youshido\DoctrineExtensionBundle\AesEncrypt\Type\AesEncryptedType;
use Youshido\DoctrineExtensionBundle\DependencyInjection\Configuration;

class AesDecryptCommand extends AesBase
{
    protected function configure()
    {
        $this->setName('aes:decrypt')
            ->setDescription('Decrypt selected entity\'s property')
            ->addArgument('entity', 1)
            ->addArgument('field', 1);
    }

    protected function testData($config, ClassMetadata $entity, $column)
    {
        return $this->testDecryptData($config, $entity, $column);
    }


    protected function getSQL($config, ClassMetadata $entity, $column)
    {
        $sql    = 'UPDATE `' . $entity->getTableName() . '`'
                  . ' SET `' . $column . '` = AES_DECRYPT(`' . $column . '`, "' . $config[AesEncryptedType::NAME]['key'] . '")'
        ;
        return $sql;
    }


}