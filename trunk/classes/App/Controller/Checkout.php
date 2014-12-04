<?php
namespace App\Controller;

use App\Cart\CartService;
use App\Exception\ForbiddenException;
use App\Exception\NotFoundException;
use App\Exception\RedirectException;
use App\Model\Cart as CartModel;
use App\Model\Cart;
use App\Model\CustomerAddress;
use App\Page;

class Checkout extends Page {

    /**
     * require auth
     */
    public function before()
    {
        parent::before();
    }

    /**
     * validate restrict for actions by last step checkout
     * @param int $actionStep
     * @throws ForbiddenException
     */
    protected function restrictActions($actionStep)
    {
        $lastStep = $this->pixie->cart->getLastStep();

        if ($lastStep == CartModel::STEP_ORDER && $actionStep != CartModel::STEP_ORDER) {
            $this->redirect('/checkout/order');
        }

        if ($actionStep > $lastStep) {
            if ($this->request->is_ajax()) {
                throw new ForbiddenException();
            } else {
                $this->redirect('/cart/view');
            }
        }
    }

    /**
     * 1. Set cart customer
     * 2. if ajax create/update customer addresses
     * 3. default show shipping step
     */
    public function action_shipping() {
        $this->restrictActions(CartModel::STEP_SHIPPING);
        $service = $this->pixie->cart;
        $customerAddresses = $service->getAddresses();

        if ($this->request->is_ajax()) {
            $this->checkCsrfToken('checkout_step2', null, false);

            $post = $this->request->post();
            $addressId = isset($post['address_id']) ? $post['address_id'] : 0;


            if ($post['full_form']) {
                /** @var CustomerAddress $address */
                $address = $this->pixie->orm->get('CustomerAddress');
                $address->createFromArray($post);

                if ($addressId) {
                    $existingAddress = $this->getAddressForUid($addressId);

                    if ($existingAddress->isSimilarTo($address)) {
                        $service->setShippingAddressUid($addressId);
                        $service->setShippingAddress($existingAddress);

                    } else {
                        $service->setShippingAddressUid(null);
                        $service->setShippingAddress($address);
                    }

                } else {
                    $service->setShippingAddressUid(null);
                    $service->setShippingAddress($address);
                }

            } else {
                if ($addressId) {
                    $existingAddress = $this->getAddressForUid($addressId);

                    if ($existingAddress) {
                        $service->setShippingAddressUid($addressId);
                        $service->setShippingAddress($existingAddress);

                    } else {
                        throw new NotFoundException();
                    }

                } else {
                    throw new NotFoundException();
                }
            }

            $service->updateLastStep(Cart::STEP_BILLING);
            $this->execute = false;

        } else {
            $this->prepareShippingAndBillingAddresses($service, $customerAddresses);

            $this->view->subview = 'cart/shipping';
            $this->view->tab = 'shipping';//active tab
            $this->view->step = $service->getStepLabel();//last step
            $currentAddress = $service->getShippingAddress();
            $this->view->shippingAddress = !$currentAddress ? [] : $currentAddress->as_array();
            $this->view->customerAddresses = $customerAddresses;
        }
    }

    /**
     * if ajax create/update customer addresses
     * default show billing step
     */
    public function action_billing() {
        $this->restrictActions(CartModel::STEP_BILLING);
        $service = $this->pixie->cart;
        $customerAddresses = $service->getAddresses();
                //var_dump($customerAddresses);exit;
        if ($this->request->is_ajax()) {
            $this->checkCsrfToken('checkout_step3', null, false);

            $post = $this->request->post();
            $addressId = isset($post['address_id']) ? $post['address_id'] : 0;

            if ($post['full_form']) {
                /** @var CustomerAddress $address */
                $address = $this->pixie->orm->get('CustomerAddress');
                $address->createFromArray($post);

                if ($addressId) {
                    $existingAddress = $this->getAddressForUid($addressId);

                    if ($existingAddress->isSimilarTo($address)) {
                        $service->setBillingAddressUid($addressId);
                        $service->setBillingAddress($existingAddress);

                    } else {
                        $service->setBillingAddressUid(null);
                        $service->setBillingAddress($address);
                    }

                } else {
                    $service->setBillingAddressUid(null);
                    $service->setBillingAddress($address);
                }

            } else {
                if ($addressId) {
                    $existingAddress = $this->getAddressForUid($addressId);

                    if ($existingAddress) {
                        $service->setBillingAddressUid($addressId);
                        $service->setBillingAddress($existingAddress);

                    } else {
                        throw new NotFoundException();
                    }

                } else {
                    throw new NotFoundException();
                }
            }

            $service->updateLastStep(Cart::STEP_CONFIRM);
            $this->execute = false;

        } else {
            $this->prepareShippingAndBillingAddresses($service, $customerAddresses);

            $this->view->subview = 'cart/billing';
            $this->view->tab = 'billing';

            $this->view->step = $service->getStepLabel();//last step
            $currentAddress = $service->getBillingAddress();
            $this->view->billingAddress = !$currentAddress ? [] : $currentAddress->as_array();
            $this->view->customerAddresses = $customerAddresses;
        }
    }

    protected function prepareShippingAndBillingAddresses(CartService $service, &$customerAddresses)
    {
        if ($service->getShippingAddress() && !$service->getShippingAddress()->id()) {
            $service->getShippingAddress()->setUid('_shipping_');
            array_unshift($customerAddresses, $service->getShippingAddress());
        }

        if ($service->getBillingAddress() && !$service->getBillingAddress()->id()
            && !$service->getBillingAddress()->isSimilarTo($service->getShippingAddress())
        ) {
            $service->getBillingAddress()->setUid('_billing_');
            array_unshift($customerAddresses, $service->getBillingAddress());
        }
    }

    protected function getAddressForUid($addressId)
    {
        $service = $this->pixie->cart;

        if ($addressId == '_shipping_') {
            $existingAddress = $service->getShippingAddress();

        } else if ($addressId == '_billing_') {
            $existingAddress = $service->getBillingAddress();

        } else {
            $existingAddress = $service->getAddress($addressId);
        }

        return $existingAddress;
    }

    /**
     * ajax action return CustomerAddress json by address id
     */
    public function action_getAddress()
    {
        $service = $this->pixie->cart;
        $post = $this->request->post();
        $addressId = $post['address_id'];
        $address = $service->getAddress($addressId);
        $this->jsonResponse($address->as_array());
    }

    /**
     * delete address by id
     */
    public function action_deleteAddress()
    {
        $service = $this->pixie->cart;
        $post = $this->request->post();
        $addressId = $post['address_id'];
        $service->removeAddress($addressId);
        $this->execute = false;
    }

    /**
     * show confirmation step
     */
    public function action_confirmation()
    {
        $this->restrictActions(CartModel::STEP_CONFIRM);
        $this->checkCart();

        $service = $this->pixie->cart;

        $this->view->subview = 'cart/confirmation';
        $this->view->tab = 'confirmation';
        $this->view->step = $service->getStepLabel();//last step
        $this->view->cart = $service->getCart();
        $this->view->items = $service->getItems();
        $this->view->shippingAddress = $service->getShippingAddress();
        $this->view->billingAddress = $service->getBillingAddress();
        $this->view->totalPrice = $service->getTotalPrice();
        $this->view->discount = $service->getDiscount();
    }

    /**
     * ajax action which create order
     */
    public function action_placeOrder()
    {
        if (is_null($this->pixie->auth->user())) {
            $location = '/user/login?return_url=' . rawurlencode('/checkout/confirmation');
            if ($this->request->is_ajax()) {
                $this->jsonResponse(['location' => $location]);
            } else {
                $this->redirect($location);
            }
            $this->execute = false;
            return;
        }
        $this->checkCsrfToken('checkout_step4', null, false);
        $this->restrictActions(CartModel::STEP_CONFIRM);

        $service = $this->pixie->cart;
        $this->checkCart();
        $service->placeOrder();

        if ($this->request->is_ajax()) {
            $this->jsonResponse(['success' => 1]);
        } else {
            $this->redirect('/checkout/order');
        }
        $this->execute = false;
    }

    /**
     * show order step success
     */
    public function action_order()
    {
        $this->restrictActions(CartModel::STEP_ORDER);
        $service = $this->pixie->cart;
        $this->view->subview = 'cart/order';
        $this->view->tab = 'order';
        $this->view->step = $service->getStepLabel();//last step
        $service->reset();
    }

    public function checkCart()
    {
        try {
            $service = $this->pixie->cart;
            $service->checkCart();

        } catch (RedirectException $e) {
            if ($e->getLocation()) {
                $this->redirect($e->getLocation());
            }
        }
    }
}