<?php
namespace App\Services;

use App\Repositories\CheckoutRepository;


class CheckoutService{

    protected $checkoutRepository;

    public function __construct(CheckoutRepository $checkoutRepository){
        $this->checkoutRepository = $checkoutRepository;
    }

    public function getCartItem(){
        return $this->checkoutRepository->getCartItem();
    }

    public function deleteProduct($data){
        return $this->checkoutRepository->deleteProduct($data);
    }
    public function addressStore($data){
        return $this->checkoutRepository->addressStore($data);
    }

    public function guestAddressStore($data)
    {
        return $this->checkoutRepository->guestAddressStore($data);
    }

    public function shippingAddressChange($data){
        return $this->checkoutRepository->shippingAddressChange($data);
    }
    public function billingAddressChange($data){
        return $this->checkoutRepository->billingAddressChange($data);
    }

    public function emailChange($data){
        return $this->checkoutRepository->emailChange($data);
    }
    public function phoneChange($data){
        return $this->checkoutRepository->phoneChange($data);
    }
    public function getCountries(){
        return $this->checkoutRepository->getCountries();
    }

    public function emailChangeGuest($data)
    {
        return $this->checkoutRepository->emailChangeGuest($data);
    }
    public function phoneChangeGuest($data)
    {
        return $this->checkoutRepository->phoneChangeGuest($data);
    }

}
