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

class AesBase extends ContainerAwareCommand
{


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em     = $this->getContainer()
                       ->get('doctrine.orm.entity_manager');
        $entity = $em
            ->getClassMetadata($input->getArgument('entity'));
        if ($entity) {
            $column = $entity->getColumnName($input->getArgument('field'));
            if (!$column) {
                throw new \Exception('There is no such column [' . $input->getArgument('field').'] for entity '.$input->getArgument('entity'));
            }
            $config = $this->getContainer()->getParameter(Configuration::KEY);
            if ($this->testData($config, $entity, $column)) {
                $sql    = $this->getSQL($config, $entity, $column);
            $em->getConnection()->executeQuery($sql);
                echo "Data conversion is done. \n\r";
            } else {
                echo "Your key is invalid or data is already ". ($this->getName() == "aes:encrypt" ? "encrypted" : "decrypted") . ". \n\r";
            }
        } else {
            throw new \Exception('There is no such entity: ' . $input->getArgument('entity'));
        }
    }

    protected function testDecryptData($config, ClassMetadata $entity, $column)
    {
        $sql    = 'SELECT AES_DECRYPT(`' . $column . '`, "' . $config[AesEncryptedType::NAME]['key'] . '") as `'.$column.'`'
                  .'FROM `' . $entity->getTableName() . '` LIMIT 1';
        $data = $this->getContainer()->get('doctrine.orm.entity_manager')->getConnection()->executeQuery($sql);
        return !empty($data->fetchColumn(0));
    }

    protected function testData($config, ClassMetadata $entity, $column)
    {
        return false;
    }

    protected function getSQL($config, ClassMetadata $entity, $column)
    {
        return "";
    }


}