<?php

/* * ********************************************************************** */
/*                   My Implemented PDF Class                            */
/* * ********************************************************************** */

define('FPDF_FONTPATH', 'font/');

require_once __DIR__ . '/fpdf_protection.php';

class PDF extends FPDF_Protection {

  public $Query;
  public $maxln;
  public $rh;
  public $colw;
  public $cols;
  public $fh = 3.5;
  private $PrintHeader = true;

  function PDF() {
    $this->PDF_MemImage("P", "mm", "A4");
    $this->SetAuthor('NIC Paschim Medinipur');
    $this->SetCreator('NIC Paschim Medinipur');
    $this->SetCompression(TRUE);
    $this->AliasNbPages();
    $this->SetMargins(10, 10, 10);
    $this->SetAutoPageBreak(true, 5);
  }

  function AutoHeader($Print = true) {
    $this->PrintHeader = $Print;
  }

  function Header() {
    if ($this->PrintHeader) {
      //Arial bold 15
      //$this->SetFont('Arial','',10);
      //Calculate width of title and position
      //$w=$this->GetStringWidth($title)+6;
      //$this->SetX((210-$w)/2);
      //$this->SetTextColor(0);
      //Title
      //$this->Wrap(0,$this->title,0);
      //$this->Cell(0,7,$this->title,0,0,'C');
      //$this->Wrap(0,$title,0);
      //Line break
      $this->PreHeader();
      $i = 0;
      $this->SetFont('Arial', 'B', 8);
      $this->SetLineWidth(0.3);
      $this->SetColW($this->cols[1]);
      $row = $this->SplitLn($this->cols[0], 0);
      while ($i < count($row)) {
        $this->Wrap($this->colw[$i], $row[$i]);
        $i++;
      }
      $this->Ln();
    }
  }

  function Footer() {
    //Position at 1.5 cm from bottom
    $this->PreFooter();
    //Text color in gray
    $this->SetTextColor(128);
    $this->SetFont('Arial', '', 5);
    $this->Cell(0, 0, date("d/m/Y g:i:s A", time() + (15 * 60)), 0, 1, 'L');
    $this->Cell(0, 0, "Designed and Developed By National Informatics Centre, Paschim Medinipur", 0, 1, 'C');
    //Arial italic 8
    $this->SetFont('Arial', 'I', 6);
    //Page number
    $this->Cell(0, 0, 'Page: ' . $this->PageNo() . ' of {nb}', 0, 1, 'R');
  }

  private function SplitLn($s, $SlNo = 1) {
    $c = 0;
    $this->maxln = 0;
    $ns = array();
    while ($c < count($s)) {
      $ns[$c] = $this->SplitStr($s[$c], $this->colw[$c + $SlNo]);
      $c++;
    }
    return $ns;
  }

  function SplitStr($s, $w) {
    $ns = "";
    $ln = "";
    $i = 0;
    $wd = preg_split('# #', $s);
    while ($i < count($wd)) {
      if ((($this->GetStringWidth($ln) + $this->GetStringWidth($wd[$i])) >= $w) && ($ln != "") && (substr_count($s, '|') == 0)) {
        $ns = $ns . trim($ln) . '|';
        $ln = "";
      } else {
        $ln = $ln . ' ' . $wd[$i];
        $i++;
      }
    }
    $ns = $ns . trim($ln);
    $this->maxln = max(substr_count($ns, '|'), $this->maxln);
    return $ns;
  }

  function SetColW($cols) {
    $i = 0;
    while ($i < count($cols)) {
      $this->colw[$i] = $cols[$i];
      $i++;
    }
  }

  function Wrap($w, $s, $b = 1, $align = 'C') {
    //$s=(substr_count($s,'|')>0)?$s:(($this->GetStringWidth($s)>$w)?$this->SplitLn($s,$w):$s);
    $nb = strlen($s);
    $p = $this->page;
    $h = ($this->maxln * $this->fh) + 6;
    if (($this->GetStringWidth($s) + 2) > $w) {
      $ox = $this->GetX();
      $oy = $this->GetY();

      $x = $this->GetX();
      $y = $this->GetY();
      $r = ((substr_count($s, '|') > 0 ? substr_count($s, '|') + 1 : 1));
      $oy = $oy + (($h - ($r * $this->fh)) / 2);

      do {
        $j = strpos($s, '|');
        $j = empty($j) ? strlen($s) : $j;
        $this->SetXY($ox, $oy);
        $this->SetDrawColor(255, 0, 0);
        if (($j > 0) || (strlen($s) > 0))
          $this->Cell($w, $this->fh, str_replace("|", ", ", substr($s, 0, $j)), 0, 0, $align);
        $oy+=$this->fh;
        $i = $j;
        if (strlen(substr($s, 0, $j)) > 0)
          $s = substr($s, $j + 1, $nb - $j);
        $nb = strlen($s);
      }while ($j);
      $this->SetXY($x, $y);
      $this->SetDrawColor(0);
      $this->SetTextColor(255, 255, 255);
      $this->Cell($w, $h, '', $b, 0, $align);
      $this->SetTextColor(0);
    }
    else
      $this->Cell($w, $h, str_replace("|", ", ", $s), $b, 0, $align);
  }

  function PreHeader() {
    $this->SetAutoPageBreak(true, 20);
    $this->SetFont('Arial', 'B', 12);
    $this->SetTextColor(0);
    $this->SetFont('Arial', 'B', 8);
    $this->Cell(0, 5, $_SESSION['AppID'], 0, 1, "C");
  }

  function PreFooter() {
    $this->SetFont('Arial', '', 8);
    $this->SetTextColor(0);
    //$this->Ln(8);
    //$this->SetX(125);
    //$this->Cell(0,4,"Signature with Seal",0,2,"C");
    //$this->Cell(0,4,"SRER 2013 Form6",0,1,"C");
    $this->SetY(-4);
  }

}

/* * *********************************** End of PDF Class ************************************** */
?>
