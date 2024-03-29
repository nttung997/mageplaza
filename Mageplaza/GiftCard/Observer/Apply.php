<?php

namespace Mageplaza\GiftCard\Observer;

use \Magento\Framework\Event\Observer;
use \Magento\Framework\Event\ObserverInterface;

class Apply implements ObserverInterface
{
    protected $giftCard;
    protected $messageManager;
    protected $quoteRepository;
    protected $_actionFlag;
    protected $redirect;
    protected $_checkoutSession;
    protected $cart;

    public function __construct(
        \Mageplaza\GiftCard\Model\GiftCardFactory $giftCard,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\App\ActionFlag $actionFlag,
        \Magento\Framework\App\Response\RedirectInterface $redirect,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository

    )
    {
        $this->quoteRepository = $quoteRepository;
        $this->giftCard = $giftCard;
        $this->messageManager = $messageManager;
        $this->_actionFlag = $actionFlag;
        $this->redirect = $redirect;
        $this->_checkoutSession = $checkoutSession;
        $this->cart = $cart;
    }

    public function deleteOldCouponCode()
    {
        $cartQuote = $this->cart->getQuote();
        $cartQuote->setCouponCode('')->collectTotals();
        $this->quoteRepository->save($cartQuote);
    }

    public function execute(Observer $observer)
    {
        $controller = $observer->getControllerAction(); //object
        $couponCode = $controller->getRequest()->getParam('coupon_code');
        $remove = $controller->getRequest()->getParam('remove');

        $giftCard = $this->giftCard->create();
        $giftCard->load($couponCode, 'code');

        if ($giftCard->getData('giftcard_id') && $remove == 0) { //code is gift card code && not remove
            //add code to session
            $this->_checkoutSession->setGiftcardCode($couponCode);
            $this->_checkoutSession->setGiftcardCodeAmount($giftCard->getData('balance'));

//            //collect totals
//            $this->_checkoutSession->getQuote()->collectTotals()->save();

            //delete old coupon code
            $this->deleteOldCouponCode();

            //add success message
            $this->messageManager->addSuccess(__('Gift code applied successfully.'));

            //stop apply default
            $this->_actionFlag->set('', \Magento\Framework\App\Action\Action::FLAG_NO_DISPATCH, true);
            $this->redirect->redirect($controller->getResponse(), 'checkout/cart/');

        }
        //neu apply coupon code
        $coupon = $this->couponFactory->create();
        $newCoupon = $coupon->load($couponCode, 'code')->getId();
        $oldGiftCard = $this->_checkoutSession->getGiftcardCode();
        if ($oldGiftCard && $newCoupon) {
            //delete gift card
            $this->_checkoutSession->unsGiftcardCode();
            $this->_checkoutSession->unsGiftcardCodeAmount();
        }
        if ($oldGiftCard && ($couponCode == '' || $remove == 1)) { //isset old gift card && (apply '' || cencel)
            //delete gift card
            $this->_checkoutSession->unsGiftcardCode();
            $this->_checkoutSession->unsGiftcardCodeAmount();
            //collect totals
            $this->_checkoutSession->getQuote()->collectTotals()->save();
            $this->messageManager->addSuccess(__('Gift code canceled successfully.'));
        }
    }
}

