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
 * @package Gplanchat\EcmaScript\Lexer
 */

namespace Gplanchat\EcmaScript\Lexer;

use Closure;
use Generator;
use Gplanchat\Lexer\Grammar\RecursiveGrammarInterface;
use Gplanchat\Tokenizer\TokenizerInterface;
use SplDoublyLinkedList;

/**
 * Class ExecutionWrapper
 * @package Gplanchat\EcmaScript\Lexer
 */
class ExecutionWrapper
{
    /**
     * @var bool
     */
    protected $started = false;

    /**
     * @var Generator
     */
    protected $generator = null;

    /**
     * @param Generator $generator
     */
    public function __construct(Generator $generator)
    {
        $this->generator = $generator;
    }

    /**
     * @param SplDoublyLinkedList $stack
     * @param TokenizerInterface $tokenizer
     * @return mixed
     */
    public function __invoke(SplDoublyLinkedList $stack, TokenizerInterface $tokenizer)
    {
        if ($this->started !== false) {
            $this->generator->next();
        }
        $this->started = true;

        /** @var Generator $item */
        $item = $this->generator->current();
        if ($item !== null) {
            $stack->push(new static($item));
        }

        return $item;
    }

    /**
     * @return bool
     */
    public function valid()
    {
        return $this->generator->valid();
    }
}
