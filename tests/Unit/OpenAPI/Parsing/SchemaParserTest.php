<?php
/*
 * This file is part of Swagger Mock.
 *
 * (c) Igor Lazarev <strider2038@yandex.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Tests\Unit\OpenAPI\Parsing;

use App\Mock\Parameters\Schema\Schema;
use App\Mock\Parameters\Schema\Type\InvalidType;
use App\OpenAPI\Parsing\SchemaParser;
use App\OpenAPI\Parsing\SpecificationAccessor;
use App\OpenAPI\Parsing\SpecificationPointer;
use App\Tests\Utility\TestCase\ParsingTestCaseTrait;
use PHPUnit\Framework\TestCase;

class SchemaParserTest extends TestCase
{
    use ParsingTestCaseTrait;

    private const VALUE_TYPE = 'value_type';
    private const SCHEMA_DEFINITION = [
        'type' => self::VALUE_TYPE,
    ];
    private const VALID_SCHEMA = [
        'schema' => self::SCHEMA_DEFINITION,
    ];

    protected function setUp(): void
    {
        $this->setUpParsingContext();
    }

    /** @test */
    public function parsePointedSchema_validSchema_schemaCreatedByTypeParserFromLocator(): void
    {
        $parser = $this->createSchemaParser();
        $type = $this->givenInternalParser_parsePointedSchema_returnsObject();
        $specification = new SpecificationAccessor(self::VALID_SCHEMA);

        /** @var Schema $parsedSchema */
        $parsedSchema = $parser->parsePointedSchema($specification, new SpecificationPointer());

        $this->assertInternalParser_parsePointedSchema_wasCalledOnceWithSpecificationAndPointerPath(
            $specification,
            ['schema']
        );
        $this->assertSame($type, $parsedSchema->value);
    }

    /** @test */
    public function parsePointedSchema_invalidSchema_errorReported(): void
    {
        $parser = $this->createSchemaParser();
        $specification = new SpecificationAccessor([]);
        $reportMessage = $this->givenParsingErrorHandler_reportError_returnsMessage();

        /** @var Schema $parsedSchema */
        $parsedSchema = $parser->parsePointedSchema($specification, new SpecificationPointer());

        $this->assertInstanceOf(InvalidType::class, $parsedSchema->value);
        $this->assertSame($reportMessage, $parsedSchema->value->getError());
        $this->assertParsingErrorHandler_reportError_wasCalledOnceWithMessageAndPointerPath('Invalid schema', []);
    }

    private function createSchemaParser(): SchemaParser
    {
        return new SchemaParser($this->internalParser, $this->errorHandler);
    }
}
