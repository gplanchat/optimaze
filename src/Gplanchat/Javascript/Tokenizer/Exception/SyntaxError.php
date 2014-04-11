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


namespace Gplanchat\Javascript\Tokenizer\Exception;

/**
 * Syntax error exception type. Thrown when an invalid syntax is found syntax
 *
 * @package Gplanchat\Javascript\Tokenizer
 */
class SyntaxError
    extends \RuntimeException
    implements Exception
{
    /** @var string */
    private $sourceFile = null;

    /** @var int */
    private $sourceLine = null;

    /** @var int */
    private $sourceOffset = null;

    /**
     * @param string $message
     * @param int $file
     * @param int $line
     * @param int $offset
     */
    public function __construct($message, $file = null, $line = null, $offset = null)
    {
        parent::__construct($message);

        $this->sourceFile = $file;
        $this->sourceLine = $line;
        $this->sourceOffset = $offset;
    }

    /**
     * @return string
     */
    public function getSourceFile()
    {
        return $this->file;
    }

    /**
     * @return int
     */
    public function getSourceLine()
    {
        return $this->line;
    }

    /**
     * @return int
     */
    public function getSourceOffset()
    {
        return $this->offset;
    }

    public function __toString()
    {
        return sprintf('Parse error: %s in file "%s" on line %d', $this->getMessage(), $this->getSourceFile(), $this->getSourceLine())
            . PHP_EOL . $this->getTraceAsString();
    }
}
