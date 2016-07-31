<?php
/**
 * @author Jakub Stribrny <stribrny@1webit.cz>
 */

namespace Custom\Condroid;


class CondroidExport
{
    private $db;

    /**
     * @var ICondroidExportable $dataSource
     */
    private $dataSource;

    public function __construct(ICondroidExportable $dataSource) {
        $this->dataSource = $dataSource;
    }

    public function run(&$count)
    {
        $programme = $this->dataSource->fetchForCondroid();
        $count = count($programme);

        $xml = $this->createXml($programme);

        return $xml;
    }

    /**
     * @param ProgrammeCondroid[] $programme
     */
    private function createXml(array $programme)
    {
        $annotations = new \SimpleXMLElement('<annotations></annotations>');

        foreach($programme as $item) {
            $node = $annotations->addChild('programme');

            $node->addChild( 'pid', $item->pid );
            $node->addChild( 'author', '<![CDATA[' . $item->author . ']]>' );
            $node->addChild( 'title', '<![CDATA[' . $item->title . ']]>' );
            $node->addChild( 'type', $item->type );
            $node->addChild( 'program-line', '<![CDATA[' . $item->programLine . ']]>' );
            $node->addChild( 'location', $item->location );
            $node->addChild( 'start-time', $item->startTime->format( DATE_ISO8601 ) );
            $node->addChild( 'end-time', $item->endTime->format( DATE_ISO8601 ) );
            $node->addChild( 'annotation', '<![CDATA[' . $item->annotation . ']]>' );
        }

        return $annotations->asXml();
    }
}