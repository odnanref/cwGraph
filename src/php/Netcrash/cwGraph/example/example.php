<?php
/**
 * Basic example for pChart usage with cwGraph
 * 
 * 
 */

$Data = array();
if (count($Data) > 0 ){
	$cwgraph = new cwGraph();
	$cwgraph->setHeight(180)->setWidth(400);
	$cwgraph->setData($Data)->setTitle("Comandos emitidos por dia entre $datainicio e $datafim");
	$cwgraph->setX("Data criado", "datacriado")->setAnguloTextoX(40);
	$cwgraph->addVariavel("Data criado", "datacriado");
	$cwgraph->addVariavel("Total", "total");

	$cwgraph->drawByEngine();
	print '<img src="../../../data/'.$cwgraph->getFilename().'" /><br>';
}


if (count($DataG) > 0 ){
	$cwgraph = new cwGraph("bar");
	$cwgraph->setHeight(180)->setWidth(400);
	$cwgraph->setData($DataG);//->setTitle("Total de comandos por estado");
	$cwgraph->setX("Data criado", "datacriado")->setAnguloTextoX(40);
	$cwgraph->addVariavel("Data criado", "datacriado");
	$cwgraph->addVariavel("Total", "total");
	$cwgraph->drawByEngine();
	print '<img src="'.$cwgraph->getFilename().'" /><br>';
}

$Data = array();
$total = 0;

if (count($DataA) > 0 ){
	$cwgraph = new cwGraph("pie");
	$cwgraph->setData($DataA)->setHeight(150)->setWidth(200);
	$cwgraph->setX("Estados", "estado");
	$cwgraph->setPieAngle()->setPieGapRadius();
	$cwgraph->setDisplayValues(true);

	$cwgraph->addVariavel("total", "total");
	$cwgraph->addVariavel("Estados", "estado");

	$cwgraph->drawByEngine();
	print '<img src="'.$cwgraph->getFilename().'" /><br>';
}
