<?php
/**
 * @author Jakub Stribrny <stribrny@1webit.cz>
 */

namespace Natsu\Control\NatsuGrid;


use Natsu\Forms\BaseForm;
use Nette\Application\AbortException;
use Nette\Application\UI\Control;
use Nette\Utils\ArrayHash;

class Grid extends Control
{

    const COLUMN_TEXT = 'text';
    const COLUMN_DATETIME = 'datetime';

    const FILTER_TEXT = 'text';

    /** @var DibiFluent */
    protected $dataSource;

    protected $filters = [];
    protected $filterDefaults = [];

    protected $columns = [];

    protected $filterState = [];
    protected $sortState = [];


    public function setDataSource(\DibiFluent $dataSource)
    {
        $this->dataSource = $dataSource;
    }

    /**
     * Apply dibi fluent where from array hash
     * @param ArrayHash $filters
     *
     * @throws Exception
     */
    public function filter(ArrayHash $filters)
    {
        if(!$this->dataSource)
            throw new Exception('Data source is missing');

        foreach($filters as $key => $value) {
            $value = trim($value);
            if(empty($value))
                continue;

            if(isset($this->filters[$key]) && !is_null($this->filters[$key]['callback'])) {
                call_user_func( $this->filters[ $key ][ 'callback' ], $value, $this->dataSource);
                continue;
            }


            if(is_numeric($value))
                $this->dataSource->where($key . " = ?", $value);
            else
                $this->dataSource->where($key)->like("?", '%' . $value . '%');
        }

       // echo $this->dataSource; exit;
    }

    public function sort($condition)
    {
        if(!empty($condition))
            list($key,$direction) = explode(':', $condition);

        if(!empty($key) && !empty($direction))
            $this->dataSource->orderBy($key . ' ' . $direction );
    }

    public function getData()
    {
        if($this->filterState)
            $this->filter($this->filterState);
        if($this->sortState)
            $this->sort($this->sortState);

        return $this->dataSource->fetchAll();
    }

    public function handleModifiers()
    {
        $this->filterState = ArrayHash::from( $_POST[ 'natsu_filter' ] );
        $this->sortState = $_POST[ 'natsu_sort'];
    }

    /*
    public function handleFilter()
    {
        $this->filterState = ArrayHash::from($_POST['natsu_filter']);

    }

    public function handleSort()
    {
        $this->sortState = $_POST['natsu_sort'];
    }
    */

    /**
     * 'name'
     */
    public function addColumns(array $columns){
        foreach($columns as $column) {

        }
    }

    public function addColumn($type, $name, $label) {
        $this->columns[] = ArrayHash::from([
            'type' => $type, 'key' => $name, 'label' => $label
                                           ]);
    }

    public function getColumns() {
        return $this->columns;
    }

    public function render()
    {
        $this->template->setFile(__DIR__ . '/Grid.latte');
        $this->template->data = $this->getData();
        $this->template->columns = $this->getColumns();

        $filterValues = [];
        foreach($this->columns as $column) {
            if(isset($this->filterDefaults[$column['key']]))
                $filterValues[$column['key']] = $this->filterDefaults[$column['key']];
            else
                $filterValues[$column['key']] = '';
        }
        $this->template->filterValues = $filterValues;

        $this->template->render();
    }

    public function addFilterSpecific( $type, $key, $label, $callback = null )
    {
        $this->filters[$key] = ArrayHash::from(
            [
                'type' => $type, 'key' => $key, 'label' => $label, 'callback' => $callback
            ]
        );
    }

    public function setFilterDefault($filterName, $value)
    {
        $this->filterDefaults[$filterName] = $value;
    }
}

class Exception extends \Exception {}