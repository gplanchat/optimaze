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
 * @package Gplanchat\Javascript
 */

namespace Gplanchat\Javascript;

trait ExceptionTrait
{
    /** @var string */
    private $sourceFile = null;

    /** @var int */
    private $sourceLine = null;

    /** @var int */
    private $sourceLineOffset = null;

    /** @var int */
    private $sourceOffset = null;

    /**
     * @return string
     */
    public function getSourceFile()
    {
        return $this->sourceFile;
    }

    /**
     * @return int
     */
    public function getSourceLine()
    {
        return $this->sourceLine;
    }

    /**
     * @return int
     */
    public function getSourceLineOffset()
    {
        return $this->sourceLineOffset;
    }

    /**
     * @return int
     */
    public function getSourceOffset()
    {
        return $this->sourceOffset;
    }

    /**
     * @return string
     */
    abstract public function getMessage();

    /**
     * @return string
     */
    abstract public function getTraceAsString();

    /**
     * @return string
     */
    public function __toString()
    {
        if ($this->getSourceFile() === null) {
            return sprintf('Parse error: %s in source string on line %d (offset %d)',
                $this->getMessage(), $this->getSourceLine(), $this->getSourceOffset())
                . PHP_EOL . $this->getTraceAsString();
        }

        return sprintf('Parse error: %s in source file "%s" on line %d (offset %d)',
            $this->getMessage(), $this->getSourceFile(), $this->getSourceLine(), $this->getSourceOffset())
            . PHP_EOL . $this->getTraceAsString();
    }
}
