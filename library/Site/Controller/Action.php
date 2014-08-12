<?php
class Site_Controller_Action extends Zend_Controller_Action{
public function init()
{
	
    //sessionda mesaj varsa okuyup view'a aktar ve sessiondan sil...
      $this->session = new Zend_Session_Namespace('genel');
 
      $this->view->bilgiMesaji=$this->session->bilgiMesaji;
      $this->session->bilgiMesaji="";  
      $this->view->hataMesaji=$this->session->hataMesaji;
      $this->session->hataMesaji="";  
}

public function preDispatch()
{
  if ($this->userSession->grup_kodu!=1) 
  {
  	$acl=$this->userSession->acl;

  	if ($acl->has($this->getRequest()->getControllerName())) 
  	{
  		  try
         {
            if (!$acl->isAllowed($this->userSession->grup_kodu,$this->_request->getControllerName(),$action)) 
            {
              Ubit_Session::hataMesaji("Yetkiniz Yok!");
              $this->_redirect('/error/error');
            }

         }
        catch(Zend_Exception $e)
         {
           echo $e->getMessage();exit;
         } 
  	}
   else  
    {
      Ubit_Session::hataMesaji("Yetkiniz Yok!");
      $this->_redirect('/error/error');
    } 
  }
}



}