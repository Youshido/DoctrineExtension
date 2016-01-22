<?php
/*
* This file is a part of landing project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 1/12/16 9:18 PM
*/

namespace Youshido\DoctrineExtensionBundle\AesEncrypt;

use Doctrine\Common\EventArgs;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\ObjectManager;

class EncryptSubscriber implements EventSubscriber
{
    const ENCRYPT = 'Youshido\DoctrineExtensionBundle\AesEncrypt\Annotation\Encrypt';

    /**
     * Static List of cached object configurations
     * leaving it static for reasons to look into
     * other listener configuration
     *
     * @var array
     */
    protected static $configurations = array();
    /**
     * Custom annotation reader
     *
     * @var object
     */
    private $annotationReader;

    /**
     * @var \Doctrine\Common\Annotations\AnnotationReader
     */
    private static $defaultAnnotationReader;

    public function setAnnotationReader($reader)
    {
        $this->annotationReader = $reader;
    }

    public function getSubscribedEvents()
    {
        return [
            'loadClassMetadata',
        ];
    }

    public function loadClassMetadata(EventArgs $args)
    {
//        echo "loadClassMetadata \n";
    }

    public function getConfiguration(ObjectManager $objectManager, $class)
    {
        if (!isset(self::$configurations[$class])) {
            $factory = $objectManager->getMetadataFactory();
            $config = $factory->getMetadataFor($class);
            self::$configurations[$class] = $this->annotationReader->getClassAnnotation($config->reflClass, self::ENCRYPT);
        }

        return self::$configurations[$class];
    }

    protected function getNamespace()
    {
        return __NAMESPACE__;
    }


}