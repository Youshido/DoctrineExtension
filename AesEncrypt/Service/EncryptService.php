<?php
/*
* This file is a part of landing project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 1/21/16 12:32 AM
*/

namespace Youshido\DoctrineExtensionBundle\AesEncrypt\Service;


use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class EncryptService
{
    use ContainerAwareTrait;

    private static $encryptionKey;

    public function setKey($key)
    {
        self::$encryptionKey = $key;
    }

    public static function getKey()
    {
        return self::$encryptionKey;
    }

}