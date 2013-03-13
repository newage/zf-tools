<?php

/**
 * Implement generate public method
 *
 * @category Library
 * @package Library_Generator
 * @author Vadim Leontiev <vadim.leontiev@gmail.com>
 * @see https://bitbucket.org/newage/zf-tool
 * @since php 5.1 or higher
 */
interface ZFTool_Generator_Interface
{

    /**
     * Generate Code
     *
     * @return bool
     */
    public function generate();
}
