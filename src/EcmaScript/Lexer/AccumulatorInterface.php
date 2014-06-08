<?php

namespace Gplanchat\EcmaScript\Lexer;


use Gplanchat\Lexer\Grammar;
use Gplanchat\Lexer\Grammar\GrammarInterface;
use Gplanchat\EcmaScript\Lexer\Rule\RuleInterface;
use Gplanchat\Tokenizer\TokenizerInterface;
use SplDoublyLinkedList;

interface AccumulatorInterface
{
    /**
     * @param RuleInterface $rule
     * @param GrammarInterface $grammar
     * @param SplDoublyLinkedList $stack
     */
    public function __construct(RuleInterface $rule, GrammarInterface $grammar, SplDoublyLinkedList $stack = null);

    /**
     * @param TokenizerInterface $tokenizer
     * @return GrammarInterface
     */
    public function __invoke(TokenizerInterface $tokenizer);

    /**
     * @param SplDoublyLinkedList $stack
     * @return $this
     */
    public function setStack(SplDoublyLinkedList $stack);

    /**
     * @return SplDoublyLinkedList
     */
    public function getStack();

    /**
     * @param GrammarInterface $grammar
     * @return $this
     */
    public function setGrammar(GrammarInterface $grammar);

    /**
     * @return GrammarInterface
     */
    public function getGrammar();

    /**
     * @param RuleInterface $rule
     * @return $this
     */
    public function setRule(RuleInterface $rule);

    /**
     * @return RuleInterface
     */
    public function getRule();
}
