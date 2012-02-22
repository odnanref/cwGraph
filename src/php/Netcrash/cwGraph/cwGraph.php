<?php

/**
 * Copyright (c) 2011 Fernando Ribeiro
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 * * Redistributions of source code must retain the above copyright
 * notice, this list of conditions and the following disclaimer.
 *
 * * Redistributions in binary form must reproduce the above copyright
 * notice, this list of conditions and the following disclaimer in
 * the documentation and/or other materials provided with the
 * distribution.
 *
 * * Neither the names of the copyright holders nor the names of the
 * contributors may be used to endorse or promote products derived
 * from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package Netcrash
 * @subpackage cwGraph
 * @author Fernando Andre <netriver+cwGraph at gmail dit com>
 * @copyright 2011 Fernando Ribeiro http://netcrash.wordpress.com
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @link
 * @version @@PACKAGE_VERSION@@
 */



/**
 * info relativamente aos dados de um tipo de letra pertencente
 * a um grafico
 *
 * Information for the font details
 *
 * @author andref
 *
 */
class cwFont
{
	/**
	 * Font Path
	 *
	 * @var String
	 */
	public $fontpath;
	/**
	 * Name of letter type (Arial, verdana, etc)
	 *
	 * @var String
	 */
	public $name;
	/**
	 * Letter size
	 *
	 * @var int
	 */
	public $size;
	/**
	 * 
	 *
	 * @param string $font
	 * @param int $size
	 */
	public function __construct($font, $size, $fontpath = null)
	{
		$this->name = $font;
		$this->size = $size;

		if ($fontpath !== null ){
			$this->fontpath = $fontpath;
		}
	}
	/**
	 * get the font Path
	 * @return String
	 */
	public function getPath()
	{
		return $this->fontpath.$this->name.".ttf";
	}
}
/**
 * Abstraccao para criacao de graficos
 * Chart abstraction class mainly for pChart
 *
 * @author andref
 *
 */
class cwGraph
{
	/**
	 * Type of object of the graphic currently only pchart
	 *
	 * default: pchart
	 *
	 * @var String
	 */
	private $graphtype;
	/**
   * Location of where you can find the files and letter types.
	 *
	 * @var string
	 */
	private $fontPath;
	/**
	 * Available fonts
   *
	 * @var Array
	 */
	private $fonts = array();
	/**
	 * The name of this graph
	 *
	 * @var string
	 */
	private $name;
	/**
	 *
	 * Type of graphic library were going to use
	 * @var pChart
	 */
	private $chart;
	/**
	 *
	 * Details on X , Y , Z fields
	 * @var Array
	 */
	private $GraphDetails;
	/**
	 * Series to add description in the graph
   *
	 * @var Array
	 */
	private $Series = array();
	/**
	 * Graph Width
   * 
	 * @var int
	 */
	private $width;
	/**
	 * Graph Height
	 *
	 * @var int
	 */
	private $height;
	/**
   * Path to save the graphic to on local disk
	 *
	 * @var string
	 */
	private $outputpath;
	/**
   * Name of the file to use in the outputpath
	 *
	 * @var string
	 */
	private $filename;
	/**
   * By default the image is recorded to disk
   * 
	 * @var boolean
	 *
	 */
	private $render = true;
	/**
	 * No output goes to the browser by default
   *
	 * @var boolean
	 */
	private $display = false;

	private $legendStatus = true;
	/**
	 * DataSet para pChart
	 *
	 * @var pData
	 */
	private $pData;
	/**
	 * Create a new graphic
	 *
	 *
	 * @param string $type line|bar|pie
	 *
	 * @return cwGraph
	 */
	public function __construct($type = 'line')
	{
		$this->type = strtolower($type);

		$this->addFont('advent_light');
		$this->addFont('Bedizen');
		$this->addFont('calibri');
		$this->addFont('Forgotte');
		$this->addFont('GeosansLight');
		$this->addFont('MankSans');
		$this->addFont('pf_arma_five');
		$this->addFont('Silkscreen');
		$this->addFont('verdana');

		$this->setFontPath( "/data/pChart/fonts/");
		$this->outputpath = "/tmp/";
		$this->filename	= date("Ymd_His").".png";

		$this->setFont("verdana", 6);
		$this->setFontFor('Silkscreen', 6, "PictureTitle");
		$this->setTitleFont("Forgotte", 11);
		$this->setLegendFont("pf_arma_five", 6);

		$this->width = 700;
		$this->height = 300;
		// Criar um array para colocar dados adicionais do tipo de grafico
    // Create an array to place aditional data of the graph type
		$this->GraphDetails[$type] = array();
		
    // Define the legend has vertical and specify the initial position
		$this->setLegendVertical(60, 40);
		$this->setLegendSize();

		$this->setDisplayValues(false);

		return $this;
	}
	/**
	 * recalculate all graph positions
	 *
	 * @return cwGraph
	 */
	public function redraw()
	{
		if ($this->legendStatus === true )
		{
			$this->height = $this->height + $this->getLegendHeight();
			$this->width = $this->width + $this->getLegendWidth();
			$this->setLegendSize( $this->getLegendWidth(), $this->getLegendHeight() );
			$this->setLegendPosition(($this->width-$this->getLegendWidth()), 40);
		}
	}
	/**
	 * Get the current path for the fonts
	 *
	 * @return String
	 */
	public function getFontPath()
	{
		return $this->fontPath;
	}
	/**
	 * Specify the graph height
	 *
	 * @param int $height
	 * @return cwGraph
	 */
	public function setHeight($height)
	{
		$this->height = $height;
		return $this;
	}
	/**
	 * Specify the graph width
	 *
	 * @param int $width
	 * @return cwGraph
	 */
	public function setWidth($width)
	{
		$this->width = $width;
		return $this;
	}
	/**
	 * Define on filesystem where the letter types can be found (fonts)
   *
	 * @param string $path
	 * @return cwGraph
	 */
	public function setFontPath($path)
	{
		$this->fontPath = $path;
		return $this;
	}
	/**
	 * Add a letter type to the array
   *
	 * @param string $font
	 * @return cwGraph
	 */
	public function addFont($font)
	{
		$this->fonts[] = $font;
		return $this;
	}
	/**
	 * Define X Label and field name
	 *
	 * @param string $title
	 * @param string $field
	 * @param int	$start
	 *
	 * @return cwGraph
	 */
	public function setX($title, $field = null, $start = 0)
	{
		$this->GraphDetails['x']["title"] = $title;
		$this->GraphDetails['x']["field"] = $field;
		$this->GraphDetails['x']["start"] = 0;
		return $this;
	}
	/**
	 * Define Y Label and field name
	 *
	 * @param string $name
	 * @param string $field
	 * @param int $start
	 *
	 * @return cwGraph
	 */
	public function setY($name, $field = null, $start = 0 )
	{
		$this->GraphDetails['y']['title'] = $name;
		$this->GraphDetails['y']['field'] = $field;
		$this->GraphDetails['y']["start"] = $start;
		return $this;
	}
	/**
	 * Define the data series
	 *
	 * @param string $title label of the field campo Title
	 * @param string $field  field name
	 * @return cwGraph
	 */
	public function addVariavel($title, $field)
	{
		$this->Series[$campo]['title'] = $title;
		$this->Series[$campo]['field'] = $field;
		return $this;
	}
	/**
	 * Pass Data (dataset) to graph
	 *
	 * @param Array $Data
	 * @return cwGraph
	 */
	public function setData($Data)
	{
		$this->Data = $Data;
		return $this;
	}
	/**
	 * Define a different axis for the graph
	 *
	 * @param string $axis
	 * @param string $name
	 * @param string $field
	 *
	 * @return cwGraph
	 */
	public function setAxis($axis, $name, $field = null )
	{
		$this->GraphDetails[$axis][$name] = $field;
		return $this;
	}
	/**
	 * Define graph title
	 *
	 * @param string $title
	 * @return cwGraph
	 */
	public function setTitle($title)
	{
		$this->GraphDetails['title']['value'] = $title;
		return $this;
	}
	/**
	 * Criar o tipo de grafico baseado num tipo de codigo de sistema.
   * Create the graphic based on a type of graphic system
	 *
	 * @param string $graphtype pChart
	 *
	 * @return cwGraph
	 */
	public function drawByEngine($graphtype = 'pchart')
	{
		$graphtype = strtolower($graphtype);
		$this->graphtype = $graphtype;

		$this->redraw();

		switch($graphtype)
		{
			case 'pchart':{
				$this->pChart();
				break;
			}
		}
	}
	/**
	 * Define the letter type (font) for  xxx
	 *
	 * @param string $font
	 * @param int $size
	 * @param string $for
	 *
	 * @return cwGraph
	 */
	public function setFontFor($font, $size, $for = null )
	{
		if ($for !== null ){
			$this->GraphDetails[$for]['font'] = new cwFont($font, $size,  $this->getFontPath() );
		}else{
			$this->GraphDetails['font'] = new cwFont($font, $size,  $this->getFontPath() );
		}

		return $this;
	}
	/**
   * Set the font to be used for the title
	 *
	 * @param String $font tahoma | GeosansLight | MankSans | pf_arma_five | Silkscreen
	 * @param int $size 6 | 8 | 9 | 10
	 * @return cwGraph
	 */
	public function setTitleFont($font = "tahoma", $size = 8)
	{
		$this->GraphDetails['title']['font'] = new cwFont($font, $size,  $this->getFontPath() );
		return $this;
	}
	/**
   * Set the legend font to be used
	 *
	 * @param String $font tahoma | GeosansLight | MankSans | pf_arma_five | Silkscreen
	 * @param int $size 6 | 8 | 9 | 10
	 * @return cwGraph
	 */
	public function setLegendFont($font = "tahoma", $size = 8)
	{
		$this->GraphDetails['legend']['font'] = new cwFont($font, $size, $this->getFontPath() );
		return $this;
	}
	/**
	 * Set the legend position
   *
	 * @param int $x
	 * @param int $y
	 *
	 * @return cwGraph
	 */
	public function setLegendPosition($x, $y)
	{
		$this->GraphDetails['legend']['position']['x'] = $x; // Default x position
		$this->GraphDetails['legend']['position']['y'] = $y; // default y position
		return $this;
	}
	/**
	 * Set the legend size
   * 
	 * @param int $height
	 * @param int $width
	 *
	 * @return cwGraph
	 */
	public function setLegendSize($height=0, $width=0)
	{
		if ($height > 0 )
		{
			$this->GraphDetails['legend']['height'] = $height;
		}else{
			$this->GraphDetails['legend']['height'] = 70;
		}

		if ($width > 0 )
		{
			$this->GraphDetails['legend']['width'] = $width;
		}else{
			$this->GraphDetails['legend']['width'] = 170;
		}

		return $this;
	}
	/**
	 * get The height of the legend
	 *
	 * @return int
	 */
	public function getLegendHeight()
	{
		return (int)$this->GraphDetails['legend']['height'];
	}
	/**
	 * get legend width
	 *
	 * @return int
	 */
	public function getLegendWidth()
	{
		return (int)$this->GraphDetails['legend']['height'];
	}
	/**
	 * enable/disable legend
	 *
	 * @param boolean $status
	 * @return cwGraph
	 */
	public function setLegend($status)
	{
		$this->legendStatus = $status;
		if ($status === false ){
			$this->setLegendSize(0, 0);
		}else{
			$this->setLegendSize();
		}

		return $this;
	}
	/**
   * Set the default font to be used
	 *
	 * @param String $font tahoma | GeosansLight | MankSans | pf_arma_five | Silkscreen
	 * @param int $size 6 | 8 | 9 | 10
	 * @return cwGraph
	 */
	public function setFont($font = "tahoma", $size = 8)
	{
		$this->GraphDetails['font'] = new cwFont($font, $size,  $this->getFontPath() );
		return $this;
	}
	/**
	 * Display inline values in the graph chart
	 *
	 * @param bool $bool
	 * @return cwGraph
	 */
	public function setDisplayValues($bool)
	{
		$this->GraphDetails['inlinetext'] = $bool;
		return $this;
	}
	/**
   * should Display  data information on graph or not
	 *
	 * @return boolean
	 */
	public function getDisplayValues()
	{
		return $this->GraphDetails['inlinetext'];
	}
	/**
	 * Set the graphic type Bar Pie overlay Line
	 *
	 * @param string $type line|bar|overlay|pie
	 * @throws Exception
	 * @return cwGraph
	 */
	private function setType($type)
	{
		if (!is_object($this->chart)){
			throw new Exception("Chart drawing object engine not created.");
		}
		$type = strtolower($type);
		switch($type){
			case 'line':{
				$this->setLine();
				break;
			}

			case 'bar':{
				$this->setBar();
				break;
			}
			case 'pie':{
				$this->setPie();
				break;
			}
		}

	}
	/**
   * Basic information to setup a line chart
   * acording to the type of engine used
   *
	 */
	private function setLine()
	{
		if ($this->chart instanceOf pImage ){

			if ( $this->legendStatus === true ){
				$heightL = $this->getLegendHeight()+5;
				$widthL	= $this->getLegendWidth()+5;
			}else{
				$heightL = 70;
				$widthL = 70;
			}

			/* Draw the scale and the 1st chart */
			$this->chart->setGraphArea(60, 60, ($this->width-$widthL), ($this->height-$heightL) ); // 700-450 , 300-190

			$this->chart->drawFilledRectangle(60, 60, ($this->width-$widthL), ($this->height-$heightL),
			array("R"=>255,"G"=>255,"B"=>255,"Surrounding"=>-200,"Alpha"=>10)
			);
			$this->chart->drawScale(array("Mode" => SCALE_MODE_START0, "DrawSubTicks"=>TRUE, 'LabelRotation' => $this->getAnguloTextoX()));
			$this->chart->setShadow(TRUE,
			array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>10)
			);
			$font = $this->getFontFor();
			$this->chart->setFontProperties(array("FontName"=> $font->getPath(),"FontSize"=> $font->size));

			$this->chart->drawLineChart(
			array(
				"DisplayValues"=> $this->getDisplayValues(),
				"DisplayColor"=> DISPLAY_AUTO
			)
			);

			$this->chart->setShadow(FALSE);
		}
	}
	/**
	 * Create the chart of type Bar 
	 *
	 */
	private function setBar()
	{
		if ( $this->legendStatus === true ){
			$heightL = $this->getLegendHeight()+5;
			$widthL	= $this->getLegendWidth()+5;
		}else{
			$heightL = 70;
			$widthL = 70;
		}
		
		if ($this->chart instanceOf pImage ){
			/* Draw the scale and the 1st chart */
			$this->chart->setGraphArea(60, 60, ($this->width-$widthL), ($this->height-$heightL) ); // 700-450 , 300-190
			$this->chart->drawFilledRectangle(60,60, ($this->width-$widthL), ($this->height-$heightL),array("R"=>255,"G"=>255,"B"=>255,"Surrounding"=>-200,"Alpha"=>10));
			$this->chart->drawScale(array("Mode" => SCALE_MODE_START0 , "DrawSubTicks"=>TRUE, 'LabelRotation' => $this->getAnguloTextoX()));
			$this->chart->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>10));
			$font = $this->getFontFor();
			$this->chart->setFontProperties(array("FontName"=> $font->getPath(),"FontSize"=> $font->size));

			$this->chart->drawBarChart(
			array(
			 	"DisplayPos"	=> LABEL_POS_INSIDE,
			 	"DisplayValues"	=> $this->getDisplayValues(),
			 	"Rounded"		=> false,
			 	"Surrounding"	=> 30
			)
			);
		}

	}
	/**
	 * Criar o grafico do tipo pie
   * Create the chart of type Pie
	 *
	 */
	private function setPie()
	{
		$PieChart = new pPie($this->chart, $this->pData );
//		$PieChart->draw2DPie(140, 125, array("SecondPass"=>FALSE));
		// Com legendas inline
    // with inline legends
//		$PieChart->draw2DPie(340, 125, array("DrawLabels"=>$this->getDisplayValues(),"Border"=>TRUE));
		// Com abertura de angulo
		$PieChart->draw2DPie( 100, 120,
			array("DrawLabels"=>$this->getDisplayValues(),
			"DataGapAngle"=>$this->getPieAngle(),
			"DataGapRadius"=>$this->getPieGapRadius(),
			"Border"=>TRUE,"BorderR"=>255,"BorderG"=>255,"BorderB"=>255
			)
		);
		
		if ($this->legendStatus === true ){
			$PieChart->drawPieLegend($this->getLegendX(), $this->getLegendY(), array("Style"=>LEGEND_NOBORDER,"Mode"=>LEGEND_VERTICAL));
			$this->legendStatus = false;
		}		
	}
	/**
   * Set opening angle for pie chart
	 * 
	 * @param int $angle 10
	 * @return cwGraph
	 */
	public function setPieAngle($angle = 10)
	{
		$this->GraphDetails['pie']['angle'] = (int) $angle;
		return $this;
	}
	/**
   * Get the separating angle of the pie slices
	 * 
	 * @return int
	 */
	public function getPieAngle()
	{
		if (array_key_exists("angle", $this->GraphDetails['pie'])){
			return $this->GraphDetails['pie']['angle'];
		}
		return 0;
	}
	/**
   * Set the gap between pie angles
	 * 
	 * @param int $radius = 6
	 * @return cwGraph
	 */
	public function setPieGapRadius($radius = 6)
	{
		$this->GraphDetails['pie']['radius'] = (int) $radius;
		return $this;
	}
	/**
   * Get the pie angle Gap 
	 * 
	 * @return int
	 */
	public function getPieGapRadius()
	{
		if (array_key_exists("radius", $this->GraphDetails['pie'])){
			return $this->GraphDetails['pie']['radius'];
		}
		
		return 0;
	}
	/**
	 * @deprecated
	 * Criar a legenda e escala do grafico
	 * baseado nos dados de Data consegue criar e definir uma escala e legenda para o grafico
	 *
	 * @param int $Angulo angulo de apresentacao do texto
	 *
	 * @return cwGraph
	 */
	private function criarEscalaELegenda($Angulo = 0)
	{
		return $this;
	}
	/**
	 * get the dataset
	 *
	 * @return Array
	 */
	public function getData()
	{
		return $this->Data;
	}
	/**
   * Get the font to be used on a specific graphic object
	 *
	 * @param string $place|null legend|title|null
	 * @return cwFont
	 */
	public function getFontFor($place = null)
	{
		if ($place === null || !array_key_exists($place, $this->GraphDetails)){
			$font = $this->GraphDetails['font'];
		}else{
			if (array_key_exists("font", $this->GraphDetails[$place])){
				$font = $this->GraphDetails[$place]['font'];
			}else{
				$font = $this->GraphDetails['font'];
			}
		}

		return $font;
	}
	/**
   * Get the angle for text of X 
	 *
	 * @return int
	 */
	public function getAnguloTextoX()
	{
		return (int)$this->GraphDetails['legend']['x']['angulo'];
	}
	/**
	 * Set the angle for text in the X acssis
   *
	 * @param int $angulo
	 * @return cwGraph
	 */
	public function setAnguloTextoX($angulo)
	{
		$this->GraphDetails['legend']['x']['angulo'] = (int) $angulo;
		return $this;
	}
	/**
	 * Set the file name
   *
   * No spaces are allowed
   *
	 * @param string $filename
	 * @return cwGraph
	 */
	public function setFilename($filename)
	{
		$filename = str_replace(" ", "", $filename);
		$this->filename = $filename;
		return $this;
	}
	/**
	 * Output path must be writable by app
	 *
	 * @param string $path
	 * @return cwGraph
	 */
	public function setOutputPath($path)
	{
    if (!is_writable($this->outputpath)) {
        throw new Exception("Output path not writable!");
    }
		$this->outputpath = $path;
		return $this;
	}
	/**
	 * Set if file needs to be written to disk
	 *
	 * @param boolean $bool
	 * @return cwGraph
	 */
	public function setRender($bool)
	{
		$this->render = $bool;
		return $this;
	}
	/**
	 * Set if output should be directed to the browser
	 *
	 * @param boolean $bool
	 * @return cwGraph
	 */
	public function setDisplay($bool)
	{
		$this->display = $bool;
	}
	/**
	 * Set the legend has vertical |
	 *
	 * @param int $x|null
	 * @param int $y|null
	 *
	 * @return cwGraph
	 */
	public function setLegendVertical($x=0, $y=0)
	{
		$this->GraphDetails['legend']['orientation'] = "vertical";

		if ($x > 0 ) {
			$this->GraphDetails['legend']['position']['x'] = $x;
		}

		if ($y > 0 ){
			$this->GraphDetails['legend']['position']['y'] = $y;
		}

		return $this;
	}
	/**
	 * Set the legend has Horizontal
	 *
	 * @param int $x definir posicao em x
	 * @param int $y
	 *
	 * @return cwGraph
	 */
	public function setLegendHorizontal($x=0, $y=0)
	{
		$this->GraphDetails['legend']['orientation'] = "horizontal";

		if ($x > 0 ) {
			$this->GraphDetails['legend']['position']['x'] = $x;
		}

		if ($y > 0 ){
			$this->GraphDetails['legend']['position']['y'] = $y;
		}
		return $this;
	}
	/**
	 * Get the legend position from X
   * 
	 * @return int
	 */
	public function getLegendX()
	{
		return $this->GraphDetails['legend']['position']['x'];
	}
	/**
	 * Get the legend position from Y 
   *
	 * @return int
	 */
	public function getLegendY()
	{
		return $this->GraphDetails['legend']['position']['y'];
	}
	/**
	 * Get legend orientation
	 *
	 * MUST only be called after the type of "engine" to be used has been defined
   * for example pChart
	 *
	 * @return String
	 */
	public function getLegendOrientation()
	{
		if ($this->graphtype == 'pchart'){
			if ($this->GraphDetails['legend']['orientation'] == 'vertical'){
				return LEGEND_VERTICAL; // VERTICAL
			}else{
				return LEGEND_HORIZONTAL; // HORIZONTAL
			}
		}
	}
	/**
	 * Returns the complete path  to the file
   *
	 * @return String
	 */
	public function getFilePath()
	{
		return $this->outputpath.$this->filename;
	}
	/**
	 * Get the file name
	 *
	 * @return String
	 */
	public function getFilename()
	{
		return $this->filename;
	}
	/**
	 * Returns the path to the directory of the images of the rendered graphs
   *
	 * @return cwGraph
	 */
	public function getOutputPath()
	{
		return $this->outputpath;
	}
	/**
	 * Returns the chart title
	 *
	 * @return String|null
	 */
	public function getGraphTitle()
	{
		if (array_key_exists("title", $this->GraphDetails)){
			if (array_key_exists("value", $this->GraphDetails['title'])){
				return $this->GraphDetails['title']['value'];
			}
		}

		return null;
	}
	/**
	 * Return the name of the X field
	 *
	 * @return String|null
	 */
	public function getXField()
	{
		if (array_key_exists("x", $this->GraphDetails)){
			if (array_key_exists("field", $this->GraphDetails['x'])){
				return $this->GraphDetails['x']["field"];
			}
		}

		return null;

	}
	/**
	 * Method to create the chart based on the pChart Lib
	 *
	 */
	private function pChart()
	{
		$DataSet = new pData();
		foreach ($this->Data as $value )
		{
			foreach ($value as $k => $v){
				$DataSet->AddPoints($v, $k);
			}
		}

		if ($this->getXField() !== null ){
			$DataSet->setAbscissa( $this->getXField() );
		}

		if (count($this->Series) > 0 ){
			foreach ($this->Series as $a ){
				$DataSet->setSerieDescription($a['field'], $a['title']);
			}
		}

		$this->pData = $DataSet;

		$this->chart = new pImage($this->width, $this->height, $DataSet);
		// Draw the background
		$Settings = array("R"=>170, "G"=>183, "B"=>87, "Dash"=>1, "DashR"=>190, "DashG"=>203, "DashB"=>107);
		$this->chart->drawFilledRectangle(0, 0, $this->width, $this->height, $Settings);

		// Overlay with a gradient
		$Settings = array("StartR"=>219, "StartG"=>231, "StartB"=>139, "EndR"=>1, "EndG"=>138, "EndB"=>68, "Alpha"=>50);
		$this->chart->drawGradientArea(0, 0, $this->width, $this->height, DIRECTION_VERTICAL, $Settings);

		if ($this->getGraphTitle() !== null ){
			// Barra do titulo
			$Settings2 = array("StartR"=>0,"StartG"=>0,"StartB"=>0,"EndR"=>50,"EndG"=>50,"EndB"=>50,"Alpha"=>80);
			$this->chart->drawGradientArea(0, 0, $this->width, 20, DIRECTION_VERTICAL, $Settings2);
		}


		// Add a border to the picture
		$this->chart->drawRectangle(0, 0, $this->width-1, $this->height-1, array("R"=>0,"G"=>0,"B"=>0));

		if ($this->getGraphTitle() !== null ){
			// picture title
			$font = $this->getFontFor("PictureTitle");

			$this->chart->setFontProperties(array("FontName"=> $font->getPath(),"FontSize"=> $font->size));
			$this->chart->drawText( 10, 13, $this->getGraphTitle() , array("R"=>255,"G"=>255,"B"=>255));
		}

		$font = $this->getFontFor();
		// write chart title
		$this->chart->setFontProperties(array("FontName"=> $font->getPath(),"FontSize"=> 5));
		$this->chart->drawText($this->width-36, $this->height-5, "Zap ".date("Y-m-d") ,array("FontSize"=>6,"Align"=>TEXT_ALIGN_BOTTOMMIDDLE));

		$font = $this->getFontFor();
		// write chart title
		$this->chart->setFontProperties(array("FontName"=> $font->getPath(),"FontSize"=> $font->size));
		// Criar o grafico mesmo.
		$this->setType($this->type);

		if ($this->legendStatus === true ){
			$this->chart->drawLegend($this->getLegendX(), $this->getLegendY(),
				array("Style"=>LEGEND_NOBORDER,"Mode"=>$this->getLegendOrientation())
			);
		}

		if ($this->render === true )
		{
			if ( $this->chart->Render($this->getFilePath()) === false ){
				throw  new Exception("UNABLE TO OUTPUT IMAGE GRAPH to DISK");
			}
		}

		if ($this->display === true )
		{
			$this->chart->Stroke();
		}
	}

}
