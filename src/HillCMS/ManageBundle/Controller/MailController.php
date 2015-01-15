<?php

namespace HillCMS\ManageBundle\Controller;

use Symfony\Component\Debug\Exception\ContextErrorException;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;


class MailController extends Controller
{
    /*
     * reads a flat file to change the mailing list. 
     * mailinglist accepts adds from the front page pretty much, it filters email regular expressions to avoid
     * issues.
     * removing a user should be done from the admin side.
     * 
     * This controller handles sending an email and removing users. 
     */
    private $maillist = "/web/includes/maillist.txt";
    public function indexAction()
    {
        $maillist = $this->get('kernel')->getRootDir() . "/.." .$this->maillist;
        $fd = fopen($maillist, "r");
        $email_arr = array();
        while (($email = fgets($fd, 4096)) !== false) {
            array_push($email_arr, $email);
        }
        return $this->render('HillCMSManageBundle:Default:mail.html.twig',array("emails" => $email_arr));
    }
    function removeAction()
    {
    	$request = $this->getRequest();
    	if ($request->getMethod() != "POST"){
    		return new Response("Not permitted", 403);
    	}
    	$username = $request->request->get('username');
    	$password = $request->request->get('password');
    	
    	return new Response("Successfully added user " . $username . " " . $role .". Reload your page to modify the user.");
    }
    
    function sendAction()
    {
    	$request = $this->getRequest();
    	if ($request->getMethod() != 'POST'){
    		return new Response("Not permitted", 403);
    	}
        $subject = $request->request->get('subject');
        $body = $request->request->get('body');
        if($subject == "" || $body == ""){
            return new Response("Not enough info", "400");
        }
        $mailmaster = "webmaster@setaria.mocklerlab.org";
        $headers = 'From: ' .$mailmaster . "\r\n";
        $maillist = $this->get('kernel')->getRootDir() . "/.." .$this->maillist;
        $fd = fopen($maillist, "r");
        while (($email = fgets($fd, 4096)) !== false) {
            mail($email, $subject, $body, $headers);
        }
        fclose($fd);	
        return new Response("Sent message $subject.", 200);
    } 
    function userViewAction(){
        return $this->render('HillCMSManageBundle:Default:user_mail.html.twig',array());
    }
    function addUserAction(){
        $request = $this->getRequest();
        if ($request->getMethod() != 'POST'){
            return new Response("Not permitted", 403);
        }
        $email = $request->request->get("email");
        if ($email == ""){
            return new Response("No email provided", 400);
        }
        if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
            //Valid email!
            $maillist = $this->get('kernel')->getRootDir() . "/.." .$this->maillist;
            $fd = fopen($maillist, "a");
            fwrite($fd, $email . PHP_EOL);
            fclose($fd);
            return new Response("Added email: $email to mailing list.", 200);
        }
            return new Response("No email provided", 400);
   } 
}
