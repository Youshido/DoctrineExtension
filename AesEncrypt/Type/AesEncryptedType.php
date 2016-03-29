<?php
/*
 * This file is a part of Trill - Landing project.
 *
 * @author Alexandr Viniychuk <a@viniychuk.com>
 * created: 11:29 AM 1/20/16
 */

namespace Youshido\DoctrineExtensionBundle\AesEncrypt\Type;


use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\PostgreSQL91Platform;
use Doctrine\DBAL\Platforms\PostgreSQL92Platform;
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

        if ($this->isPostgreSQL($platform)) {
            return 'BYTEA';
        }

        return $length > 1000 ? "MEDIUMBLOB" : "VARBINARY(" . $length . ")";
    }

    public function getDefaultLength(AbstractPlatform $platform)
    {
        return 255;
    }

    private function isPostgreSQL($platform)
    {
        return $platform instanceof PostgreSQL92Platform || $platform instanceof PostgreSQL91Platform;
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

        if ($this->isPostgreSQL($platform)) {
            return 'encrypt(' . $sql . ', \'' . $this->getKey() . '\', \'aes\')';
        }

        return 'AES_ENCRYPT(' . $sql . ', "' . $this->getKey() . '")';
    }


    public function convertToPHPValueSQL($sqlExpr, $platform)
    {
        $sql = parent::convertToPHPValueSQL($sqlExpr, $platform);

        if ($this->isPostgreSQL($platform)) {
            return 'encode(decrypt(' . $sql . ', \'' . $this->getKey() . '\', \'aes\'), \'escape\')';
        }

        return 'CAST(AES_DECRYPT(' . $sql . ', "' . $this->getKey() . '") AS CHAR)';
    }

    public function canRequireSQLConversion()
    {
        return true;
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