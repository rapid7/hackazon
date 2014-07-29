<?php

namespace App\Model;

use PHPixie\ORM;
use App\Helpers\ArraysHelper;

/**
 * Class Review
 * @property int reviewID
 * @property int productID
 * @property string username
 * @property string email
 * @property string review
 * @property string date_time
 * @property int moder
 * @property int rating
 * @property \App\Model\Product product
 * @package App\Model
 */
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

    /**
     * Selects {$maxCount} random reviews from DB.
     *
     * @param int $maxCount Maximum count of selected items
     *      (if table contains less items).
     * @return array
     */
    public function getRandomReviews($maxCount)
    {
        /** @var ORM $orm */
        $orm = $this->pixie->orm;
        $reviewCount = $orm->get('review')->count_all();
        $offsets = ArraysHelper::getRandomArray($maxCount, 1, $reviewCount);
        $reviews = [];
        // Query for every product with given offset
        foreach ($offsets as $offset) {
            $review = $orm->get('review')->offset($offset - 1)->find();
            $reviews[] = $review;
        }

        return $reviews;
    }
}