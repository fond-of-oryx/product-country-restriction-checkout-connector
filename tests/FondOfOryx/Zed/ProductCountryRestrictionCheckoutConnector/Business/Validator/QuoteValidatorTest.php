<?php

namespace FondOfOryx\Zed\ProductCountryRestrictionCheckoutConnector\Business\Validator;

use ArrayObject;
use Codeception\Test\Unit;
use FondOfOryx\Zed\ProductCountryRestrictionCheckoutConnector\Business\Reader\BlacklistedCountryReader;
use FondOfOryx\Zed\ProductCountryRestrictionCheckoutConnector\Dependency\Facade\ProductCountryRestrictionCheckoutConnectorToProductCountryRestrictionFacadeInterface;
use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\ShipmentTransfer;

class QuoteValidatorTest extends Unit
{
    /**
     * @var \FondOfOryx\Zed\ProductCountryRestrictionCheckoutConnector\Dependency\Facade\ProductCountryRestrictionCheckoutConnectorToProductCountryRestrictionFacadeInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $productCountryRestrictionFacadeMock;

    /**
     * @var \FondOfOryx\Zed\ProductCountryRestrictionCheckoutConnector\Business\Validator\QuoteValidator
     */
    protected $quoteValidator;

    /**
     * @var \Generated\Shared\Transfer\QuoteTransfer|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $quoteTransferMock;

    /**
     * @var array<\PHPUnit\Framework\MockObject\MockObject>|array<\Generated\Shared\Transfer\ItemTransfer>
     */
    protected $itemTransferMocks;

    /**
     * @var \Generated\Shared\Transfer\ShipmentTransfer|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $shipmentTransferMock;

    /**
     * @var \Generated\Shared\Transfer\AddressTransfer|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $addressTransferMock;

    /**
     * @var \FondOfOryx\Zed\ProductCountryRestrictionCheckoutConnector\Business\Reader\BlacklistedCountryReaderInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $countryreaderMock;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->productCountryRestrictionFacadeMock = $this->getMockBuilder(ProductCountryRestrictionCheckoutConnectorToProductCountryRestrictionFacadeInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->quoteTransferMock = $this->getMockBuilder(QuoteTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->itemTransferMocks = [
            $this->getMockBuilder(ItemTransfer::class)
                ->disableOriginalConstructor()
                ->getMock(),
            $this->getMockBuilder(ItemTransfer::class)
                ->disableOriginalConstructor()
                ->getMock(),
            $this->getMockBuilder(ItemTransfer::class)
                ->disableOriginalConstructor()
                ->getMock(),
        ];

        $this->shipmentTransferMock = $this->getMockBuilder(ShipmentTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->addressTransferMock = $this->getMockBuilder(AddressTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->countryreaderMock = $this->getMockBuilder(BlacklistedCountryReader::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->quoteValidator = new QuoteValidator(
            $this->countryreaderMock,
        );
    }

    /**
     * @return void
     */
    public function testValidate(): void
    {
        $blacklistedCountries = [
            'FOO-1' => ['DE'],
            'FOO-2' => ['DE', 'BE'],
        ];

        $this->quoteTransferMock->expects(static::atLeastOnce())
            ->method('getItems')
            ->willReturn(new ArrayObject($this->itemTransferMocks));

        $this->itemTransferMocks[0]->expects(static::atLeastOnce())
            ->method('getSku')
            ->willReturn('FOO-1');

        $this->itemTransferMocks[1]->expects(static::atLeastOnce())
            ->method('getSku')
            ->willReturn('FOO-2');

        $this->itemTransferMocks[2]->expects(static::atLeastOnce())
            ->method('getSku')
            ->willReturn('FOO-3');

        $this->countryreaderMock->expects(static::atLeastOnce())
            ->method('getGroupedByQuote')
            ->with($this->quoteTransferMock)
            ->willReturn($blacklistedCountries);

        $this->itemTransferMocks[0]->expects(static::atLeastOnce())
            ->method('getShipment')
            ->willReturn(null);

        $this->itemTransferMocks[1]->expects(static::atLeastOnce())
            ->method('getShipment')
            ->willReturn($this->shipmentTransferMock);

        $this->itemTransferMocks[2]->expects(static::never())
            ->method('getShipment');

        $this->shipmentTransferMock->expects(static::atLeastOnce())
            ->method('getShippingAddress')
            ->willReturn($this->addressTransferMock);

        $this->addressTransferMock->expects(static::atLeastOnce())
            ->method('getIso2Code')
            ->willReturn('DE');

        $quoteValidationResponseTransfer = $this->quoteValidator->validate($this->quoteTransferMock);

        static::assertFalse($quoteValidationResponseTransfer->getIsSuccessful());
    }

    /**
     * @return void
     */
    public function testValidateWithoutBlacklistedCountries(): void
    {
        $this->countryreaderMock->expects(static::atLeastOnce())
            ->method('getGroupedByQuote')
            ->with($this->quoteTransferMock)
            ->willReturn([]);

        $this->quoteTransferMock->expects(static::never())
            ->method('getBillingAddress');

        $quoteValidationResponseTransfer = $this->quoteValidator->validate($this->quoteTransferMock);

        static::assertTrue($quoteValidationResponseTransfer->getIsSuccessful());
    }
}
