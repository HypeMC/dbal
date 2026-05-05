<?php

declare(strict_types=1);

namespace Doctrine\DBAL\Tests\Functional\Schema\MySQL;

use Doctrine\DBAL\Platforms\AbstractMySQLPlatform;
use Doctrine\DBAL\Schema\ColumnEditor;
use Doctrine\DBAL\Tests\Functional\Schema\AlterColumnCollationTestCase;
use Doctrine\DBAL\Types\Types;

final class AlterColumnCollationTest extends AlterColumnCollationTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if ($this->connection->getDatabasePlatform() instanceof AbstractMySQLPlatform) {
            return;
        }

        self::markTestSkipped('Test is for MySQL.');
    }

    /** {@inheritDoc} */
    public static function provideAlterColumnCollationCases(): iterable
    {
        yield 'utf8mb4_general_ci to utf8mb4_general_ci' => [
            'utf8mb4_general_ci',
            static function (ColumnEditor $editor): void {
                $editor->setCollation('utf8mb4_general_ci');
            },
            true,
            'utf8mb4_general_ci',
        ];

        yield 'utf8mb4_general_ci to utf8mb4_bin' => [
            'utf8mb4_general_ci',
            static function (ColumnEditor $editor): void {
                $editor->setCollation('utf8mb4_bin');
            },
            false,
            'utf8mb4_bin',
        ];

        yield 'utf8mb4_bin to utf8mb4_general_ci' => [
            'utf8mb4_bin',
            static function (ColumnEditor $editor): void {
                $editor->setCollation('utf8mb4_general_ci');
            },
            false,
            'utf8mb4_general_ci',
        ];

        yield 'utf8mb4_general_ci to implicit default' => [
            'utf8mb4_general_ci',
            static function (ColumnEditor $editor): void {
                $editor->setCollation(null);
            },
            false,
            'utf8mb4_0900_ai_ci',
        ];

        yield 'collated string to non-collatable type' => [
            'utf8mb4_general_ci',
            static function (ColumnEditor $editor): void {
                $editor
                    ->setTypeName(Types::INTEGER)
                    ->setLength(null)
                    ->setCollation(null);
            },
            false,
            null,
        ];

        yield 'length change preserves explicit collation' => [
            'utf8mb4_bin',
            static function (ColumnEditor $editor): void {
                $editor->setLength(100);
            },
            false,
            'utf8mb4_bin',
        ];
    }
}
