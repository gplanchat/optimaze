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

use Gplanchat\Lexer\Grammar;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 *
 */
class OptionalGrammarTraitTest
    extends \PHPUnit_Framework_TestCase
{
    public function optionalGrammarsDataProvider()
    {
        return [
            [Grammar\AssignmentExpression::class,     Grammar\ConditionalExpression::class],
            [Grammar\ConditionalExpression::class,    Grammar\OrExpression::class],
            [Grammar\OrExpression::class,             Grammar\AndExpression::class],
            [Grammar\AndExpression::class,            Grammar\BitwiseOrExpression::class],
            [Grammar\BitwiseOrExpression::class,      Grammar\BitwiseXorExpression::class],
            [Grammar\BitwiseXorExpression::class,     Grammar\BitwiseAndExpression::class],
            [Grammar\BitwiseAndExpression::class,     Grammar\EqualityExpression::class],
            [Grammar\EqualityExpression::class,       Grammar\RelationalExpression::class],
            [Grammar\RelationalExpression::class,     Grammar\ShiftExpression::class],
            [Grammar\ShiftExpression::class,          Grammar\AdditiveExpression::class],
            [Grammar\AdditiveExpression::class,       Grammar\MultiplicativeExpression::class],
            [Grammar\MultiplicativeExpression::class, Grammar\UnaryExpression::class],
            [Grammar\MemberExpression::class,         Grammar\PrimaryExpression::class]
        ];
    }

    /**
     * @dataProvider optionalGrammarsDataProvider
     * @param $parentClass
     * @param $childClass
     */
    public function testOptimizationWithOneChild($parentClass, $childClass)
    {
        /** @var Grammar\RecursiveGrammarInterface|MockObject */
        $root = $this->getMock(Grammar\Expression::class, ['addChild', 'removeChild', 'count'], [], '', true, true, true, false, true);

        /** @var Grammar\RecursiveGrammarInterface|MockObject */
        $grammar = new $parentClass();
        $root->addChild($grammar);

        $child = $this->getMock($childClass);
        $grammar->addChild($child);

        $grammar->optimize();

        $this->assertCount(1, $root);
        $this->assertCount(0, $grammar);
    }

    /**
     * @dataProvider optionalGrammarsDataProvider
     * @param $parentClass
     * @param $childClass
     */
    public function testOptimizationWithMultipleChildren($parentClass, $childClass)
    {
        /** @var Grammar\RecursiveGrammarInterface|MockObject */
        $root = $this->getMock(Grammar\Expression::class, ['addChild', 'removeChild', 'count'], [], '', true, true, true, false, true);

        /** @var Grammar\RecursiveGrammarInterface|MockObject */
        $grammar = new $parentClass();
        $root->addChild($grammar);

        $root->expects($this->never())
            ->method('addChild')
        ;
        $root->expects($this->never())
            ->method('removeChild')
        ;

        for ($i = 0; $i < 3; $i++) {
            $child = $this->getMock($childClass);
            $grammar->addChild($child);
        }

        $grammar->optimize();

        $this->assertCount(1, $root);
        $this->assertCount(3, $grammar);
    }
}
