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

namespace Gplanchat\Lexer\Grammar;

class ObjectEntry
    implements RecursiveGrammarInterface
{
    use LeftAssociativeGrammarTrait;
    use Optimization\MandatoryGrammarTrait;
    use GrammarRecursiveDumpTrait;

    /**
     * @var string
     */
    protected $identifier = null;

    /**
     * @var int
     */
    protected $type = null;

    /**
     * @param string $identifier
     * @param int $type
     */
    public function __construct($identifier, $type)
    {
        $this->identifier = $identifier;
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }
}
