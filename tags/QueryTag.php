<?php

namespace pahanini\restdoc\tags;

use phpDocumentor\Reflection\DocBlock\Tag;

class QueryTag extends Tag
{
    public $defaultValue = '';

    public $variableName = '';

    public $requestMethod = '';

    public $isRequired = false;

    /**
     * {@inheritdoc}
     */
    public function setContent($content)
    {
        Tag::setContent($content);
        $parts = preg_split('/\s+/Su', $this->description, 3);

        $this->requestMethod = array_shift($parts);
        $paramName = array_shift($parts);

        $tmp = explode('=', $paramName);
        if (count($tmp) == 2) {
            $this->defaultValue = $tmp[1];
            $this->variableName = $tmp[0];
        } else {
            $tmp = explode(' ', $paramName);
            $this->variableName = $tmp[0];
        }

        if(false !== strpos($this->variableName, '*')) {
            $this->isRequired = true;
            $this->variableName = str_replace('*', '', $this->variableName);
        }

        $this->setDescription(join(' ', str_replace("\n", " ", $parts)));

        return $this;
    }
}