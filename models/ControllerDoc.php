<?php

namespace pahanini\restdoc\models;

use phpDocumentor\Reflection\DocBlock;

/**
 * Class ControllerDoc
 *
 * @property string $shortDescription
 * @property string $longDescription
 */
class ControllerDoc extends Doc
{
    /**
     * @var string[] list of actions
     */
    public $actions;

    /**
     * @var \pahanini\restdoc\models\ModelDoc
     */
    public $model;

    /**
     * @var
     */
    public $path;

    /**
     * @var
     */
    protected $query = [];

    /**
     * @var array Keeps attached labels.
     */
    private $_labels = [];

    /**
     * @var string Long description
     */
    private $_longDescription;

    /**
     * @var string Short description of controller
     */
    private $_shortDescription;

    /**
     * @return string
     */
    public function getLongDescription()
    {
        return $this->_longDescription;
    }

    /**
     * @return string
     */
    public function getShortDescription()
    {
        return $this->_shortDescription;
    }

    /**
     * @param $name string
     * @return string
     */
    public function getQuery($name)
    {
        return isset($this->query[$name]) ? $this->query[$name] : [];
    }


    /**
     * @param $value
     * @return bool If label attached to doc
     */
    public function hasLabel($value)
    {
        return isset($this->_labels[$value]);
    }

    /**
     * Prepares doc
     */
    public function prepare()
    {
        parent::prepare();

        foreach ($this->getTagsByName('label') as $tag) {
            $this->_labels[$tag->getContent()] = true;
        }

        $queries = $this->getTagsByName('query');

        foreach(['GET', 'PUT', 'POST', 'DELETE'] as $action) {

            $this->query[$action] = array_filter($queries, function($item) use($action) {
                return $item->requestMethod === $action;
            });

        }

        if ($this->model) {
            $this->model->prepare();
        }
    }

    /**
     * @param $value
     */
    public function setShortDescription($value)
    {
        if (!$this->_shortDescription && $value) {
            $this->_shortDescription = $value;
        }
    }

    /**
     * @param $value
     */
    public function setLongDescription($value)
    {
        if (!$this->_longDescription && $value) {
            $this->_longDescription = $value;
        }
    }
}
