<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Tests;

use Arokettu\Uuid\Node\RandomNode;
use Arokettu\Uuid\Node\RawNode;
use PHPUnit\Framework\TestCase;
use Random\Engine\Xoshiro256StarStar;
use Random\Randomizer;

class NodesTest extends TestCase
{
    public function testRandomNode(): void
    {
        $node = new RandomNode(new Randomizer(new Xoshiro256StarStar(123)));

        self::assertEquals('f969a0d1a18f', $node->getHex());
        self::assertEquals('5f4d6d65c7e3', $node->getHex());
        self::assertEquals('2fa6f2c3462b', $node->getHex());
    }

    public function testRawNode(): void
    {
        $node = new RawNode('222222222222'); // constructor accepts unset bit
        self::assertEquals('222222222222', $node->getHex());

        $node = new RawNode('1234567890ab'); // what goes in, goes out
        self::assertEquals('1234567890ab', $node->getHex());

        $node = RawNode::fromHex('aBcDeF000000'); // allows mixed case
        self::assertEquals('abcdef000000', $node->getHex());

        $node = RawNode::fromBytes('ABCDEF'); // 6 bytes
        self::assertEquals('414243444546', $node->getHex());
    }

    public function testRawNodeFormatEnforced(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('$hex must be 12 lowercase hexadecimal digits');
        new RawNode('1234567890AB');
    }

    public function testRawNodeFromHexFormatEnforced(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('$hex must be 12 hexadecimal digits');
        RawNode::fromHex('must be hex!');
    }

    public function testRawNodeFromBytesLengthEnforced(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('$bytes must be 6 bytes');
        RawNode::fromBytes('123');
    }
}
