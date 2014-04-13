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

namespace Gplanchat\Javascript\Lexer\Rule;

use Gplanchat\Javascript\Lexer\TokenizerNavigationAwareTrait;
use Gplanchat\Javascript\Lexer\Grammar\RecursiveGrammarInterface;
use Gplanchat\Javascript\Tokenizer\TokenizerInterface;
use Gplanchat\Tokenizer\Token;
use Traversable;

class TokenSeekerIterator
    implements RuleInterface
{
    /**
     * @var RuleInterface[]
     */
    protected $iterator = null;

    /**
     * @var int
     */
    protected $loops = 0;

    /**
     * @param RuleInterface[] $tokenSeekers
     * @param int $loops
     */
    public function __construct(array $tokenSeekers, $loops = 0)
    {
        $this->iterator = new \ArrayIterator($tokenSeekers);
    }

    /**
     * @return int
     */
    public function getLoops()
    {
        return $this->loops;
    }

    /**
     * @param RecursiveGrammarInterface $parent
     * @param TokenizerInterface $tokenizer
     * @throws \RuntimeException
     */
    public function parse(RecursiveGrammarInterface $parent, TokenizerInterface $tokenizer)
    {
        if (!$this->iterator->valid()) {
            if ($this->getLoops() <= 0) {
                throw new \RuntimeException('No token seeker available or ended loop');
            }
            $this->loops--;
            $this->iterator->rewind();
        }

        /** @var RuleInterface $current */
        $current = $this->iterator->current();
        if ($current !== null) {
            $current->parse($parent, $tokenizer);
        }
        $this->iterator->next();
    }
}
