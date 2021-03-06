<?php declare(strict_types=1);

namespace PeeHaa\MailGrabTest\Unit\Http\Command;

use PeeHaa\AmpWebsocketCommand\Input;
use PeeHaa\AmpWebsocketCommand\Success;
use PeeHaa\MailGrab\Http\Command\DeleteNotification;
use PeeHaa\MailGrab\Http\Storage\Storage;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use function Amp\Promise\wait;

class DeleteNotificationTest extends TestCase
{
    /** @var MockObject|Storage */
    private $storageMock;

    /** @var MockObject|Input */
    private $inputMock;

    private const ID = '53d8d320-f546-406e-b17f-2938098cbb74';
    
    public function setUp()
    {
        $this->storageMock = $this->createMock(Storage::class);

        $this->inputMock = $this->getMockBuilder(Input::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
    }

    public function testExecuteReturnsResponse()
    {
        $this->inputMock
            ->expects($this->once())
            ->method('getParameter')
            ->willReturn(self::ID)
            ->with('id')
        ;

        $deleteNotification = new DeleteNotification($this->storageMock);

        $result = wait($deleteNotification->execute($this->inputMock));

        $this->assertInstanceOf(Success::class, $result);
        $this->assertSame('{"success":true,"payload":{"command":"deleteNotification","id":"53d8d320-f546-406e-b17f-2938098cbb74"}}', (string) $result);
    }
}
