<?php

namespace HillCMS\SetariaBundle\Controller;

use Symfony\Component\HttpFoundation\Response;

use HillCMS\ManageBundle\Controller\CMSController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use HillCMS\ManageBundle\Entity\CmsPage;
use HillCMS\ManageBundle\Entity\CmsPageThings; 

class NewBlastController extends CMSController
{
	private $pid = 4;
	
    public function indexAction()
    {
    	return $this->render('HillCMSSetariaBundle:Default:new_blast.html.twig', array());
    	
    }
}
