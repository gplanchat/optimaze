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

interface GrammarInterface
{
    /**
     * @return int
     */
    public function getType();

    /**
     * @return GrammarInterface|null
     */
    public function getParent();

    /**
     * @param RecursiveGrammarInterface $parent
     * @return $this
     */
    public function setParent(RecursiveGrammarInterface $parent);

    /**
     * @return $this
     */
    public function unsetParent();

    /**
     * @return $this
     */
    public function optimize();

    /**
     * @return string
     */
    public function dump();
}
