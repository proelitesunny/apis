<?php

namespace App\MyHealthcare\Helpers;

class GenerateCode
{
    /**
     * @var int
     */
    private $maxRange;

    /**
     * @var int
     */
    private $alphaAscii;

    /**
     * @var int
     */
    private $sequenceNumber;

    /**
     * @var string
     */
    private $alpha;

    /**
     * @var
     */
    private $prefix;

    /**
     * @var null
     */
    private $lastObject;

    /**
     * @var null
     */
    private $lastCode;

    /**
     * @var
     */
    private $model;

    /**
     * @var
     */
    private $modelColumn;

    /**
     * @var
     */
    private $searchTerm;

    /**
     * GenerateCode constructor.
     * @param $model
     * @param $modelColumn
     * @param $prefix
     * @param null $lastObject
     * @param int $maxRange
     * @param int $alphaAscii
     * @param int $sequenceNumber
     * @param string $alpha
     * @param null $lastCode
     */
    public function __construct(
        $lastObject = null,
        $maxRange = 99999,
        $alphaAscii = 65,
        $sequenceNumber = 1,
        $alpha = 'A',
        $lastCode = null
    ) {
        $this->maxRange = $maxRange;
        $this->alphaAscii = $alphaAscii;
        $this->sequenceNumber = $sequenceNumber;
        $this->alpha = $alpha;
        $this->lastCode = $lastCode;
        $this->lastObject = $lastObject;
    }

    /**
     * @param $model
     */
    private function setModel($model)
    {
        $this->model = $model;
    }

    /**
     * @param $modelColumn
     */
    private function setModelColumn($modelColumn)
    {
        $this->modelColumn = $modelColumn;
    }

    /**
     * @param $alpha
     */
    private function setAlpha($alpha)
    {
        $this->alpha = $alpha;
    }


    private function setAlphaAscii()
    {
        $this->alphaAscii = ord($this->alpha);
    }

    private function setSearchTerm()
    {
        $this->searchTerm = $this->prefix.$this->alpha;
    }


    private function setLastObject()
    {
        
        $this->lastObject = $this->model->withTrashed()
            ->whereNotNull($this->modelColumn)
            ->where($this->modelColumn, 'Not Like', '$'.$this->searchTerm.'%')
            ->orderBy('id', 'DESC')
            ->first();

    }

    /**
     * @param $prefix
     */
    private function setPrefix($prefix)
    {
        $this->prefix = $prefix;
    }

    private function setSequenceNumber()
    {
        $sequenceNumber = (int) substr($this->lastCode, strlen($this->prefix) + 1);

        if ($sequenceNumber < $this->maxRange) {
            $sequenceNumber++;
        } else {
            $sequenceNumber = 1;
            $this->alphaAscii = $this->alphaAscii + 1;
        }

        $this->sequenceNumber = $sequenceNumber;
    }

    private function setLastCode()
    {
        $column = $this->modelColumn;
        if ($this->lastObject == null) {
            $this->setAlpha('A');
            $this->setAlphaAscii();
        } else {
            $this->lastCode = $this->lastObject->$column;
        }
    }

    public function generateCode($model, $modelColumn, $prefix)
    {
        $this->setModel($model);
        $this->setModelColumn($modelColumn);
        $this->setPrefix($prefix);
        $this->setAlpha('Z');
        $this->setAlphaAscii();
        $this->setSearchTerm();
        $this->setLastObject();
        $this->setLastCode();

        if ($this->lastCode != null) {
            $lastPrefix = $this->prefix;
            $this->setPrefix(substr($this->lastCode, 0, strlen($lastPrefix)));
            $this->setAlpha(substr($this->lastCode, strlen($lastPrefix), 1));
            $this->setAlphaAscii();
            $this->setSequenceNumber();
        }

        return sprintf(
            '%s%s%s',
            $this->prefix,
            chr($this->alphaAscii),
            str_pad($this->sequenceNumber, strlen($this->prefix)-1, 0, STR_PAD_LEFT)
        );
    }
}
