<?php

namespace HillCMS\SetariaBundle\Controller;

use Symfony\Component\HttpFoundation\Response;

use HillCMS\ManageBundle\Controller\CMSController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use HillCMS\ManageBundle\Entity\CmsPage;
use HillCMS\ManageBundle\Entity\CmsPageThings; 

class BlastController extends CMSController
{
	private $pid = 3;
	
    public function indexAction()
    {
    	$em = $this->getDoctrine()->getManager();
    	$repo = $em->getRepository("HillCMSManageBundle:CmsPageThings");
    	$pagethings = $repo->findBy(array("pageid" => $this->pid)); //our people page id
    	if (sizeof($pagethings) === 0){
    			//empty page
    		return new Response("Error", 404);
    	}
    	$newsgroups = $this->buildPageGroups($pagethings);
    	
    	return $this->render('HillCMSSetariaBundle:Default:blast.html.twig', array( "contacts" => $newsgroups["Contact"], 
    																				  "resources" => $newsgroups["Resources"]));
    	
    }
    /**
     * The purpose of this action is to listen for the post data from the user form. The post data then gets converted to some protocol format
     * and is submitted to the blast daemon.
     */
    public function blastListenerAction()
    {
    	
    }
}
