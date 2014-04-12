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
 * @package Gplanchat\Javascript\Lexer
 */

namespace Gplanchat\Javascript\Lexer\Exception;

use Gplanchat\Javascript\ExceptionTrait;

/**
 * Lexical error exception type. Thrown when an invalid lexical form is found
 *
 * @package Gplanchat\Javascript\Lexer\Exception
 */
class LexicalError
    extends \RuntimeException
    implements Exception
{
    use ExceptionTrait;

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
        $this->sourceLine = $line ?: -1;
        $this->sourceOffset = $offset;
    }
}
