<?php

/**
 * Class GlobalNamespaceClass
 */
class GlobalNamespaceClass
{

    /**
     * @param string $c
     */
    public function publicMethodWitDocBlock($c)
    {
        return 'c';
    }

    public function publicMethodWithoutDocBlock($c)
    {
        return 'c1';
    }

    /**
     * @param int $b
     */
    protected function protected_method_with_doc_block($b)
    {
        // no-op
    }

    protected function protected_method_without_doc_block($b)
    {
        return 'b';
    }

    /**
     * @param array $a
     */
    private function privateMethodWithDocBlock(array $a)
    {
        return 'a';
    }

    private function privateMethodWithoutDocBlock(array $a)
    {
        return 'foo';
    }
}
