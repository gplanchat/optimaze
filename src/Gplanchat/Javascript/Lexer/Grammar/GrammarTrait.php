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

namespace Gplanchat\Javascript\Lexer\Grammar;

trait GrammarTrait
{
    /** @var int */
    protected $type = null;

    /** @var GrammarInterface */
    protected $parent = null;

    /**
     * @param RecursiveGrammarInterface $parent
     * @return $this
     */
    public function setParent(RecursiveGrammarInterface $parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return RecursiveGrammarInterface|null
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param int $type
     * @return $this
     */
    protected function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return $this
     */
    public function optimize()
    {
        return $this;
    }

    /**
     * @param int $level
     * @return string
     */
    public function dump($level = 0)
    {
        $separatorPosition = strrpos(static::class, '\\');
        $namespace = substr(static::class, 0, $separatorPosition);
        $class = substr(static::class, $separatorPosition + 1);
        $padding = str_pad('', $level * 2, ' ');

        return sprintf("\n%1\$s%2\$s [%3\$s]", $padding, $class, $namespace);
    }
}
