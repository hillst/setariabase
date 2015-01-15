<?php

namespace HillCMS\SetariaBundle\Controller;

use Symfony\Component\HttpFoundation\Response;

use HillCMS\ManageBundle\Controller\CMSController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use HillCMS\ManageBundle\Entity\CmsPage;
use HillCMS\ManageBundle\Entity\CmsPageThings; 
use HillCMS\SetariaBundle\ClientSocket\DaemonHunter;

class BlastController extends CMSController
{
	private $pid = 3;
	
    public function indexAction()
    {
    	
    	return $this->render('HillCMSSetariaBundle:Default:blast.html.twig', array()); 
    																				 
    	
    }
    /**
     * The purpose of this action is to listen for the post data from the user form. The post data then gets converted to some protocol format
     * and is submitted to the blast daemon.
     */
    public function blastListenerAction()
    {
        //validate post parameters
        $request = $this->getRequest();
        if ($request->getMethod() === 'GET') {
            $db = "databases/" .$request->get('database');
            $query = $request->get('query');
            if ($query == "" ){
                $query = $request->get('fasta-query');
            }
            $name = $request->get('name');
            //$file = $request->files->all('queryFile');
            //others for advanced options later
            $algorithm = $request->get('algorithm');
            $evalue = $request->get('evalue');
            $disallowGaps = $request->get('disallowgaps');
            $filter = $request->get("filter");
            $wordSize = $request->get("wordsize");
        } else{
            return new Response("Error: Invalid request type.", 400);
        }
        if($db == "" || $query == "" || $algorithm == ""){
            return new Response("Error: Invalid parameters.", 400);
        }
        //build the name of the db
        //protein databases end in .protein
        $type = substr($algorithm, -1);
        $translated = substr($algorithm, 0, 1);
        if (($type == "p" || $type =="x") and $translated == "b"){
            $db .= ".protein";
        }
        //write query file for both options
        $tmpname = uniqid("query");
        $qPath = "/var/www/daemons/query_temps/$tmpname";
        $fd = fopen( $qPath, "w");
        //if name exists
        if ($name !== ""){
            //name option supports a single query, file probably also only supports one
            fwrite($fd, ">" . $name . "\n");
        }
        fwrite($fd, $query);
        fclose($fd);
        //build arguments
        $arguments = array();
        $arguments[0] = "-db";
        $arguments[1] = $db;
        $arguments[2] = "-query";
        $arguments[3] = $qPath;
        $arguments[4] = "-html"; //may help
        //advanced
        if ($evalue !== ""){
            array_push($arguments, "-evalue");
            array_push($arguments, $evalue);
        }
        if ( $wordSize !== ""){
            array_push($arguments, "-word_size");
            array_push($arguments, $wordSize);
        }
        if ($disallowGaps === "on"){
            array_push($arguments, "-ungapped");
        }
        //this is default... consider changing
        if ($filter === "on"){
            array_push($arguments, "-soft_masking");
            array_push($arguments, "true");
        }
        //open socket
        $daemonSocket = new DaemonHunter();
        
        $json = $daemonSocket->jsonBuilder($algorithm, $algorithm, $arguments);
        $result = $daemonSocket->socketSend($json);
        //prepare result
        if(strlen($result) < 1){
            return new Response("Error, unexpected response.", 500);
        }
        $fd = fopen("/var/www/daemons/results/$tmpname", "w");
        fwrite($fd, $result);
        fclose($fd);
        return new Response("$tmpname", 200);	

    }
    public function blastResultsAction($token){
        $fd = fopen("/var/www/daemons/results/$token","r");
        $results = "";
        while( ! feof($fd )){
            $results .= fread($fd, 8092);
        }
        fclose($fd);
        return $this->render("HillCMSSetariaBundle:Default:blastResults.html.twig", array( "results" => $results));
    }         
}
