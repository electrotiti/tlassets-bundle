<?php

namespace TlAssetsBundle\Extension\Twig;

class TlAssetsNode extends \Twig_Node
{
    public function __construct($node, $attributes, $nbLine, $tag)
    {
        $attributes = array_merge(array('var_name'=>'asset_url'),$attributes);
        parent::__construct($node, $attributes, $nbLine, $tag);
    }

    public function compile(\Twig_Compiler $compiler)
    {
        foreach($this->getAttribute('assets') as $path) {
            $compiler
                ->write('$context[')
                ->repr($this->getAttribute('var_name'))
                ->raw('] = ')
                ->string($path)
                ->raw(";\n")
                ->subcompile($this->getNode('content'))
            ;
        }
    }
}