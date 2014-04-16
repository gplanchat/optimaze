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

trait RecursiveGrammarTrait
{
    use GrammarTrait;

    /** @var GrammarInterface[] */
    protected $children = [];

    /**
     * @return GrammarInterface[]
     */
    public function count()
    {
        return count($this->children);
    }

    /**
     * @return GrammarInterface[]
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @param GrammarInterface $node
     * @return $this
     */
    public function removeChild(GrammarInterface $node)
    {
        $key = array_search($node, $this->children);
        if ($key === false) {
            return $this;
        }

        $this->children[$key]->unsetParent();
        unset($this->children[$key]);

        return $this;
    }
}
