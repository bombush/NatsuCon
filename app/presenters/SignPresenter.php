<?php

namespace Natsu\Presenters;

use Nette;
use Natsu\Forms\SignFormFactory;
use Natsu\Forms\ForgetFormFactory;
use Natsu\Forms\NewPassFormFactory;
use Natsu\Forms\MyAccountFormFactory;
use Natsu\Forms\RegistrationFormFactory;
use Natsu\Model\UserManager;
use Natsu\Model\EntityModel;

class SignPresenter extends BasePresenter {

    /** @var SignFormFactory @inject */
    public $factory;

    /** @var ForgetFormFactory @inject */
    public $factoryForget;

    /** @var NewPassFormFactory @inject */
    public $factoryNewPass;

    /** @var MyAccountFormFactory @inject */
    public $factoryMyAccount;

    /** @var RegistrationFormFactory @inject */
    public $factoryRegistration;

    /** @var UserManager @inject */
    public $userManager;

    /** @var EntityModel @inject */
    public $entityModel;
    private $code;
    private $userId;

    /**
     * Sign-in form factory.
     * @return Nette\Application\UI\Form
     */
    protected function createComponentSignInForm() {
        $form = $this->factory->create();
        $form->onSuccess[] = function ($form) {
            $form->getPresenter()->redirect('Homepage:');
        };
        return $form;
    }

    protected function createComponentRegistrationForm() {
        $form = $this->factoryRegistration->create();
        $this->factoryRegistration->setManager($this->userManager);

        $emailModel = $this->entityModel->reflection("email");
        $emailModel->setTable("emailtemplate");
        $this->factoryRegistration->setEmailModel($emailModel);
        $form->onSuccess[] = function ($form) {
            $form->getPresenter()->flashMessage("Registrace dokončena, přihlašte se");
            $form->getPresenter()->redirect('Sign:in');
        };
        return $form;
    }

    /**
     * Forget form factory.
     * @return Nette\Application\UI\Form
     */
    protected function createComponentForgetForm() {
        $form = $this->factoryForget->create();
        $this->factoryForget->setManager($this->userManager);

        $emailModel = $this->entityModel->reflection("email");
        $emailModel->setTable("emailtemplate");
        $this->factoryForget->setEmailModel($emailModel);
        $form->onSuccess[] = function ($form) {
            $form->getPresenter()->flashMessage("Požadavek o změnu hesla odeslán na e-mail!");
            $form->getPresenter()->redirect('Sign:forget');
        };
        return $form;
    }

    /**
     * Forget form factory.
     * @return Nette\Application\UI\Form
     */
    protected function createComponentMyAccountForm() {
        $this->factoryMyAccount->setManager($this->userManager);
        $this->factoryMyAccount->setUserId($this->getUser()->getId());
        $this->factoryMyAccount->setEmailModel($this->entityModel);
        $form = $this->factoryMyAccount->create();


        $form->onSuccess[] = function ($form) {
            $form->getPresenter()->flashMessage("OK");
            $form->getPresenter()->redirect('Sign:myaccount');
        };
        return $form;
    }
    
    
    /**
     * Forget form factory.
     * @return Nette\Application\UI\Form
     */
    protected function createComponentEditAccountForm() {
        $this->factoryMyAccount->setManager($this->userManager);
        $this->factoryMyAccount->setUserId($this->userId);
        $this->factoryMyAccount->setEmailModel($this->entityModel);
        $form = $this->factoryMyAccount->createAdmin();


        $form->onSuccess[] = function ($form) {
            $form->getPresenter()->redirect('Management:users');
        };
        return $form;
    }

    /**
     * Forget form factory.
     * @return Nette\Application\UI\Form
     */
    protected function createComponentNewPassForm() {
        $this->factoryNewPass->setManager($this->userManager);

        $this->factoryNewPass->setCode($this->code);
        $form = $this->factoryNewPass->create();


        $form->onSuccess[] = function ($form) {
            $form->getPresenter()->flashMessage("Heslo změněno!");
            $form->getPresenter()->redirect('Homepage:');
        };
        return $form;
    }
    
    
    
    /**
     * Forget form factory.
     * @return Nette\Application\UI\Form
     */
    protected function createComponentEditNewPassForm() {
        $this->factoryNewPass->setManager($this->userManager);

        $this->factoryNewPass->setCode($this->code);
        $form = $this->factoryNewPass->create();


        $form->onSuccess[] = function ($form) {
            $form->getPresenter()->redirect('in');
        };
        return $form;
    }

    public function actionGoogle() {
        $client_id = '889446373278-48us44fdiupdpadch01o1hvrpeu5nffn.apps.googleusercontent.com';
        $client_secret = '3h9o0PG-ZPbmgBVGoLomBcHM';
        $redirect_uri = 'http://www.natsucon.cz/sign/google';
        
        @session_start();

        $client = new \Google_Client();
        $client->setClientId($client_id);
        $client->setClientSecret($client_secret);
        $client->setRedirectUri($redirect_uri);
        $client->addScope("email");
       // $client->setAccessType("offline");
      //  $client->setApprovalPrompt("force");
      //  $client->addScope("profile");
        
       
      //  exit;

        $service = new \Google_Service_Oauth2($client);
        if (isset($_GET['code'])) {
            
        
            try{
            $client->authenticate($_GET['code']);
            }catch(\Exception $e){
                echo $e->getMessage();
                exit;
                
            }
            $_SESSION['access_token'] = $client->getAccessToken();
            
          //  echo $client->getAccessToken();exit;
            
            header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
            exit;
            
        }

//if we have access_token continue, or else get login URL for user
        if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
            
            $client->setAccessToken($_SESSION['access_token']);
        } else {
            $authUrl = $client->createAuthUrl();
        }

        if (isset($authUrl)) {
            $this->redirectUrl($authUrl);
        } else {
            try{
            $user = $service->userinfo->get();
            }catch(\Exception $e){
                $this->flashMessage("Službu nelze v tuto chvíli použít.".$e->getMessage());
                unset($_SESSION['access_token']);
                $this->redirect("Sign:in");
            }
            $email = $user->getEmail();
            try{
            $this->user->login($email, "**********GoogleApproved*************");
            unset($_SESSION['access_token']);
            }catch(\Exception $e){
                $this->flashMessage($e->getMessage());
                $this->redirect("Sign:in");
            }
            $this->flashMessage("Logged via Google");
            $this->redirect("Homepage:");
        }
    }

    public function actionNewpass($id) {
        $this->code = $id;
        $row = $this->userManager->identifyHash($id);
       // dump($row);
        if(empty($id) || $row === false){
            throw new Nette\Application\ForbiddenRequestException;
        }
        
        //  print_r($this->code); exit;
    }

    public function actionOut() {
        $this->getUser()->logout();
        $this->flashMessage('Odhlášení proběhlo úspěšně.');
        $this->redirect('Homepage:');
    }

    public function renderMyaccount() {
        if(!$this->user->loggedIn){
            throw new Nette\Application\ForbiddenRequestException;
        }
        
        $this->add("pageTitle", "Můj účet");
        $this->prepare();
    }
    
    public function actionEdit($id){
        $this->userId = $id;
    }

}
