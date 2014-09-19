<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 14.08.2014
 * Time: 17:11
 */


namespace VulnModule\Config;
use App\Pixie;

/**
 * Class ModelInfoRepository.
 * Collects model data, such as table name, etc.
 * @package VulnModule\Config
 */
class ModelInfoRepository 
{
    /**
     * @var array
     */
    protected $models = [];

    /**
     * @var Pixie
     */
    protected $pixie;

    public function __construct(Pixie $pixie)
    {
        $this->pixie = $pixie;
    }

    public function getModelInfo($modelName)
    {
        if (!$modelName) {
            throw new \InvalidArgumentException('Model name must not be empty.');
        }

        if ($this->models[$modelName] === false) {
            return false;
        }

        if (!is_array($this->models[$modelName])) {
            if (preg_match('/^tbl_/i', $modelName)) {
                $modelInfo = [
                    'table' => $modelName
                ];

            } else {
                try {
                    $model = $this->pixie->orm->get($modelName);
                    $modelInfo = [
                        'table' => $model->table
                    ];

                } catch (\Exception $e) {
                    $modelInfo = false;
                }
            }
            $this->models[$modelName] = $modelInfo;
        }

        return $this->models[$modelName];
    }
} 