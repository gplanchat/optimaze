<?php
/**
 * Created by PhpStorm.
 * User: Greg
 * Date: 17/03/14
 * Time: 20:10
 */

namespace Gplanchat\Tokenizer;

use Gplanchat\Tokenizer\Source\SourceInterface;

interface TokenizerInterface
    extends \Iterator
{
    public function __construct(SourceInterface $source);

    public function get();
}
