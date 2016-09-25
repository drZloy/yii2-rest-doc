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
     * @var string[][] asides
     */
    public $asides;

    /**
     * @var \pahanini\restdoc\models\ModelDoc
     */
    public $model;

    /**
     * @var
     */
    public $path;

    /**
     * @var \pahanini\restdoc\tags\QueryTag[]
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
     * @param $name string
     * @return array|string
     */
    public function getAsides($name = '')
    {
        return !empty($name) ? (!empty($this->asides[$name]) ? $this->asides[$name] : []) : $this->asides;
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

        if ($this->model) {
            $this->model->prepare();
        }

        foreach(['notice', 'warning', 'success'] as $item) {
            $this->asides[$item] = array_map(
                function($item) { return $item->getContent(); },
                $this->getTagsByName($item)
            );
        }

        $queries = $this->getTagsByName('query');

        foreach(['GET', 'PUT', 'POST', 'DELETE'] as $action) {

            $this->query[$action] = array_filter($queries, function($item) use($action) {
                return $item->requestMethod === $action;
            });

            if($this->model) {
                $modelFields = $this->model->getFields();
                foreach($this->query[$action] as $queryTag) {
                    foreach($modelFields as $modelField) {
                        if($queryTag->variableName === $modelField->getName()) {
                            $queryTag->type = $modelField->getType();
                            $queryTag->setDescription($modelField->getDescription());
                            break;
                        }
                    }
                }
            }

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
