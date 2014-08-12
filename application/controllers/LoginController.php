<?php 

class LoginController extends Zend_Controller_Action {

public function indexAction() 
  {


  }

public function controlAction()

  {
    $post=$this->getRequest()->getPost();

    $db=Zend_Db_Table::getDefaultAdapter();


    $authAdapter=new Zend_Auth_Adapter_DbTable($db);
    $authAdapter->setTableName("tbl_kullanici")
                ->setIdentityColumn("kullanici_adi")
                ->setCredentialColumn("parola") 
                ->setIdentity($post['kullanici_adi'])
                ->setCredential($post['parola']);


    $auth = Zend_Auth::getInstance();

try
 {
    $result = $auth->authenticate($authAdapter);

    if (!$result->isValid())
    {
      $this->_redirect("/login/index");
    }
    else
    {

      $tblgrup= new TblKullanici();
      $kullanici_adi=$this->getRequest()->getParam("kullanici_adi");
      $select=$tblgrup->select()->where("kullanici_adi=?",$kullanici_adi);               
      $data=$tblgrup->fetchAll($select)->toArray();
      $kullanici=$data[0];
      $ses= new Zend_Session_Namespace('userSession');
      $grup_kodu= $kullanici['grup_kodu'] ? $kullanici['grup_kodu'] : 4;

      $acl=new Zend_Acl();
      $role=new Zend_Acl_Role($grup_kodu);
      $acl->addRole($role);
      $acl->add(new Zend_Acl_Resource('index')); 
      $acl->allow($grup_kodu,'index','index');

      $tblacl=new TblYetki();
      $select=$tblacl->select()->where("grup_kodu=?", $grup_kodu);
      $grupHak=$tblacl->fetchAll($select)->toArray();
     //$this->grupHak=$grupHak[0];

      foreach ($this->grupHak as $gHak) 
      {
        if (!$acl->has(new Zend_Acl_Resource($gHak['controller'])))
         {
           $acl->add(new Zend_Acl_Resource($gHak['controller']));
         }

        $acl->allow($gHak['grup_kodu'],$gHak['controller'],$gHak['action']);  
      }

      $ses->acl=$acl;
      $ses->grup_kodu=$grup_kodu;
      $this->_redirect("/index/index");

    }
  }  
  catch(Zend_Exception $e) 
  {
    echo $e->getMessage();exit;
  }

  }


public function logoutAction()

  {
      $auth = Zend_Auth::getInstance();
      $auth->clearIdentity();
      $this->_redirect("/login/index");
  }


}

?>