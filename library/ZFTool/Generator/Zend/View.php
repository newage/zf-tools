<?php

/**
 * Generate View code
 *
 * @category ZFScaffold
 * @package ZFScaffold_Generator
 * @subpackage Zend
 * @license New BSD
 * @author V.Leontiev <vadim.leontiev@gmail.com>
 * @link https://bitbucket.org/newage/zf-tool
 */
class ZFTool_Generator_Zend_View
    extends ZFTool_Generator_Abstract
        implements ZFTool_Generator_Interface
{
    private $_viewScriptDirectory;

    public function generate()
    {
        $this->_viewScriptDirectory = $this->_getModulePath() .
                DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'scripts' .
                DIRECTORY_SEPARATOR . $this->_config->tableName;

        $this->_generateFile(
            $this->_getBodyForCreate(),
            $this->_viewScriptDirectory . DIRECTORY_SEPARATOR . 'create.phtml'
        );
        $this->_generateFile(
            $this->_getBodyForUpdate(),
            $this->_viewScriptDirectory . DIRECTORY_SEPARATOR . 'update.phtml'
        );
        $this->_generateFile(
            $this->_getBodyForRead(),
            $this->_viewScriptDirectory . DIRECTORY_SEPARATOR . 'read.phtml'
        );
        $this->_generateFile('', $this->_viewScriptDirectory . DIRECTORY_SEPARATOR . 'delete.phtml');
        $this->_generateFile(
            $this->_generatePaginatorControl(),
            $this->_getModulePath() . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'scripts'
            . DIRECTORY_SEPARATOR . 'pagination_control.phtml'
        );
    }

    /**
     * Get body for read.phtml file
     *
     * @return string
     */
    protected function _getBodyForRead()
    {
        $body = '<a id="createUrl" href="<?php echo $this->url(array(\'action\'=>\'create\'))?>">Create</a>

<table id="'.$this->_config->moduleName.ucfirst($this->_config->tableName).'" class="dataGrid">
<!-- count paginator -->
<?php if (count($this->paginator)) : ?>
    <?php $firstRow = $this->paginator->getAdapter()->getItems(0,1); ?>
    <tr>
        <!-- get column name -->
        <?php foreach (array_keys($firstRow[0]) as $columnName) : ?>
            <th><?php echo ucfirst($columnName); ?></th>
        <?php endforeach; ?>

        <th>Update</th>
        <th>Delete</th>
    </tr>

    <!-- get all items of paginator -->
    <?php foreach ($this->paginator as $item) : ?>
        <tr>
        <!-- get item property -->
        <?php foreach ($item as $key=>$column) : ?>
            <td><?php echo $column ?></td>

            <?php if ($key == \''.$this->_getAutoincrementColumnName().'\') : ?>
                <?php $id = $column; ?>
            <?php endif; ?>
        <?php endforeach; ?>

        <td><a href="<?php echo $this->url(array(\'action\'=>\'update\', \'id\'=>$id)) ?>">X</a></td>
        <td><a href="<?php echo $this->url(array(\'action\'=>\'delete\', \'id\'=>$id)) ?>">X</a></td>
        </tr>
    <?php endforeach; ?>

    <tr class="paginator">
        <td colspan="<?php echo count($firstRow[0]) + 2 ?>">
        <?php echo $this->paginationControl($this->paginator,
                                          \'Sliding\',
                                          \'pagination_control.phtml\'); ?>
        </td>
    </tr>

<?php endif; ?>
</table>';

        return $body;
    }

    /**
     * Get column name with autoincrement
     *
     * @return integer
     */
    protected function _getAutoincrementColumnName()
    {
        $id = 'id';
        foreach ($this->_config as $fieldName => $fieldOptions) {
            if ($fieldOptions instanceof Zend_Config && $fieldOptions->PRIMARY === true) {
                $id = $fieldName;
                break;
            }
        }
        return $id;
    }

    /**
     * Get body for create.phtml file
     *
     * @return string
     */
    protected function _getBodyForCreate()
    {
        $body = '<?php $this->form->setAction($this->url()); ?>
<?php echo $this->form; ?>

<a href="<?php echo $this->url(array(\'action\'=>\'read\'))?>">&lt;- To read</a>';

        return $body;
    }

    /**
     * Get body for update.phtml file
     *
     * @return string
     */
    protected function _getBodyForUpdate()
    {
        return $this->_getBodyForCreate();
    }

    /**
     * Generate paginator controls
     *
     * @return string
     */
    protected function _generatePaginatorControl()
    {
        $body = '<?php if ($this->pageCount): ?>
    <div class="paginationControl">
    <!-- Previous page link -->
    <?php if (isset($this->previous)): ?>
        <a href="<?php echo $this->url(array(\'page\' => $this->previous)); ?>">
            &lt; Previous
        </a> |
    <?php else: ?>
      <span class="disabled">&lt; Previous</span> |
    <?php endif; ?>

    <!-- Numbered page links -->
    <?php foreach ($this->pagesInRange as $page): ?>
        <?php if ($page != $this->current): ?>
            <a href="<?php echo $this->url(array(\'page\' => $page)); ?>">
            <?php echo $page; ?>
            </a> |
        <?php else: ?>
            <?php echo $page; ?> |
        <?php endif; ?>
    <?php endforeach; ?>

    <!-- Next page link -->
    <?php if (isset($this->next)): ?>
        <a href="<?php echo $this->url(array(\'page\' => $this->next)); ?>">
            Next &gt;
        </a>
    <?php else: ?>
        <span class="disabled">Next &gt;</span>
    <?php endif; ?>
    </div>
<?php endif; ?>';

        return $body;
    }
}
