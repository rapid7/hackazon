<?php

namespace App\Controller;

use App\Exception\HttpException;
use App\Exception\NotFoundException;
use App\Page;
use VulnModule\Config\Annotations as Vuln;

class Review extends Page
{
    /**
     * @throws HttpException
     * @throws NotFoundException
     * @throws \Exception
     * @Vuln\Description("No view.")
     */
    public function action_send()
    {
        if ($this->request->method == 'POST') {
            $this->checkCsrfToken('review');

            $productID = $this->request->postWrap('productID');

            /** @var \App\Model\Product $product */
            $product = $this->pixie->orm->get('Product')->where('productID', $productID)->find();

            if ($product->loaded()) {
                $user = $this->pixie->auth->user();
                $model = new \App\Model\Review($this->pixie);
                $username = !is_null($user) ? $user->username : $this->request->postWrap('userName');
                $rating = $this->request->postWrap('starValue');
                $review = $this->request->postWrap('textReview');
                $email = !is_null($user) ? $user->email : $this->request->postWrap('userEmail');
                $model->productID = $product->productID;
                $model->add('product', $product);
                $model->addReview($username, $email, $review, $rating);
                $this->redirect('/product/view?id='.$product->productID);

            } else {
                throw new NotFoundException("Product not found");
            }

        } else {
            throw new HttpException('Method Not Allowed: ' . $this->request->method, 405, null, 'Method Not Allowed');
        }
    }

}