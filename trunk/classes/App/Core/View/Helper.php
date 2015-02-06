<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 08.08.2014
 * Time: 16:43
 */


namespace App\Core\View;
use App\Core\BaseController;
use App\Page;
use App\Pixie;
use PHPixie\Paginate\Pager\ORM as ORMPager;
use VulnModule\VulnerableField;
use VulnModule\VulnInjection;


/**
 * Class Helper
 * @property-read Pixie pixie
 * @package App\Core\View
 */
class Helper extends \PHPixie\View\Helper
{
    protected $orderStatusLabelMapping = [
        'complete' => 'label-success'
    ];

    /**
     * @var BaseController|Page
     */
    protected $controller;

    protected $aliases = array(
        '_' => 'output',
        '_esc' => 'escape',
        '_token' => 'token',
        '_dump' => 'dump',
        '_order_status' => 'order_status',
        '_pager' => 'pager',
        '_addToCartLink' => 'addToCartLink',
        '_trim' => 'trim'
    );

    /**
     * @inheritdoc
     * @param Pixie $pixie
     */
    public function __construct($pixie)
    {
        parent::__construct($pixie);
    }

    /**
     * @inheritdoc
     */
    public function escape($str/*, $fieldName = null*/)
    {
        if ($str instanceof VulnerableField) {
            return $str->escapeXSS();
        }

        return htmlspecialchars($str, ENT_COMPAT, 'UTF-8');
    }

    /**
     * @inheritdoc
     */
    public function output($str, $fieldName = null)
    {
        echo $this->escape($str, $fieldName);
    }

    /**
     * Render hidden CSRF field.
     * @param $tokenId
     * @param bool $refresh
     */
    public function token($tokenId, $refresh = true)
    {
        $service = $this->pixie->getVulnService();

        if (!$service || $service->csrfIsEnabled()) {
            echo '';
            return;
        }
        echo $service->renderTokenField(Page::TOKEN_PREFIX . $tokenId, $refresh);
    }

    /**
     * Dump all passed vars to output.
     */
    public function dump()
    {
        echo call_user_func_array('App\\Debug::dump', func_get_args());
    }

    /**
     * Generates Bootstrap label for order status.
     * @param $status
     * @return string
     */
    public function order_status($status)
    {
        $canonicalStatus = strtolower(trim($status));
        $label = isset($this->orderStatusLabelMapping[$canonicalStatus])
            ? $this->orderStatusLabelMapping[$canonicalStatus] : 'label-default';
        return '<span class="label ' . $label . '">' . htmlspecialchars($status, ENT_COMPAT, 'UTF-8') . '</span>';
    }

    /**
     * Renders Bootstrap pager based on PHPixies Paginate module.
     * @param $pager ORMPager
     * @param string $linkTemplate
     */
    public function pager($pager, $linkTemplate = '/?page=#page#')
    {
        if (!($pager instanceof ORMPager)) {
            return;
        }
        $pager->set_url_pattern($linkTemplate);
        if ($pager->num_pages > 1) { ?>
            <ul class="pagination pull-right clearfix">
                <li class="previous <?php if ($pager->page == 1): ?>disabled<?php endif; ?>"><a
                        href="<?php echo $pager->url($pager->page > 1 ? $pager->page - 1 : 1); ?>">&laquo;</a></li>
                <?php for ($page = 1; $page <= $pager->num_pages; $page++): ?>
                    <li <?php if ($page == $pager->page): ?>class="active"<?php endif; ?>><a href="<?php echo $pager->url($page); ?>"><?php echo $page; ?></a></li>
                <?php endfor; ?>
                <li class="next <?php if ($pager->page == $pager->num_pages): ?>disabled<?php endif; ?>"><a
                        href="<?php echo $pager->url($pager->page < $pager->num_pages ? $pager->page + 1 : $pager->num_pages); ?>">&raquo;</a></li>
            </ul> <?php
        }
    }

    public function addToCartLink($productId)
    {
        static $productsInCart;
        if (!$productsInCart) {
            $productsInCart = $this->controller->getProductsInCartIds();
        }
        if (!in_array($productId, $productsInCart)) { ?>
            <a href="/cart/view" class="btn btn-primary pull-left ladda-button buy-link js-add-to-cart-shortcut"
               data-product-id="<?php echo $productId; ?>"
               data-style="contract" title="Add to Cart"><span class="ladda-label"><span
                            class="glyphicon glyphicon-shopping-cart"></span></span></a><?php
        } else {
            ?><a href="/cart/view" class="btn btn-success btn-sm pull-left ladda-button buy-link added-to-cart" data-product-id="<?php echo $productId; ?>"
                    data-style="contract" title="Go to Cart">Added to Cart</a> <?php
        }
    }

	public function trim($string, $trimLength = 40) {
		echo $this->_smartTrim($string, $trimLength);
	}
	
	private function _smartTrim($text, $max_len, $trim_middle = false, $trim_chars = '...')
	{
		$text = trim($text);

		if (strlen($text) < $max_len) {

			return $text;

		} elseif ($trim_middle) {

			$hasSpace = strpos($text, ' ');
			if (!$hasSpace) {
				/**
				 * The entire string is one word. Just take a piece of the
				 * beginning and a piece of the end.
				 */
				$first_half = substr($text, 0, $max_len / 2);
				$last_half = substr($text, -($max_len - strlen($first_half)));
			} else {
				/**
				 * Get last half first as it makes it more likely for the first
				 * half to be of greater length. This is done because usually the
				 * first half of a string is more recognizable. The last half can
				 * be at most half of the maximum length and is potentially
				 * shorter (only the last word).
				 */
				$last_half = substr($text, -($max_len / 2));
				$last_half = trim($last_half);
				$last_space = strrpos($last_half, ' ');
				if (!($last_space === false)) {
					$last_half = substr($last_half, $last_space + 1);
				}
				$first_half = substr($text, 0, $max_len - strlen($last_half));
				$first_half = trim($first_half);
				if (substr($text, $max_len - strlen($last_half), 1) == ' ') {
					/**
					 * The first half of the string was chopped at a space.
					 */
					$first_space = $max_len - strlen($last_half);
				} else {
					$first_space = strrpos($first_half, ' ');
				}
				if (!($first_space === false)) {
					$first_half = substr($text, 0, $first_space);
				}
			}

			return $first_half.$trim_chars.$last_half;

		} else {

			$trimmed_text = substr($text, 0, $max_len);
			$trimmed_text = trim($trimmed_text);
			if (substr($text, $max_len, 1) == ' ') {
				/**
				 * The string was chopped at a space.
				 */
				$last_space = $max_len;
			} else {
				/**
				 * In PHP5, we can use 'offset' here -Mike
				 */
				$last_space = strrpos($trimmed_text, ' ');
			}
			if (!($last_space === false)) {
				$trimmed_text = substr($trimmed_text, 0, $last_space);
			}
			return $this->_removeTrailingPunctuation($trimmed_text).$trim_chars;

		}

	}

	/**
	 * Strip trailing punctuation from a line of text.
	 *
	 * @param  string $text The text to have trailing punctuation removed from.
	 *
	 * @return string       The line of text with trailing punctuation removed.
	 */
	private function _removeTrailingPunctuation($text)
	{
		return preg_replace("'[^a-zA-Z_0-9]+$'s", '', $text);
	}

    /**
     * @return mixed
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * @param mixed $controller
     */
    public function setController($controller)
    {
        $this->controller = $controller;
    }

    public function getOrderStatusLabelMapping()
    {
        return $this->orderStatusLabelMapping;
    }
}