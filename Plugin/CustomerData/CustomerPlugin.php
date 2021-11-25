<?php

namespace Crode\CustomerStatus\Plugin\CustomerData;

use Magento\Customer\CustomerData\Customer;
use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Framework\Api\AttributeInterface;

class CustomerPlugin
{
    /**
     * @var CurrentCustomer
     */
    private $currentCustomer;

    /**
     * @param CurrentCustomer $currentCustomer
     */
    public function __construct(
        CurrentCustomer $currentCustomer
    ) {
        $this->currentCustomer = $currentCustomer;
    }

    /**
     * @param Customer $subject
     * @param array $result
     * @return array
     */
    public function afterGetSectionData(Customer $subject, array $result = []): array
    {
        if (!$result) {
            return $result;
        }

        $customer = $this->currentCustomer->getCustomer();
        $status = $customer->getCustomAttribute('customer_status');
        if (!$status instanceof AttributeInterface) {
            return $result;
        }

        $result[$status->getAttributeCode()] = $status->getValue();
        return $result;
    }
}
