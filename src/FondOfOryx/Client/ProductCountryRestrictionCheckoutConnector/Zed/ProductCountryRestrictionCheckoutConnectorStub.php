<?php

namespace FondOfOryx\Client\ProductCountryRestrictionCheckoutConnector\Zed;

use FondOfOryx\Client\ProductCountryRestrictionCheckoutConnector\Dependency\Client\ProductCountryRestrictionCheckoutConnectorToZedRequestClientInterface;
use Generated\Shared\Transfer\BlacklistedCountryCollectionTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\QuoteValidationResponseTransfer;

class ProductCountryRestrictionCheckoutConnectorStub implements ProductCountryRestrictionCheckoutConnectorStubInterface
{
    /**
     * @var \FondOfOryx\Client\ProductCountryRestrictionCheckoutConnector\Dependency\Client\ProductCountryRestrictionCheckoutConnectorToZedRequestClientInterface
     */
    protected $zedRequestClient;

    /**
     * @param \FondOfOryx\Client\ProductCountryRestrictionCheckoutConnector\Dependency\Client\ProductCountryRestrictionCheckoutConnectorToZedRequestClientInterface $zedRequestClient
     */
    public function __construct(ProductCountryRestrictionCheckoutConnectorToZedRequestClientInterface $zedRequestClient)
    {
        $this->zedRequestClient = $zedRequestClient;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteValidationResponseTransfer
     */
    public function validateQuote(QuoteTransfer $quoteTransfer): QuoteValidationResponseTransfer
    {
        /** @var \Generated\Shared\Transfer\QuoteValidationResponseTransfer $quoteValidationResponseTransfer */
        $quoteValidationResponseTransfer = $this->zedRequestClient->call(
            '/product-country-restriction-checkout-connector/gateway/validate-quote',
            $quoteTransfer,
        );

        return $quoteValidationResponseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\BlacklistedCountryCollectionTransfer
     */
    public function getBlacklistedCountryCollectionByQuote(QuoteTransfer $quoteTransfer): BlacklistedCountryCollectionTransfer
    {
        /** @var \Generated\Shared\Transfer\BlacklistedCountryCollectionTransfer $blacklistedCountryCollectionTransfer */
        $blacklistedCountryCollectionTransfer = $this->zedRequestClient->call(
            '/product-country-restriction-checkout-connector/gateway/get-blacklisted-country-collection-by-quote',
            $quoteTransfer,
        );

        return $blacklistedCountryCollectionTransfer;
    }
}
