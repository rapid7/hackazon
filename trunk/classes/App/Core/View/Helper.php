<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 08.08.2014
 * Time: 16:43
 */


namespace App\Core\View;
use App\Page;
use App\Pixie;
use PHPixie\Paginate\Pager\ORM as ORMPager;
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

    protected $aliases = array(
        '_' => 'output',
        '_token' => 'token',
        '_dump' => 'dump',
        '_order_status' => 'order_status',
        '_pager' => 'pager'
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
    public function escape($str, $fieldName = null)
    {
        $service = $this->pixie->getVulnService();

        if (!$fieldName || !$service) {
            return htmlspecialchars($str, ENT_COMPAT, 'UTF-8');
        }

        $vulns = $service->getConfig()->getVulnerabilities();

        $xss = $vulns['xss'];
        $fields = $service->getConfig()->getFields();
        $field = $fields[$fieldName];

        if ((!isset($xss['enabled']) || $xss['enabled'] == true) && is_array($field) && in_array('xss', $field)) {
            return $str;
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
} 