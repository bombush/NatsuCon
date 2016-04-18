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
        $this->template->render();
    }

    public function createComponentGrid()
    {
        $gridModel = $this->model->getProgramsListForGrid();
        $minMax = $this->model->getMaxMinTimeForGrid();

        $grid = new NatsuGrid\Grid();

        $grid->addColumn( \Natsu\Control\NatsuGrid\Grid::COLUMN_TEXT, 'id', 'program_id' );
        $grid->addColumn(\Natsu\Control\NatsuGrid\Grid::COLUMN_TEXT, 'contentTitle', 'Title');
        $grid->addColumn( \Natsu\Control\NatsuGrid\Grid::COLUMN_TEXT, 'programGenre', 'Druh programu' );
        $grid->addColumn( \Natsu\Control\NatsuGrid\Grid::COLUMN_TEXT, 'roomTitle', 'Mistnost' );
        $grid->addColumn( \Natsu\Control\NatsuGrid\Grid::COLUMN_TEXT, 'author', 'Autor' );

        $grid->addFilterSpecific( NatsuGrid\Grid::FILTER_TEXT, 'contentTitle', 'Autor', function( $value, $source) {
            $source->where( 'content.title' )
                   ->like( '%s', '%' . $value . '%' )
            ;
        });
        $grid->addFilterSpecific(
            NatsuGrid\Grid::FILTER_TEXT, 'programGenre', 'Druh programu', function ( $value, $source ) {
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
        $grid->setFilterDefault('timeFrom', $minMax['minTimeFrom']);
        $grid->setFilterDefault('timeTo', $minMax['maxTimeFrom']);
        $grid->addFilterSpecific(\Natsu\Control\NatsuGrid\Grid::FILTER_TEXT,'timeFrom', 'Od-do', function($value, $source){
                $source->where("timeFrom >= '$value'");
        });
        $grid->addFilterSpecific(
            \Natsu\Control\NatsuGrid\Grid::FILTER_TEXT, 'timeTo', '', function ( $value, $source ) {
            $source->where( "timeFrom <= '$value'" );
        }
        );


        $grid->setDataSource($gridModel);

        /*$grid = new Grid( $this, 'grid' );
        $grid->setRowCallback(function($parm){
            $row = Html::el('tr')->addAttributes(
                [
                    'class' => ['grid-row', 'grid-row-' . $parm->id],
                    'data-id' => $parm->id
            ]);
            return $row;
        });

        $grid->setDefaultPerPage( 2000 );

        $grid->setModel( $gridModel);
        $data = $gridModel->fetchAll();
        //$this->template->data = $data;
        //$grid->setModel([['id' => 1 ]]);
        $grid->addColumnText('id', 'ID');

        $grid->addColumnText('contentTitle', 'Name')
            ->setFilterText()
                ->setWhere(function($value, $source) {
                    $source->where('content.title')
                        ->like('%s', '%' . $value . '%');
            });
        $grid->addColumnText('programGenre', 'Druh programu')
            ->setFilterText();
        $grid->addColumnText('roomTitle', 'Místnost')
            ->setFilterText();
        $grid->addColumnText('author', 'autor')
            ->setSortable()
            ->setFilterText();
        $grid->addColumnDate('startTs', 'Začátek')
            ->setCustomRender(
                function ( $model ) {
                    return $model->timeFrom->format( 'H:i' );
                }
            )
            ->setFilterDate();
        $grid->addColumnText('programDay', 'Den')
            ->setCustomRender(function($model) {
                return $model->timeFrom->format('l');
            })
            ->setFilterText()
                ->setWhere(function($value, $source) {
                    $source->where('content.title')
                        ->like('%s', '%' . $value . '%');
            });*/


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