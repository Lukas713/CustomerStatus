<?php

namespace Crode\CustomerStatus\Controller\Index;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;

class Save implements HttpPostActionInterface
{
    /**
     * @var Session
     */
    private $session;

    /**
     * @var RedirectFactory
     */
    private $redirectFactory;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var ManagerInterface
     */
    private $messageManager;

    /**
     * @var Http
     */
    private $request;

    /**
     * @param Session $session
     * @param Http $request
     * @param ManagerInterface $messageManager
     * @param CustomerRepositoryInterface $customerRepository
     * @param RedirectFactory $redirectFactory
     */
    public function __construct(
        Session $session,
        Http $request,
        ManagerInterface $messageManager,
        CustomerRepositoryInterface $customerRepository,
        RedirectFactory $redirectFactory
    ) {
        $this->session = $session;
        $this->redirectFactory = $redirectFactory;
        $this->customerRepository = $customerRepository;
        $this->messageManager = $messageManager;
        $this->request = $request;
    }

    /**
     * @return Redirect
     */
    public function execute(): Redirect
    {
        $resultRedirect = $this->redirectFactory->create();

        if (!$customerId = $this->session->getCustomerId()) {
            return $resultRedirect->setPath('customer/account/login');
        }

        if (!$status = $this->request->getParam('customer_status')) {
            $this->messageManager->addErrorMessage(__('Customer Status is not provided'));
            return $resultRedirect->setPath('customerStatus/index/index');
        }

        try {
            $customer = $this->customerRepository->getById($customerId);
            $customer->setCustomAttribute('customer_status', $status);
            $this->customerRepository->save($customer);

        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            return $resultRedirect->setPath('customerStatus/index/index');
        }

        $this->messageManager->addSuccessMessage(__('Successfully saved customer status'));
        return $resultRedirect->setPath('customerStatus/index/index');
    }
}
