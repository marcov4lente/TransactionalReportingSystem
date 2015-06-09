<?php

namespace GenericCorp\TransactionalReportingSystem;

/**
 * Class ASCITable
 * @package GenericCorp\TransactionalReportingSystem
 */
class ASCITable
{

    private $tableWidth = null;
    private $columnCount = null;
    private $columnWidths = array();
    private $cellPadding = 1;

    /**
    * Analyse the table data and measure the table's and column's
    * required widths.
    *
    * @param Array $dataArray
    * @return null
    */
    function __construct($dataArray)
    {
        $this->calculateColumnWidths($dataArray);
        $this->calculateColumnCount($dataArray);
        $this->calculateTableWidth();


    }

    /**
    * Output cell data, formatted to the correct width.
    *
    * @param String $cellData, Int $column, Bool $header
    * @return String
    */
    public function outputCell($cellData,$column,$header = false)
    {

        $cellData = str_pad($cellData, $this->columnWidths[$column], ' ', STR_PAD_RIGHT);

        $paddedCellWidth = $this->columnWidths[$column] + (2*$this->cellPadding);
        $cellData = str_pad($cellData, $paddedCellWidth, ' ', STR_PAD_BOTH);

        if($column == 0) {
            $cellData = '|'.$cellData;
        }

        $cellData = $cellData.'|';


        return $cellData;
    }

    /**
    * Output a horizontal border.
    *
    * @param null
    * @return String
    */
    public function outputHr()
    {
        $horizontalLine = str_pad('', $this->tableWidth, '-', STR_PAD_RIGHT);
        return $horizontalLine;
    }

    /**
    * Calculate each column's max width.
    *
    * @param Array $dataArray
    * @return null
    */
    private function calculateColumnWidths($dataArray)
    {
        $columnWidths = array();

        foreach($dataArray as $row) {
            $column = 0;

            foreach($row as $cell) {

                if(!isset($columnWidths[$column])) {
                    $columnWidths[$column] = strlen($cell);
                } else if (strlen($cell) > $columnWidths[$column]) {
                    $columnWidths[$column] = strlen($cell);
                }

                $column++;
            }
        }

        $this->columnWidths = $columnWidths;
    }

    /**
    * Calculate how many columns the table will have.
    *
    * @param Array $dataArray
    * @return null
    */
    private function calculateColumnCount($dataArray)
    {
        foreach($dataArray as $row) {
            $this->columnCount = count($row);
            break;
        }

    }

    /**
    * Calculate the width of the entire table.
    *
    * @param null
    * @return null
    */
    private function calculateTableWidth()
    {
        $absoluteWidth = array_sum($this->columnWidths);
        $pipesWidth = $this->columnCount + 1;
        $paddingWidth = 2 * $this->cellPadding * $this->columnCount ;

        $this->tableWidth = $paddingWidth + $absoluteWidth + $pipesWidth;
    }

}
