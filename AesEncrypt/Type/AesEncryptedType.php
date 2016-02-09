<?php
/*
 * This file is a part of Trill - Landing project.
 *
 * @author Alexandr Viniychuk <a@viniychuk.com>
 * created: 11:29 AM 1/20/16
 */

namespace Youshido\DoctrineExtensionBundle\AesEncrypt\Type;


use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Youshido\DoctrineExtensionBundle\AesEncrypt\Service\EncryptService;

class AesEncryptedType extends Type
{
    const NAME = "aes_encrypted";


    public function getKey()
    {
        return EncryptService::getKey();
    }

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        $length = empty($fieldDeclaration['length']) ? $this->getDefaultLength($platform) : $fieldDeclaration['length'];

        return $length > 255 ? "BLOB" : "VARBINARY(" . $length . ")";
    }

    public function getDefaultLength(AbstractPlatform $platform)
    {
        return 255;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return parent::convertToDatabaseValue($value, $platform);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return parent::convertToPHPValue($value, $platform);
    }


    public function convertToDatabaseValueSQL($sqlExpr, AbstractPlatform $platform)
    {
        $sql = parent::convertToDatabaseValueSQL($sqlExpr, $platform);

        return 'AES_ENCRYPT(' . $sql . ', "' . $this->getKey() . '")';
    }


    public function convertToPHPValueSQL($sqlExpr, $platform)
    {
        $sql = parent::convertToPHPValueSQL($sqlExpr, $platform);

        return 'AES_DECRYPT(' . $sql . ', "' . $this->getKey() . '")';
    }


    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return true;
    }

    public function getName()
    {
        return self::NAME;
    }


}