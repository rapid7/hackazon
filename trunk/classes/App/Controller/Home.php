<?php

namespace App\Controller;

use App\Model\Product as Product;
use App\Model\Review;
use \App\Model\SpecialOffers as SpecialOffers;
use App\DataImport\BestBuyReviewImporter;
use App\Page;
use VulnModule\Config\Annotations as Vuln;

/**
 * Class Home
 * @package App\Controller
 * @Vuln\Description(description="This is a homepage controller.")
 */
class Home extends Page {

    const COUNT_RND_PRODUCTS = 3; //count products for rnd block of main page

    /**
     * @var int Count of products in top viewed products on homepage.
     */

    protected $topViewedCount = 4;

    /**
     * @var int Count of products in related to visited products on homepage.
     */
    protected $relatedToVisitedCount = 8;

    /**
     * @var int Count of products in best choice block on homepage.
     */
    protected $bestChoiceCount = 4;

    /**
     * @var int Count of reviews on homepage.
     */
    protected $reviewsCount = 2;

    /**
     * @Vuln\Description("View used: home/home")
     */
    public function action_index() {
        $mostPopularProductsCount = 3;
        $bestSellingProductsCount = 3;
        $specialOffersCount = 3;
        $otherCustomerProductCount = 4;

        // Amount of products in the bottom of the page
        $randomProductsCount = 4;

        // Count of reviews after each product section.
        $productSectionReviewCount = 3;

        $product = new Product($this->pixie);
        $special_offers = new SpecialOffers($this->pixie);
        $review = new Review($this->pixie);
        //$this->view->topViewedProducts = $product->getRandomProducts($this->topViewedCount);

        $visitedProductIds = $this->request->cookieWrap('visited_products');

        $this->view->rnd_products = $product->getRndProduct(self::COUNT_RND_PRODUCTS);
        $this->view->relatedToVisitedProducts = $product->getVisitedProducts($visitedProductIds); //$product->getRandomProducts($this->relatedToVisitedCount);
        $this->view->bestChoiceProducts = $product->getRandomProducts($this->bestChoiceCount);
        $this->view->mostPopularProducts = $product->getRandomProducts($mostPopularProductsCount);
        $this->view->bestSellingProducts = $product->getRandomProducts($bestSellingProductsCount);
        $this->view->randomProducts = $product->getRandomProducts($randomProductsCount);
        $this->view->special_offers = $special_offers->getRandomOffers($specialOffersCount);
        $this->view->selectedReviews = $review->getRandomReviews($this->reviewsCount);
        $this->view->otherCustomersProducts = $product->getRandomProducts($otherCustomerProductCount);

        $this->view->productSections = array(
            'related_to_viewed' => array(
                'title' => 'Related to Visited',
                'products' => $this->view->relatedToVisitedProducts,
                'reviews' => count($this->view->relatedToVisitedProducts) ? $review->getRandomReviews($productSectionReviewCount) : array()
            ),
            'best_choice' => array(
                'title' => 'Best Choice',
                'products' => $this->view->bestChoiceProducts,
                'reviews' => count($this->view->bestChoiceProducts) ? $review->getRandomReviews($productSectionReviewCount) : array()
            ),

            'random' => array(
                'title' => "",
                'products' => $this->view->randomProducts
            )

        );

        $this->view->topProductBlocks = array(
            'most_popular' => array(
                'title' => "Top $bestSellingProductsCount most popular",
                'products' => $this->view->mostPopularProducts
            ),
            'best_selling' => array(
                'title' => "Top $bestSellingProductsCount best selling",
                'products' => $this->view->bestSellingProducts
            )
        );

        $this->view->common_path = $this->common_path;
        $this->view->subview = 'home/home';
        $this->view->message = "Index page";
    }

    public function action_404() {
        $this->view->subview = '404';
        $this->view->message = "Index page";
    }

    /**
     * Method for generating reviews from outer source.
     */
    public function generateReviews() {
        set_time_limit(300);
        // Get product ids as parents for reviews
        $res = $this->pixie->db->query('select')->table('tbl_products')->fields('productID')->execute();
        $productIds = array();
        foreach ($res as $row) {
            $productIds[] = (int) $row->productID;
        }

        $importer = new BestBuyReviewImporter($this->pixie);
        $reviews = $importer->getReviews(500);

        foreach ($reviews as $rev) {
            $review = new Review($this->pixie);
            $review->values($rev);
            $parentKey = array_rand($productIds);
            $review->productID = $productIds[$parentKey];
            $review->save();
        }
    }

    /**
     * @return int
     */
    public function getBestChoiceCount() {
        return $this->bestChoiceCount;
    }

    /**
     * @param int $bestChoiceCount
     */
    public function setBestChoiceCount($bestChoiceCount) {
        $this->bestChoiceCount = $bestChoiceCount;
    }

    /**
     * @return int
     */
    public function getRelatedToVisitedCount() {
        return $this->relatedToVisitedCount;
    }

    /**
     * @param int $relatedToVisitedCount
     */
    public function setRelatedToVisitedCount($relatedToVisitedCount) {
        $this->relatedToVisitedCount = $relatedToVisitedCount;
    }

    /**
     * @return int
     */
    public function getReviewsCount() {
        return $this->reviewsCount;
    }

    /**
     * @param int $reviewsCount
     */
    public function setReviewsCount($reviewsCount) {
        $this->reviewsCount = $reviewsCount;
    }

    /**
     * @return int
     */
    public function getTopViewedCount() {
        return $this->topViewedCount;
    }

    /**
     * @param int $topViewedCount
     */
    public function setTopViewedCount($topViewedCount) {
        $this->topViewedCount = $topViewedCount;
    }
}