<?php declare(strict_types=1);

namespace PeeHaa\MailGrab\Http\Command;

use PeeHaa\AmpWebsocketCommand\Failure;
use PeeHaa\AmpWebsocketCommand\Input;
use PeeHaa\AmpWebsocketCommand\Success;
use PeeHaa\MailGrab\Http\Entity\Mail;
use PeeHaa\MailGrab\Http\Storage\Storage;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use function Amp\Promise\wait;

class GetSourceTest extends TestCase
{
    /** @var MockObject|Storage */
    private $storageMock;

    /** @var MockObject|Input $inputMock */
    private $inputMock;

    /** @var MockObject|Mail */
    private $mailMock;

    private const ID = '53d8d320-f546-406e-b17f-2938098cbb74';

    public function setUp()
    {
        $this->storageMock = $this->createMock(Storage::class);

        $this->inputMock = $this->getMockBuilder(Input::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->mailMock = $this->getMockBuilder(Mail::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
    }

    public function testExecuteReturnsFailureWhenMailIsNotAvailable()
    {
        $this->storageMock
            ->expects($this->once())
            ->method('has')
            ->willReturn(false)
            ->with(self::ID)
        ;

        $this->inputMock
            ->expects($this->once())
            ->method('getParameter')
            ->willReturn(self::ID)
            ->with('id')
        ;

        $getSource = new GetSource($this->storageMock);

        $result = wait($getSource->execute($this->inputMock));

        $this->assertInstanceOf(Failure::class, $result);
    }

    public function testExecuteGetsMail()
    {
        $this->mailMock
            ->expects($this->once())
            ->method('getSource')
            ->willReturn('source')
        ;

        $this->storageMock
            ->expects($this->once())
            ->method('has')
            ->willReturn(true)
            ->with(self::ID)
        ;

        $this->storageMock
            ->expects($this->once())
            ->method('get')
            ->willReturn($this->mailMock)
            ->with(self::ID)
        ;

        $this->inputMock
            ->expects($this->once())
            ->method('getParameter')
            ->willReturn(self::ID)
            ->with('id')
        ;

        $getSource = new GetSource($this->storageMock);

        $result = wait($getSource->execute($this->inputMock));

        $this->assertInstanceOf(Success::class, $result);
    }

    public function testExecuteReturnsResponseWhenSourceIsAvailable()
    {
        $this->mailMock
            ->expects($this->once())
            ->method('getSource')
            ->willReturn('SOURCE')
        ;

        $this->storageMock
            ->expects($this->once())
            ->method('has')
            ->willReturn(true)
            ->with(self::ID)
        ;

        $this->storageMock
            ->expects($this->once())
            ->method('get')
            ->willReturn($this->mailMock)
            ->with(self::ID)
        ;

        $this->inputMock
            ->expects($this->once())
            ->method('getParameter')
            ->willReturn(self::ID)
            ->with('id')
        ;

        $getSource = new GetSource($this->storageMock);

        $result = wait($getSource->execute($this->inputMock));

        $this->assertInstanceOf(Success::class, $result);
        $this->assertSame('{"success":true,"payload":{"command":"source","source":"SOURCE"}}', (string) $result);
    }

    public function testExecuteReturnsResponseWhenSourceIsNotAvailable()
    {
        $this->mailMock
            ->expects($this->once())
            ->method('getSource')
            ->willReturn('')
        ;

        $this->storageMock
            ->expects($this->once())
            ->method('has')
            ->willReturn(true)
            ->with(self::ID)
        ;

        $this->storageMock
            ->expects($this->once())
            ->method('get')
            ->willReturn($this->mailMock)
            ->with(self::ID)
        ;

        $this->inputMock
            ->expects($this->once())
            ->method('getParameter')
            ->willReturn(self::ID)
            ->with('id')
        ;

        $getSource = new GetSource($this->storageMock);

        $result = wait($getSource->execute($this->inputMock));

        $this->assertInstanceOf(Success::class, $result);
        $this->assertSame('{"success":true,"payload":{"command":"source","source":""}}', (string) $result);
    }
}
