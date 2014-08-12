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
use VulnModule\VulnInjection;


/**
 * Class Helper
 * @property-read Pixie pixie
 * @package App\Core\View
 */
class Helper extends \PHPixie\View\Helper
{
    protected $aliases = array(
        '_' => 'output',
        '_token' => 'token',
        '_dump' => 'dump'
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
     */
    public function token($tokenId)
    {
        $service = $this->pixie->getVulnService();

        if (!$service || $service->csrfIsEnabled()) {
            echo '';
            return;
        }
        echo $service->renderTokenField(Page::TOKEN_PREFIX . $tokenId);
    }

    /**
     * Dump all passed vars to output.
     */
    public function dump()
    {
        echo call_user_func_array('App\\Debug::dump', func_get_args());
    }
} 