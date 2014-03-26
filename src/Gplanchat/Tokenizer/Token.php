<?php
/**
 * Created by PhpStorm.
 * User: Greg
 * Date: 17/03/14
 * Time: 10:01
 */

namespace Gplanchat\Tokenizer;

class Token
{
    private $type;
    private $value;
    private $start;
    private $end;
    private $line;
    private $assignOperator;

    /**
     * @param string|int $type
     * @param string $value
     * @param int $start
     * @param int $end
     * @param int $line
     * @param string|null $assignOperator
     */
    public function __construct($type, $value, $start, $end, $line, $assignOperator = null)
    {
        $this->type = $type;
        $this->value = $value;
        $this->start = $start;
        $this->end = $end;
        $this->line = $line;
        $this->assignOperator = $assignOperator;
    }

    /**
     * @param mixed $assignOperator
     * @return $this
     */
    public function setAssignOperator($assignOperator)
    {
        $this->assignOperator = $assignOperator;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAssignOperator()
    {
        return $this->assignOperator;
    }

    /**
     * @param mixed $end
     * @return $this
     */
    public function setEnd($end)
    {
        $this->end = $end;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * @param mixed $line
     * @return $this
     */
    public function setLine($line)
    {
        $this->line = $line;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLine()
    {
        return $this->line;
    }

    /**
     * @param mixed $start
     * @return $this
     */
    public function setStart($start)
    {
        $this->start = $start;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * @param mixed $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $value
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}
