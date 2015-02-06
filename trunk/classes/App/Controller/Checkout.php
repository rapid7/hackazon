<?php
namespace App\Controller;

use App\Cart\CartService;
use App\Exception\ForbiddenException;
use App\Exception\NotFoundException;
use App\Exception\RedirectException;
use App\Model\Cart as CartModel;
use App\Model\CustomerAddress;
use App\Page;
use VulnModule\Config\Annotations as Vuln;

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
     * @Vuln\Description("View: cart/shipping. Or AJAX request.")
     */
    public function action_shipping() {
        $this->restrictActions(CartModel::STEP_SHIPPING);

        $service = $this->pixie->cart;
        $customerAddresses = $service->getAddresses();

        if ($this->request->is_ajax()) {
            $this->checkCsrfToken('checkout_step2', null, false);

            $post = $this->request->postWrap();
            $addressId = $post['address_id']->raw() ? $post['address_id'] : $post['address_id']->copy(0);

            if ($post['full_form'] && $post['full_form']->raw()) {
                /** @var CustomerAddress $address */
                $address = $this->pixie->orm->get('CustomerAddress');
                $address->createFromArray($post);

                if ($addressId->raw()) {
                    $existingAddress = $this->getAddressForUid($addressId->raw());

                    if ($existingAddress->isSimilarTo($address)) {
                        $service->setShippingAddressUid($addressId->raw());
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
                if ($addressId->raw()) {
                    $existingAddress = $this->getAddressForUid($addressId->raw());

                    if ($existingAddress) {
                        $service->setShippingAddressUid($addressId->raw());
                        $service->setShippingAddress($existingAddress);

                    } else {
                        throw new NotFoundException();
                    }

                } else {
                    throw new NotFoundException();
                }
            }

            $service->updateLastStep(CartModel::STEP_BILLING);
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
     * @Vuln\Description("View: cart/billing. Or AJAX request.")
     */
    public function action_billing() {
        $this->restrictActions(CartModel::STEP_BILLING);
        $service = $this->pixie->cart;
        $customerAddresses = $service->getAddresses();
                //var_dump($customerAddresses);exit;
        if ($this->request->is_ajax()) {
            $this->checkCsrfToken('checkout_step3', null, false);

            $post = $this->request->postWrap();
            $addressId = $post['address_id']->raw() ? $post['address_id'] : $post['address_id']->copy(0);

            if ($post['full_form'] && $post['full_form']->raw()) {
                /** @var CustomerAddress $address */
                $address = $this->pixie->orm->get('CustomerAddress');
                $address->createFromArray($post);

                if ($addressId) {
                    $existingAddress = $this->getAddressForUid($addressId->raw());

                    if ($existingAddress->isSimilarTo($address)) {
                        $service->setBillingAddressUid($addressId->raw());
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
                if ($addressId->raw()) {
                    $existingAddress = $this->getAddressForUid($addressId->raw());

                    if ($existingAddress) {
                        $service->setBillingAddressUid($addressId->raw());
                        $service->setBillingAddress($existingAddress);

                    } else {
                        throw new NotFoundException();
                    }

                } else {
                    throw new NotFoundException();
                }
            }

            $service->updateLastStep(CartModel::STEP_CONFIRM);
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
     * AJAX action return CustomerAddress json by address id
     * @Vuln\Description("No view. AJAX request for address.")
     */
    public function action_getAddress()
    {
        $service = $this->pixie->cart;
        $post = $this->request->postWrap();
        $addressId = $post['address_id'];
        $address = $service->getAddress($addressId->raw());
        $this->jsonResponse($address->as_array());
    }

    /**
     * Delete address by id
     * @Vuln\Description("No view. AJAX request to delete address.")
     */
    public function action_deleteAddress()
    {
        $service = $this->pixie->cart;
        $post = $this->request->postWrap();
        $addressId = $post['address_id'];
        $service->removeAddress($addressId->raw());
        $this->execute = false;
    }

    /**
     * Show confirmation step
     * @Vuln\Description("View: cart/confirmation.")
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
     * AJAX action which create order.
     * @Vuln\Description("No view.")
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
     * Show order step success.
     * @Vuln\Description("View: cart/order")
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