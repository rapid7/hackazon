<?php

namespace App\Controller;

class Review extends \App\Page
{

    public function action_send()
    {
        if ($this->request->method == 'POST') {
            try {
            $product = $this->pixie->orm->get('Product')->where('productID', (int)$this->request->post('productID'))->find();
            if ($product->loaded()) {
                $user = $this->pixie->auth->user();
                $model = $this->pixie->orm->get('Review');
                $username = !is_null($user) ? $user->username : $this->request->post('userName');
                $rating = (int)$this->request->post('starValue');
                $review = $this->request->post('textReview');
                $email = !is_null($user) ? $user->emal : $this->request->post('userEmail');
                $model->productID = $product->productID;
                $model->add('product', $product);
                $model->addReview($username, $email, $review, $rating);
                $this->redirect('/product/view/'.$product->productID);
            } else
                $this->redirect('/404');
            } catch (\Exception $e) {
                $this->redirect('/404');
            }
        }
    }

}