<?php
/**
 * Created by PhpStorm.
 * User: Greg
 * Date: 21/04/14
 * Time: 22:55
 */

namespace Gplanchat\EcmaScript\Lexer;


use Generator;
use Gplanchat\Lexer\Grammar;
use Gplanchat\Lexer\Grammar\GrammarInterface;
use Gplanchat\EcmaScript\Lexer\Rule\RuleInterface;
use Gplanchat\Tokenizer\Token;
use Gplanchat\Tokenizer\TokenizerInterface as BaseTokenizerInterface;
use Gplanchat\EcmaScript\Tokenizer\TokenizerInterface;
use SplDoublyLinkedList;
use SplStack;

class Accumulator
    implements AccumulatorInterface
{
    /**
     * @var RuleInterface
     */
    protected $rule = null;

    /**
     * @var GrammarInterface
     */
    protected $grammar = null;

    /**
     * @var SplDoublyLinkedList
     */
    protected $stack = null;

    /**
     * @param RuleInterface $rule
     * @param GrammarInterface $grammar
     * @param SplDoublyLinkedList $stack
     */
    public function __construct(RuleInterface $rule, GrammarInterface $grammar, SplDoublyLinkedList $stack = null)
    {
        $this->rule = $rule;
        $this->grammar = $grammar;
        if ($stack !== null) {
            $this->stack = $stack;
        } else {
            $this->stack = new SplStack();
        }
    }

    /**
     * @param BaseTokenizerInterface $tokenizer
     * @return GrammarInterface
     */
    public function __invoke(BaseTokenizerInterface $tokenizer)
    {
        while (true) {
            /** @var Token $token */
            $token = $tokenizer->current();
            if ($token->getType() === TokenizerInterface::TOKEN_END) {
                break;
            }
            if ($this->stack->isEmpty()) {
                $this->stack->push(new ExecutionWrapper($this->rule->run($this->grammar, $tokenizer)));
            }

            /** @var ExecutionWrapper $generator */
            $generator = $this->stack->pop();
            if (!$generator->valid()) {
                continue;
            }
            $this->stack->push($generator);

            $generator($this->stack, $tokenizer);
        }

        return $this->grammar;
    }

    /**
     * @param SplDoublyLinkedList $stack
     * @return $this
     */
    public function setStack(SplDoublyLinkedList $stack)
    {
        $this->stack = $stack;

        return $this;
    }

    /**
     * @return SplDoublyLinkedList
     */
    public function getStack()
    {
        return $this->stack;
    }

    /**
     * @param GrammarInterface $grammar
     * @return $this
     */
    public function setGrammar(GrammarInterface $grammar)
    {
        $this->grammar = $grammar;

        return $this;
    }

    /**
     * @return GrammarInterface
     */
    public function getGrammar()
    {
        return $this->grammar;
    }

    /**
     * @param RuleInterface $rule
     * @return $this
     */
    public function setRule(RuleInterface $rule)
    {
        $this->rule = $rule;

        return $this;
    }

    /**
     * @return RuleInterface
     */
    public function getRule()
    {
        return $this->rule;
    }
}
