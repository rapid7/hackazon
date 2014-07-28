<?php

namespace App\Model;

class Review extends \PHPixie\ORM\Model {

    public $table = 'tbl_review';
    public $id_field = 'reviewID';

    const APPROVED = 1;
    const NOTAPPROVED = 0;

    protected $belongs_to=array(
        'product'=>array(
            'model'=>'Product',
            'key'=>'productID'
        )
    );

    public function addReview($userName, $userEmail, $review, $rating) {
        $this->date_time = date('Y-m-d H:i:s');
        $this->username = $userName;
        $this->email = $userEmail;
        $this->rating = $rating;
        $this->review = $review;
        /* Temporary flag for testing */
        $this->moder = self::APPROVED;
        if ($this->save()) {
            /*Update Average Rating */
            $this->product->customers_rating = $this->getAverage();
            $this->product->customer_votes++;
            $this->product->save();
        }
    }

    private function getAverage() {
        $allReviews = $this->product->reviews->count_all();
        if ($allReviews > 0) {
            $sum = (int)$this->pixie->db->query('select')->table($this->table)
                ->fields($this->pixie->db->expr("SUM(`rating`) as `rate`"))
                ->where('productID', $this->product->productID)
                ->execute()->get('rate');
            return round($sum/$allReviews, 1);
        }
        return 0;
    }

    public function getDateLabel() {
        $date = \DateTime::createFromFormat('Y-m-d H:i:s', $this->date_time);
        $current = new \DateTime();
        return $current->diff($date)->format('%a days');
    }
}