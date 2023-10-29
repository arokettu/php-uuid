<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Tests;

use Arokettu\Uuid\Node\RandomNode;
use Arokettu\Uuid\Node\RawNode;
use Arokettu\Uuid\Node\StaticNode;
use PHPUnit\Framework\TestCase;
use Random\Engine\PcgOneseq128XslRr64;
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

        $node = new RawNode('333333333333'); // set bit is fine too
        self::assertEquals('333333333333', $node->getHex());

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

    public function testStaticNode(): void
    {
        $node = new StaticNode('232222222222'); // only properly set bit
        self::assertEquals('232222222222', $node->getHex());

        $node = new StaticNode('0123456789ab'); // what goes in, goes out
        self::assertEquals('0123456789ab', $node->getHex());

        $node = StaticNode::fromHex('aBcDeF000000'); // allows mixed case
        self::assertEquals('abcdef000000', $node->getHex());

        $node = StaticNode::fromHex('222222222222'); // normalizes bit
        self::assertEquals('232222222222', $node->getHex());

        $node = StaticNode::fromBytes('ABCDEF'); // 6 bytes
        self::assertEquals('414243444546', $node->getHex());

        $node = StaticNode::fromBytes('FEDCBA'); // normalizes bit
        self::assertEquals('474544434241', $node->getHex());
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

    public function testStaticNodeBitEnforced(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('The lowest bit of the first byte must be set for non-MAC nodes');
        new StaticNode('222222222222');
    }

    public function testStaticNodeFormatEnforced(): void
    {
        $this->expectException(\UnexpectedValueException::class);
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
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('$bytes must be 6 bytes');
        StaticNode::fromBytes('123');
    }
}
