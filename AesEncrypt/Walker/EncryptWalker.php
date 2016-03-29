<?php
/*
* This file is a part of landing project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 1/19/16 12:05 AM
*/

namespace Youshido\DoctrineExtensionBundle\AesEncrypt\Walker;


use Doctrine\ORM\Query\AST;
use Doctrine\ORM\Query\AST\FromClause;
use Doctrine\ORM\Query\AST\IdentificationVariableDeclaration;
use Doctrine\ORM\Query\AST\JoinAssociationDeclaration;
use Doctrine\ORM\Query\AST\RangeVariableDeclaration;
use Doctrine\ORM\Query\AST\SelectStatement;
use Doctrine\ORM\Query\ParserResult;
use Doctrine\ORM\Query\SqlWalker;
use Youshido\DoctrineExtensionBundle\AesEncrypt\Annotation\Encrypt;
use Youshido\DoctrineExtensionBundle\AesEncrypt\Service\EncryptService;
use Youshido\DoctrineExtensionBundle\AesEncrypt\EncryptSubscriber;

class EncryptWalker extends SqlWalker
{

    private $conn;
    private $em;
    private $query;
    private $queryComponents;
    protected $listener;
    private $rsm;
    private $quoteStrategy;
    private $platform;

    private $replacements = [];

    public function __construct($query, $parserResult, array $queryComponents)
    {
        parent::__construct($query, $parserResult, $queryComponents);
        $this->query           = $query;
        $this->queryComponents = $queryComponents;
        $this->rsm             = $parserResult->getResultSetMapping();
        $this->em              = $query->getEntityManager();
        $this->conn            = $this->em->getConnection();
        $this->platform        = $this->conn->getDatabasePlatform();
        $this->quoteStrategy   = $this->em->getConfiguration()->getQuoteStrategy();
    }

    public function walkSelectStatement(SelectStatement $AST)
    {
        foreach ($AST->fromClause as $items) {
            foreach ($items as $declaration) {
                /** @var IdentificationVariableDeclaration $declaration */
                $className = $declaration->rangeVariableDeclaration->abstractSchemaName;
                $class     = $this->em->getClassMetadata($declaration->rangeVariableDeclaration->abstractSchemaName);
                /** @var Encrypt $config */
                $config = $this->getListener()->getConfiguration($this->getEntityManager(), $className);
                if (!$config) continue;

                $tableAlias = $this->getSQLTableAlias($class->getTableName(), $declaration->rangeVariableDeclaration->aliasIdentificationVariable);

                $this->addReplacement([
                    'class'       => $className,
                    'aliasTable'  => $tableAlias,
                    'aliasEntity' => $declaration->rangeVariableDeclaration->aliasIdentificationVariable,
                    'fields'      => $config->getFields(),
                ]);
            }
        }

        $sql = parent::walkSelectStatement($AST);

        return $sql;
    }

    public function walkWhereClause($whereClause)
    {
        $sql = parent::walkWhereClause($whereClause);
        $sql = $this->applyReplacements($sql);

        return $sql;
    }

    public function addReplacement($object)
    {
        $this->replacements[] = $object;

        return $this;
    }

    public function walkSimpleSelectClause($simpleSelectClause)
    {
        $result = parent::walkSimpleSelectClause($simpleSelectClause);

        return $result;
    }

    protected function applyReplacements($sql)
    {
        foreach ($this->replacements as $replacement) {
            foreach ($replacement['fields'] as $field) {
                $sql = str_replace($replacement['aliasTable'] . '.' . $field,
                    'CAST(AES_DECRYPT(' . $replacement['aliasTable'] . '.' . $field . ', "' . EncryptService::getKey() . '") AS CHAR)',
                    $sql);
            }
        }

        return $sql;
    }

    protected function getListener()
    {
        if ($this->listener === null) {
            $em  = $this->getEntityManager();
            $evm = $em->getEventManager();

            foreach ($evm->getListeners() as $listeners) {
                foreach ($listeners as $listener) {
                    if ($listener instanceof EncryptSubscriber) {
                        $this->listener = $listener;

                        break 2;
                    }
                }
            }

            if ($this->listener === null) {
                throw new \RuntimeException('Listener "EncryptSubscriber" was not added to the EventManager!');
            }
        }

        return $this->listener;
    }

}