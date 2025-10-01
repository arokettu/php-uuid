<?php

/**
 * @copyright 2023 Anton Smirnov
 * @license MIT https://spdx.org/licenses/MIT.html
 */

declare(strict_types=1);

namespace Arokettu\Uuid\Tests;

use Arokettu\Uuid\Nodes\RandomNode;
use Arokettu\Uuid\Nodes\RawNode;
use Arokettu\Uuid\Nodes\StaticNode;
use PHPUnit\Framework\TestCase;
use Random\Engine\PcgOneseq128XslRr64;
use Random\Engine\Xoshiro256StarStar;
use Random\Randomizer;

final class NodesTest extends TestCase
{
    public function testRandomNode(): void
    {
        $node = new RandomNode(new Randomizer(new Xoshiro256StarStar(123)));

        self::assertEquals('f969a0d1a18f', $node->getHex());
        self::assertEquals('5f4d6d65c7e3', $node->getHex());
        self::assertEquals('2fa6f2c3462b', $node->getHex());
    }

    public function testRandomNodeToString(): void
    {
        $node = new RandomNode(new Randomizer(new Xoshiro256StarStar(123)));

        self::assertEquals('f9:69:a0:d1:a1:8f', $node->toString());
        self::assertEquals('5f:4d:6d:65:c7:e3', $node->toString());
        self::assertEquals('2f:a6:f2:c3:46:2b', $node->toString());
    }

    public function testRawNode(): void
    {
        $node = new RawNode('222222222222'); // constructor accepts unset bit
        self::assertEquals('222222222222', $node->getHex());
        self::assertEquals('22:22:22:22:22:22', $node->toString());

        $node = new RawNode('333333333333'); // set bit is fine too
        self::assertEquals('333333333333', $node->getHex());
        self::assertEquals('33:33:33:33:33:33', $node->toString());

        $node = new RawNode('1234567890ab'); // what goes in, goes out
        self::assertEquals('1234567890ab', $node->getHex());
        self::assertEquals('12:34:56:78:90:ab', $node->toString());

        $node = RawNode::fromHex('aBcDeF000000'); // allows mixed case
        self::assertEquals('abcdef000000', $node->getHex());
        self::assertEquals('ab:cd:ef:00:00:00', $node->toString());

        $node = RawNode::fromBytes('ABCDEF'); // 6 bytes
        self::assertEquals('414243444546', $node->getHex());
        self::assertEquals('41:42:43:44:45:46', $node->toString());
    }

    public function testRawNodeFormatEnforced(): void
    {
        $this->expectException(\DomainException::class);
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
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('$bytes must be 6 bytes');
        RawNode::fromBytes('123');
    }

    public function testStaticNode(): void
    {
        $node = new StaticNode('232222222222'); // only properly set bit
        self::assertEquals('232222222222', $node->getHex());
        self::assertEquals('23:22:22:22:22:22', $node->toString());

        $node = new StaticNode('0123456789ab'); // what goes in, goes out
        self::assertEquals('0123456789ab', $node->getHex());
        self::assertEquals('01:23:45:67:89:ab', $node->toString());

        $node = StaticNode::fromHex('aBcDeF000000'); // allows mixed case
        self::assertEquals('abcdef000000', $node->getHex());
        self::assertEquals('ab:cd:ef:00:00:00', $node->toString());

        $node = StaticNode::fromHex('222222222222'); // normalizes bit
        self::assertEquals('232222222222', $node->getHex());
        self::assertEquals('23:22:22:22:22:22', $node->toString());

        $node = StaticNode::fromBytes('ABCDEF'); // 6 bytes
        self::assertEquals('414243444546', $node->getHex());
        self::assertEquals('41:42:43:44:45:46', $node->toString());

        $node = StaticNode::fromBytes('FEDCBA'); // normalizes bit
        self::assertEquals('474544434241', $node->getHex());
        self::assertEquals('47:45:44:43:42:41', $node->toString());
    }

    public function testStaticNodeRandom(): void
    {
        $node = StaticNode::random(new Randomizer(new Xoshiro256StarStar(123))); // f969a0d1a18f5a32
        self::assertEquals('f969a0d1a18f', $node->getHex());

        // test bit normalizer in random mode
        $node = StaticNode::random(new Randomizer(new PcgOneseq128XslRr64(111))); // d2f8f1d31aaad01b
        self::assertEquals('d3f8f1d31aaa', $node->getHex()); // 2 becomes 3

        // random is random
        $node1 = StaticNode::random();
        $node2 = StaticNode::random();
        self::assertNotEquals($node1->getHex(), $node2->getHex()); // not guaranteed but pretty much expected
        self::assertEquals($node1->getHex(), $node1->getHex()); // but static stays static
    }

    public function testStaticNodeRandomDebugInfo(): void
    {
        $node = StaticNode::random(new Randomizer(new Xoshiro256StarStar(123))); // f969a0d1a18f5a32
        self::assertEquals(['mac' => 'f9:69:a0:d1:a1:8f'], $node->__debugInfo());
    }

    public function testStaticNodeBitEnforced(): void
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('The lowest bit of the first byte must be set for non-MAC nodes');
        new StaticNode('222222222222');
    }

    public function testStaticNodeFormatEnforced(): void
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('$hex must be 12 lowercase hexadecimal digits');
        new StaticNode('1234567890AB');
    }

    public function testStaticNodeFromHexFormatEnforced(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('$hex must be 12 hexadecimal digits');
        StaticNode::fromHex('must be hex!');
    }

    public function testStaticNodeFromBytesLengthEnforced(): void
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('$bytes must be 6 bytes');
        StaticNode::fromBytes('123');
    }
}
