<?php

namespace Dephpug\Exporter;

class Exporter
{
    private $xml;

    public function setXml($xml)
    {
        $this->xml = @simplexml_load_string($xml);
    }

    public function printByXml()
    {
        if (!$this->isContentToPrint()) {
            return null;
        }

        $klass = $this->getClassExporter();
        if (null === $klass) {
            return null;
        }

        $klass = new $klass();

        return $this->printByClass($klass);
    }

    public function printByClass(iExporter $klass)
    {
        $content = $klass->getExportedVar($this->xml);

        return " => ({$klass::getType()}) {$content}\n\n";
    }

    public function isContentToPrint()
    {
        $command = (string) $this->xml['command'];

        return 'eval' === $command || 'property_get' === $command;
    }

    public function getClassExporter()
    {
        // Getting value
        $typeVar = (string) $this->xml->property['type'];

        switch ($typeVar) {
            case 'int': return Type\IntegerExporter::class;
            case 'float': return Type\FloatExporter::class;
            case 'null': return Type\NullExporter::class;
            case 'bool': return Type\BoolExporter::class;
            case 'string': return Type\StringExporter::class;
            case 'array': return Type\ArrayExporter::class;
            case 'object': return Type\ObjectExporter::class;
            case 'resource': return Type\ResourceExporter::class;
            default: return Type\UnknownExporter::class;
        }
    }
}
