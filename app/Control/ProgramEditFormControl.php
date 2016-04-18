<?php
/**
 * @author Jakub Stribrny <stribrny@1webit.cz>
 */

namespace Natsu\Control;


use Custom\Content\ImageBase64Upload;
use Custom\Content\MainimageAttachment;
use Custom\Content\ImageAttachment;
use Natsu\Forms\BaseForm;
use Natsu\Model\EntityModel;
use Natsu\Model\Exception;
use Natsu\Model\ProgramModel;
use Nette\Application\UI\Form;
use Nette\NotImplementedException;
use Nette\Utils\ArrayHash;
use Nette\Utils\Callback;

class ProgramEditFormControl extends BaseControl
{
    const IMAGE_UPLOAD_KEY = 'b64upload';

    protected $programId;

    /** EntityModel */
    protected $em;

    /** ProgramModel */
    protected $programModel;

    /**
     * @var array | null
     */
    protected $formDefaults;

    protected $attachments;

    protected $sectionId;

    protected $timeFrom;

    protected $timeTo;

    public function __construct($sectionId)
    {
        $this->sectionId = $sectionId;
    }

    /**
     * @param mixed $timeFrom
     *
     * @return $this
     */
    public function setDefaultTimeFrom( $timeFrom )
    {
        $this->timeFrom = $timeFrom;

        return $this;
    }

    /**
     * @param mixed $timeTo
     *
     * @return $this
     */
    public function setDefaultTimeTo( $timeTo )
    {
        $this->timeTo = $timeTo;

        return $this;
    }



    public function setEm(ProgramModel $programModel, EntityModel $em)
    {
        $this->em = $em;
        $this->programModel = $programModel;
    }

    public function render()
    {
        if($this->programId) {
            $formDefaults = $this->getFormDefaults();
            $attachments = $formDefaults[ 'attachments' ];
            $attachments = $this->prepareAttachments($attachments);
        }

        $this->template->attachments = empty($attachments) ? [] : $attachments;

        list($mainThumbWidth, $mainThumbHeight) = explode('x', MainimageAttachment::THUMB_SMALL);
        $this->template->headImageThumbWidth = $mainThumbWidth;
        $this->template->headImageThumbHeight = $mainThumbHeight;

        list($normalThumbWidth, $normalThumbHeight) = explode('x', ImageAttachment::THUMB_SMALL);
        $this->template->normalThumbWidth = $normalThumbWidth;
        $this->template->normalThumbHeight = $normalThumbHeight;

        $this->template->normalFullSize = ImageAttachment::FULL;
        $this->template->headImageFullSize = MainimageAttachment::FULL;

        $this->template->defaultTimeFrom = $this->timeFrom;
        $this->template->defaultTimeTo = $this->timeTo;

        $this->template->setFile(__DIR__ . '/templates/ProgramEditFormControl.latte');
        $this->template->render();
    }

    public function createComponentForm()
    {
        $form = new BaseForm();
        $form->getElementPrototype()->addAttributes([
                                                    'class' => 'program-edit-form',
                                                    'enctype' => 'multipart/form-data',
                                                    'id' => null]);

        $form->addText( 'contentTitle', 'Název' )
            ->setRequired();
        $form->addSelect( 'typeId', 'Druh programu', $this->programModel->getTypesPairs() );
        $form->addSelect( 'roomId', 'Místnost', $this->programModel->getRoomsPairs( '1,2,3,4,5' ) );
        //$form->addSelect('genre', 'Sekce');
        $form->addText( 'author', 'Autor' );
        $form->addTextArea( 'contentText', 'Anotace' );//anotace

        $timeFromInput = $form->addText( 'timeFrom', 'Začátek' );
        $timeFromInput->getControlPrototype()->addAttributes(['class' => 'js-period-start', 'data' => ['timeFrom' => $this->timeFrom->format('Y-m-d H:i:s') ]]);
        $timeFromInput->setDefaultValue( $this->timeFrom->format( 'Y-m-d H:i:s' ));

        $timeToInput = $form->addText('timeTo', 'Konec');
        $timeToInput->getControlPrototype()->addAttributes( [ 'class' => 'js-period-end', 'data' => [ 'timeTo' => $this->timeTo->format( 'Y-m-d H:i:s' ) ] ] );
        $timeToInput->setDefaultValue( $this->timeTo->format( 'Y-m-d H:i:s' ));


        $form->addSubmit('submit', 'Odeslat')
            ->getControlPrototype()->addAttributes(['class' => 'btn']);

        $form->addHidden('id', NULL);

        $form->getElementPrototype()->class = $form->getElementPrototype()->class . ' js-program-edit-form';

        if ( $this->programId ) {
            $formDefaults = $this->getFormDefaults();
            $form->setDefaults( $formDefaults );
            $form->getComponent('id')->setValue($this->programId);
        }

        $form->onValidate[] = callback($this, 'formValidate');
        $form->onSuccess[] = callback($this, 'formSuccessful');

        return $form;
    }

    protected function prepareAttachments($attachments)
    {
        $prepared = [
            'IMAGE' => [],
            'HEADIMAGE' => [],
            'VIDEO' => []
        ];

        foreach ( $attachments as $key => $attachment ) {
            if ( $attachment['mime'] == "IMAGE" ) {

                $image = new \Custom\Content\ImageAttachment();
                $image->setRow( ArrayHash::from($attachment));
                $image->run();

                $attachment['_data'] = $image;
            }
            if ( $attachment[ 'mime' ] == "HEADIMAGE" ) {
                $image = new \Custom\Content\MainimageAttachment();
                $image->setRow( ArrayHash::from( $attachment ) );
                $image->run();

                $attachment[ '_data' ] = $image;
            }
            $mime = $attachment['mime'];
            $prepared[$attachment[ 'mime' ]][] = $attachment;

        }

        return ArrayHash::from($prepared);
    }

    public function formValidate( Form $form, $values = NULL)
    {

    }

    public function formSuccessful(Form $form, $values =  NULL)
    {
        if(!empty($values['id'])) {
            $this->programId = $values['id'];
            $success = $this->update( $values );
        }
        else
            $success = $this->create($values);

        $response = ['success' => $success];
        echo json_encode($response);
        exit;
    }

    protected function processAttachments($contentId)
    {
        $imageUploads = [ ];
        foreach ( $_POST as $key => $val ) {
            if ( strstr( $key, 'b64upload' ) ) {
                $imageUploads[ $key ] = $val;
            }
        }

        $attachmentActions = isset($_POST['attachmentActions']) ? $_POST[ 'attachmentActions' ] : [];

        $modelAttachment = clone $this->em;
        $modelAttachment->table = 'attachment';

        foreach($attachmentActions as $actionName => $actions) {
            if($actionName == 'add' )
                $this->resolveAddAttachments($modelAttachment, $actions, $contentId);

            if($actionName =='edit')
                $this->resolveEditAttachments( $modelAttachment,$actions, $contentId);

            if($actionName == 'remove')
                $this->resolveRemoveAttachments( $modelAttachment,$actions, $contentId);
        }


        /*

        foreach ( $imageUploads as $uploadName => $image ) {
            $filename = md5( time() ). '.jpg';
            $row = [
                'contentId' => $contentId,
                'file' => ImageBase64Upload::saveTmp( $image, $filename )
            ];

            $metaUploadName = $uploadName . '_meta';
            if(!isset($_POST[$metaUploadName]) || !is_array($_POST[$metaUploadName]))
                throw new \Exception('Upload is missing meta inputs');
            $meta = $_POST[$metaUploadName];
            $mime = $meta['mime'];


            if($mime == 'IMAGE')
                $attachment = new ImageAttachment();
            elseif($mime == 'HEADIMAGE')
                $attachment = new MainimageAttachment();
            else
                throw new \Exception('Unknown mime: ' . $mime);

            $attachment->setRow( ArrayHash::from( $row ) );
            $url = $attachment->create();


            $attachmentData = [
                'contentId' => $contentId,
                'mime' => $mime,
                'title' => $filename,
                'url' => $url
            ];
            $modelAttachment->insert($attachmentData);

        }*/
    }

    protected function resolveAddAttachments(EntityModel $modelAttachment, array $actions, $contentId) {
        foreach($actions as $uploadName) {
            $image = $_POST[$uploadName];

            $filename = $_POST[$uploadName . '_meta']['filename'];
            $row = [
                'contentId' => $contentId,
                'file'      => ImageBase64Upload::saveTmp( $image, $filename )
            ];

            $metaUploadName = $uploadName . '_meta';
            if ( !isset( $_POST[ $metaUploadName ] ) || !is_array( $_POST[ $metaUploadName ] ) )
                throw new \Exception( 'Upload is missing meta inputs' );
            $meta = $_POST[ $metaUploadName ];
            $mime = $meta[ 'mime' ];


            if ( $mime == 'IMAGE' )
                $attachment = new ImageAttachment();
            elseif ( $mime == 'HEADIMAGE' )
                $attachment = new MainimageAttachment();
            else
                throw new \Exception( 'Unknown mime: ' . $mime );

            $attachment->setRow( ArrayHash::from( $row ) );
            $url = $attachment->create();


            $attachmentData = [
                'contentId' => $contentId,
                'mime'      => $mime,
                'title'     => $filename,
                'url'       => $url
            ];
            $modelAttachment->insert( $attachmentData );
        }
    }

    protected function resolveRemoveAttachments( EntityModel $modelAttachment, array $actions, $contentId )
    {
        $modelAttachment = clone $this->em;
        $modelAttachment->setTable('attachment');

        foreach($actions as $id)
            $modelAttachment->deleteId((int)$id);
    }

    protected function resolveEditAttachment( EntityModel $modelAttachment, array $actions, $contentId ) {
        throw new NotImplementedException('Not implemented. Taaaa.');
    }

    protected function getFormDefaults()
    {
        if ( $this->programId && !$this->formDefaults ) {
            $this->formDefaults = $this->programModel->getFormDefaults( $this->programId );
        }

        return $this->formDefaults;
    }

    protected function update($values)
    {
        // start transaction
        $this->em->begin();

        try {

            $programValues = [
                'id'       => $values[ 'id' ],
                'typeId'   => $values[ 'typeId' ],
                //'genreId'  => $values[ 'genreId' ],
                'roomId'   => $values[ 'roomId' ],
                'timeFrom' => $values[ 'timeFrom' ],
                'timeTo'   => $values[ 'timeTo' ]
            ];

            $this->programModel->update( ArrayHash::from( $programValues ) );

            $contentId = $this->getFormDefaults()[ 'contentId' ];
            $contentModel = $this->em->reflection( 'Content' );
            $contentValues = [
                'id'   => (int)$contentId,
                'text' => $values[ 'contentText' ],
                'pageTitle' => $values['contentTitle'],
                'title' => $values[ 'contentTitle' ],
                'author' => $values['author']
            ];
            $contentModel->update( ArrayHash::from( $contentValues ) );

        } catch (\Exception $e) {
            $this->em->rollback();

            return FALSE;
        }
        $this->em->commit();

        $this->processAttachments( $contentId );


        return TRUE;
    }

    protected function create($values)
    {
        $this->em->begin();

        try {
            $contentModel = $this->em->reflection( 'Content' );
            $contentValues = [
                'text'      => $values[ 'contentText' ],
                'pageTitle' => $values[ 'contentTitle' ],
                'title'     => $values[ 'contentTitle' ],
                'author'    => $values[ 'author' ]
            ];
            $contentId = $contentModel->insertContent( ArrayHash::from( $contentValues ) );


            $programValues = [
                'typeId'    => $values[ 'typeId' ],
                //'genreId'  => $values[ 'genreId' ],
                'roomId'    => $values[ 'roomId' ],
                'timeFrom'  => $values[ 'timeFrom' ],
                'timeTo'    => $values[ 'timeTo' ],
                'statusId' => 14,
                'contentId' => $contentId,
                'sectionId' => $this->sectionId
            ];

            $programId = $this->programModel->insert( $programValues );
        } catch (\Exception $e) {
            $this->em->rollback();

            return FALSE;
        }
        $this->em->commit();

        $this->processAttachments( $contentId );

        return TRUE;
    }

    public function setProgramId($programId)
    {
        $this->programId = $programId;
    }
}