<?php

namespace HillCMS\SetariaBundle\Controller;

use Symfony\Component\HttpFoundation\Response;

use HillCMS\ManageBundle\Controller\CMSController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use HillCMS\ManageBundle\Entity\CmsPage;
use HillCMS\ManageBundle\Entity\CmsPageThings; 

class JBrowseController extends CMSController
{
	private $pid = 3;
	
    public function indexAction()
    {
    	return $this->render('HillCMSSetariaBundle:Default:jbrowse.html.twig', array());
    	
    }
}
