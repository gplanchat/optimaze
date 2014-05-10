<?php
/**
 * This file is part of gplanchat/php-javascript-tokenizer
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Grégory Planchat <g.planchat@gmail.com>
 * @licence GNU General Public Licence
 * @package Gplanchat\Tokenizer
 */

namespace Gplanchat\Tokenizer;

use Gplanchat\Javascript\Tokenizer as Javascript;

/**
 * Class Token.
 * Data store for token
 *
 * @package Gplanchat\Tokenizer
 */
class Token
{
    /**
     * @var int|string
     */
    private $type = null;

    /**
     * @var string
     */
    private $value = null;

    /**
     * @var int
     */
    private $start = null;

    /**
     * @var int
     */
    private $end = null;

    /**
     * @var string
     */
    private $path = null;

    /**
     * @var int
     */
    private $line = null;

    /**
     * @var int
     */
    private $lineOffset = null;

    /**
     * @var null|string
     */
    private $assignOperator = null;

    /**
     * @param string|int $type
     * @param string $value
     * @param int $start
     * @param int $end
     * @param string $path
     * @param int $line
     * @param int $lineOffset
     * @param string|null $assignOperator
     */
    public function __construct($type, $value, $start, $end, $path, $line, $lineOffset, $assignOperator = null)
    {
        $this->setType($type);
        $this->setValue($value);
        $this->setStart($start);
        $this->setEnd($end);
        $this->setPath($path);
        $this->setLine($line);
        $this->setLineOffset($lineOffset);
        $this->setAssignOperator($assignOperator);
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
     * @return int
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * @param string $path
     * @return $this
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param int $line
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
     * @param mixed $lineOffset
     * @return $this
     */
    public function setLineOffset($lineOffset)
    {
        $this->lineOffset = $lineOffset;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLineOffset()
    {
        return $this->lineOffset;
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

    /**
     * @return string
     */
    public function dump()
    {
        $re = new \ReflectionClass(Javascript\TokenizerInterface::class);
        $constants = $re->getConstants();

        $key = array_search($this->getType(), $constants);
        return sprintf("\n%s [%s] - line %d, offset %d",
            str_pad($key, 25, ' ', STR_PAD_RIGHT),
            $this->getValue(), $this->getLine(), $this->getLineOffset()
        );
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->dump();
    }
}
