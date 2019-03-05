<?php
namespace MMHK\SMS;

use \MMHK\SMS\Contracts\MessageInterface;

/**
 * Class Message.
 * @property string $to
 * @property string $content
 * @property string $template
 * @property string $data
 */
class Message implements MessageInterface
{
    /**
     * @var string to PhoneNumber
     */
    protected $to;
    /**
     * @var string
     */
    protected $content;
    /**
     * @var string
     */
    protected $template;
    /**
     * @var array
     */
    protected $data = [];

    /**
     * Message constructor.
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        foreach ($attributes as $property => $value) {
            if (property_exists($this, $property)) {
                $this->$property = $value;
            }
        }
    }

    /**
     * @return mixed|string
     */
    public function getContent()
    {
         if (! empty($this->content)) {
             return $this->content;
         }
        return $this->parseTemplate();
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param $template
     * @return $this
     */
    public function setTemplate($template)
    {
        $this->template = $template;
        return $this;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param array $data
     * @return $this
     */
    public function setData(array $data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @param $property
     * @return mixed
     */
    public function __get($property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }

    public function parseTemplate()
    {
        $tpl = $this->getTemplate();
        $keys = array_keys($this->getData());
        $values = array_values($this->getData());
        return str_replace($keys, $values, $tpl);
    }

    public function getTo() {
        return $this->to;
    }
}