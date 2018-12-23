<?php
/*
 * This file is part of Swagger Mock.
 *
 * (c) Igor Lazarev <strider2038@yandex.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Mock\Generation\Value\Combined;

use App\Mock\Generation\Value\ValueGeneratorInterface;
use App\Mock\Generation\ValueGeneratorLocator;
use App\Mock\Parameters\Schema\Type\Combined\OneOfType;
use App\Mock\Parameters\Schema\Type\TypeMarkerInterface;

/**
 * @author Igor Lazarev <strider2038@yandex.ru>
 */
class OneOfValueGenerator implements ValueGeneratorInterface
{
    /** @var ValueGeneratorLocator */
    private $generatorLocator;

    public function __construct(ValueGeneratorLocator $generatorLocator)
    {
        $this->generatorLocator = $generatorLocator;
    }

    public function generateValue(TypeMarkerInterface $type)
    {
        $generatingType = $this->getRandomInternalType($type);
        $generator = $this->generatorLocator->getValueGenerator($generatingType);

        return $generator->generateValue($generatingType);
    }

    private function getRandomInternalType(OneOfType $type): TypeMarkerInterface
    {
        $typesCount = $type->types->count();
        $typeIndex = random_int(0, $typesCount - 1);

        return $type->types[$typeIndex];
    }
}
