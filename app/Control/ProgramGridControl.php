<?php
/**
 * @author Jakub Stribrny <stribrny@1webit.cz>
 */

namespace Natsu\Control;


use App\Controls\Grido\Grid;
use Natsu\Model\ProgramModel;
use Nette\Http\Session;
use Nette\Utils\Html;

class ProgramGridControl extends BaseControl
{
    protected $model;
    protected $sectionId;
    protected $programStart;
    protected $programEnd;

    public function __construct(ProgramModel $model) {
        $this->model = $model;
    }

    /**
     * @param mixed $sectionId
     *
     * @return $this
     */
    public function setSectionId( $sectionId )
    {
        $this->sectionId = $sectionId;

        return $this;
    }

    /**
     * @param mixed $programStart
     *
     * @return $this
     */
    public function setProgramStart( $programStart )
    {
        $this->programStart = $programStart;

        return $this;
    }

    /**
     * @param mixed $programEnd
     *
     * @return $this
     */
    public function setProgramEnd( $programEnd )
    {
        $this->programEnd = $programEnd;

        return $this;
    }



    public function render() {
        $this->template->setFile( __DIR__ . "/templates/ProgramGridControl.latte" );
        $this->template->sectionId = $this->sectionId;

        $this->template->render();
    }

    public function createComponentGrid()
    {
        $gridModel = $this->model->getProgramsListForGrid();
        $gridModel->where("program.timeFrom >= '{$this->programStart->format('Y-m-d 00:00:00')}' AND program.timeFrom <= '{$this->programEnd->format('Y-m-d 23:59:59')}'");
        
        if($this->getParent()->getUser()->getIdentity()->roleId == 3){
            $gridModel->where("content.userId = ?", $this->getParent()->getUser()->getId());
        }else if($this->getParent()->getUser()->getIdentity()->roleId == 1 || $this->getParent()->getUser()->getIdentity()->roleId == 2){
            $gridModel->where("1=1");  
        }else{
            $gridModel->where("0=1");
        }
        
        
        $minMax = $this->model->getMaxMinTimeForGrid();

        $grid = new NatsuGrid\Grid();

        //$grid->addColumn( \Natsu\Control\NatsuGrid\Grid::COLUMN_TEXT, 'id', 'program_id' );
        $grid->addColumn(\Natsu\Control\NatsuGrid\Grid::COLUMN_TEXT, 'contentTitle', 'Název');
        $grid->addColumn( \Natsu\Control\NatsuGrid\Grid::COLUMN_TEXT, 'programType', 'Druh programu' );
        $grid->addColumn( \Natsu\Control\NatsuGrid\Grid::COLUMN_TEXT, 'programGenre', 'Sekce' );
        $grid->addColumn( \Natsu\Control\NatsuGrid\Grid::COLUMN_TEXT, 'roomTitle', 'Mistnost' );
        $grid->addColumn( \Natsu\Control\NatsuGrid\Grid::COLUMN_TEXT, 'author', 'Autor' );

        $grid->addFilterSpecific( NatsuGrid\Grid::FILTER_TEXT, 'contentTitle', 'Autor', function( $value, $source) {
            $source->where( 'content.title' )
                   ->like( '%s', '%' . $value . '%' )
            ;
        });
        $grid->addFilterSpecific(
            NatsuGrid\Grid::FILTER_TEXT, 'programType', 'Druh programu', function ( $value, $source ) {
            $source->where( 'programtype.title' )
                   ->like( '%s', '%' . $value . '%' )
            ;
        }
        );
        
        $grid->addFilterSpecific(
            NatsuGrid\Grid::FILTER_TEXT, 'programGenre', 'Sekce', function ( $value, $source ) {
            $source->where( 'programgenre.title' )
                   ->like( '%s', '%' . $value . '%' )
            ;
        }
        );

        $grid->addFilterSpecific(
            NatsuGrid\Grid::FILTER_TEXT, 'roomTitle', 'Mistnost', function ( $value, $source ) {
            $source->where( 'room.title' )
                   ->like( '%s', '%' . $value . '%' )
            ;
        }
        );

        $grid->addColumn( \Natsu\Control\NatsuGrid\Grid::COLUMN_DATETIME, 'timeFrom', 'Od-do' );
        $grid->addColumn( \Natsu\Control\NatsuGrid\Grid::COLUMN_DATETIME, 'timeTo', '' );

        $grid->setFilterDefault( 'timeFrom', $this->programStart->format( 'Y-m-d 00:00:00' ) );
        $grid->setFilterDefault( 'timeTo', $this->programEnd->format( 'Y-m-d 23:59:59' ) );

        $grid->addFilterSpecific(\Natsu\Control\NatsuGrid\Grid::FILTER_TEXT,'timeFrom', 'Od-do', function($value, $source){
                $source->where("timeFrom >= '$value'");
        });
        $grid->addFilterSpecific(
            \Natsu\Control\NatsuGrid\Grid::FILTER_TEXT, 'timeTo', '', function ( $value, $source ) {
            $source->where( "timeFrom <= '$value'" );
        }
        );


        $grid->setDataSource($gridModel);


        return $grid;
    }
}

interface IProgramGridControlFactory
{
    /**
     * @return ProgramGridControl
     */
    public function create();
}