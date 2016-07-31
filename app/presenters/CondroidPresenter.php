<?php
/**
 * @author Jakub Stribrny <stribrny@1webit.cz>
 */

namespace Natsu\Presenters;


use Custom\Condroid\CondroidExport;
use Custom\Condroid\NatsuConnector;

class CondroidPresenter extends BasePresenter
{
    /**
     * @inject
     * @var \Natsu\Model\EntityModel
     */
    public $entityModel;

    public function actionExport()
    {
        $sectionId = $this->context->getParameters()['sectionId'];
        $connector = new NatsuConnector($this->entityModel->reflection('Program'), $sectionId);
        $exporter = new CondroidExport($connector);

        $xml = $exporter->run($countPrograms);

        header( 'Content-type: text/xml' );
        header( 'X-Programme-Count: ' . $countPrograms );
        echo $xml;
        exit;
    }
}