<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Tests;

use Arokettu\Uuid\Helpers\SystemMac;
use Arokettu\Uuid\Node\MacNode;
use PHPUnit\Framework\TestCase;

class MacNodeTest extends TestCase
{
    public function testParse(): void
    {
        // parse unix
        $node = MacNode::parse('14:45:FD:78:2b:a3');
        self::assertEquals('1445fd782ba3', $node->getHex());

        // parse windows
        $node = MacNode::parse('52-41-2e-DD-1e-02');
        self::assertEquals('52412edd1e02', $node->getHex());

        // parse hex
        $node = MacNode::parse('28a86DA6be83');
        self::assertEquals('28a86da6be83', $node->getHex());
    }

    public function testParseWrongData(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('Unrecognized MAC format');
        MacNode::parse('so:me:ju:nk:00:00');
    }

    public function testDoNotAllowMixedSeparators(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('Unrecognized MAC format');
        MacNode::parse('94-47-b9-e7:6b:fc');
    }

    public function testSystemMac(): void
    {
        // this test will likely fail on Windows
        // todo: detect and disable
        $node1 = MacNode::system();
        self::assertEquals(12, \strlen($node1->getHex())); // we can't predict what mac do we have

        $node2 = MacNode::system();
        self::assertEquals($node1->getHex(), $node2->getHex());
    }
}
