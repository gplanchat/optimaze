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

namespace Gplanchat\Lexer\Grammar\Optimization;

use Gplanchat\Lexer\Grammar\GrammarInterface;
use Gplanchat\Lexer\Grammar\RecursiveGrammarInterface;

/**
 * Class OptionalGrammarTrait
 * @package Gplanchat\Lexer\Grammar
 */
trait OptionalGrammarTrait
{
    /**
     * @return int
     */
    abstract function count();

    /**
     * @return RecursiveGrammarInterface
     */
    abstract function getParent();

    /**
     * @return GrammarInterface[]
     */
    abstract function getChildren();

    /**
     * @param GrammarInterface $child
     * @return GrammarInterface
     */
    abstract function removeChild(GrammarInterface $child);

    /**
     * @param GrammarInterface $child
     * @return GrammarInterface
     */
    abstract function addChild(GrammarInterface $child);

    /**
     * @return GrammarInterface
     */
    public function optimize()
    {
        if (count($this) <= 1) {
            $parent = $this->getParent();
            if ($parent !== null) {
                $parent->removeChild($this);

                foreach ($this->getChildren() as $child) {
                    $parent->addChild($child);
                    $this->removeChild($child);
                }
            }
        }

        return $this;
    }
}
