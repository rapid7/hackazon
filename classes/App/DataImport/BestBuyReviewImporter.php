<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 30.07.2014
 * Time: 16:23
 */


namespace App\DataImport;


use App\Pixie;

/**
 * Class BestBuyReviewImporter.
 * Used o fetch reviews from bestbuy.com
 * @package App\DataImport
 */
class BestBuyReviewImporter
{
    /**
     * @var Pixie
     */
    private $pixie;

    /**
     * Constructor.
     * @param Pixie $pixie
     */
    public function __constrtuct(Pixie $pixie)
    {
        $this->pixie = $pixie;
    }

    /**
     * Fetches given number of reviews
     * @param int $num
     * @return array
     */
    public function getReviews($num = 10)
    {
        $reviews = array();

        $total = 0;
        $curPage = 1;
        $utcTZ = new \DateTimeZone('UTC');
        while (true) {
            $pageReviews = $this->getPage($curPage++);
            if (!count($pageReviews)) {
                break;
            }

            foreach ($pageReviews as $rev) {
                if (!isset($rev['reviewer']) || !isset($rev['reviewer'][0])) {
                    continue;
                }
                $date = \DateTime::createFromFormat('Y-m-d\TH:i:s', $rev['submissionTime'], $utcTZ);

                if ((int) $date->format('Y') > date('Y')) {
                    continue;
                }
                $reviews[] = array(
                    'username' => mb_substr($rev['reviewer'][0]['name'], 0, 50, 'utf-8'),
                    'email' => mb_substr('eml' . $total . '_' . preg_replace('/[^a-zA-Z0-9]/', '', strtolower($rev['reviewer'][0]['name'])), 0, 30, 'utf-8')
                            . '@example.com',
                    'review' => preg_replace('|\'|', '\\', $rev['comment']),
                    'date_time' => $date->format('Y-m-d H:i:s'),
                    'rating' => (int) $rev['rating']
                );
                $total++;
                if ($total >= $num) {
                    break 2;
                }
            }

            sleep(1);
        }

        return $reviews;
    }

    /**
     * Fetches certain page of reviews.
     * @param int $pageNum
     * @return array
     */
    public function getPage($pageNum = 1)
    {
        $reviews = array();
        $key = "7a8zjugx9s2hwc2f5t8j8gcb";
        $perPage = 100;
        $data = file_get_contents(
            "http://api.remix.bestbuy.com/v1/reviews?format=json&apiKey={$key}&page={$pageNum}&pageSize={$perPage}"
        );

        if ($data === false) {
            return $reviews;
        }
        $data = json_decode($data, true);

        if (!isset($data['reviews']) || !is_array($data['reviews']) || !count($data['reviews'])) {
            return $reviews;
        }

        return $data['reviews'];
    }
} 