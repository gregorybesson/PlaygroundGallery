<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace PlaygroundGallery\Controller\Admin;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class GalleryAdminController extends AbstractActionController
{

    /**
    * @var $mediaService Service de l'entity media
    */
    protected $mediaService;

    /**
    * @var $categoryService Service de l'entity category
    */
    protected $categoryService;

    /**
    * liste des médias d'un Média 
    *
    * @return ViewModel Templates de la liste des medias
    */
    public function indexAction()
    {
        $user = $this->zfcUserAuthentication()->getIdentity();
        $categories = $this->getCategoryService()->getCategoryMapper()->findBy(array('parent' => null));
        $medias = $this->getMediaService()->getMediaMapper()->findAll();

        // Form media
        $formMedia = $this->getServiceLocator()->get('playgroundgallery_media_form');
        $formMedia->setAttribute('method', 'post');

        // Form Category
        $formCategory = $this->getServiceLocator()->get('playgroundgallery_category_form');
        $formCategory->setAttribute('method', 'post');
        $formCategory->setAttribute('action', $this->url()->fromRoute('admin/playgroundgallery/category/create'));
        
        return new ViewModel(compact('medias', 'categories', 'formMedia', 'formCategory', 'user'));
    }

    /**
    * Création d'un Média 
    *
    * @redirect vers la liste des medias
    */
    public function createAction()
    {
        $form = $this->getServiceLocator()->get('playgroundgallery_media_form');
        $form->setAttribute('method', 'post');
        $form->setAttribute('action', $this->url()->fromRoute('admin/playgroundgallery/create'));

        if ($this->getRequest()->isPost()) {
            $form->bind($this->getRequest()->getPost());
            $data = $this->getRequest()->getPost()->toArray();
            if($form->isValid() && $this->checkValidUrl($data['url'])) {
                $media = $this->getMediaService()->create($data);
                if($media) {
                    return $this->redirect()->toRoute('admin/playgroundgallery');
                }
                else {
                    $this->flashMessenger()->setNamespace('playgroundgallery')->addMessage('Error');
                }
            }
            else {
                $this->flashMessenger()->setNamespace('playgroundgallery')->addMessage('Error');
            }
        }

        return $this->redirect()->toRoute('admin/playgroundgallery');
    }

    /**
    * Edition d'un Média 
    *
    * @redirect vers la liste des medias
    */
    public function editAction()
    {
        $mediaId = $this->getEvent()->getRouteMatch()->getParam('mediaId');
        if (!$mediaId) {
            return $this->redirect()->toRoute('admin/playgroundgallery');
        }
        $media = $this->getMediaService()->getMediaMapper()->findByid($mediaId);


        $form = $this->getServiceLocator()->get('playgroundgallery_media_form');
        $form->bind($media);

        if ($this->getRequest()->isPost()) {
            $form->bind($this->getRequest()->getPost());
            $data = $this->getRequest()->getPost()->toArray();
            if($form->isValid() && $this->checkValidUrl($data['url'])) {
                $media = $this->getMediaService()->edit($data, $media);
                
                if($media) {
                    return $this->redirect()->toRoute('admin/playgroundgallery');
                }
                else {
                    $this->flashMessenger()->setNamespace('playgroundgallery')->addMessage('Error');
                }
            }
            else {
                $this->flashMessenger()->setNamespace('playgroundgallery')->addMessage('Error');
            }
        }

        return $this->redirect()->toRoute('admin/playgroundgallery');
    }

    /**
    * Vérifie la validité d'une url 
    *
    * @return boolean $headersBool validité de l'url
    */
    public function checkValidUrl($url) {
        $handle = curl_init($url);

        curl_setopt($handle,  CURLOPT_RETURNTRANSFER, TRUE);
        $response = curl_exec($handle);
        $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
        if($httpCode == 200) {
            $headersBool = true;
        }
        else {
            $headersBool = false;
        }

        curl_close($handle);

        return $headersBool;
    }

    /**
    * Suppression d'un Média 
    *
    * @redirect vers la liste des medias
    */
    public function removeAction() {
        $mediaId = $this->getEvent()->getRouteMatch()->getParam('mediaId');
        if (!$mediaId) {
            return $this->redirect()->toRoute('admin/playgroundgallery/create');
        }

        $media = $this->getMediaService()->getMediaMapper()->findByid($mediaId);

        $this->getMediaService()->getMediaMapper()->remove($media);

        return $this->redirect()->toRoute('admin/playgroundgallery');
    }

    /**
    * Téléchargement d'un media
    *
    * @redirect vers la liste des medias
    */
    public function downloadAction()
    {
        $mediaId = $this->getEvent()->getRouteMatch()->getParam('mediaId');
        if (!$mediaId) {
            return $this->redirect()->toRoute('admin/playgroundgallery/create');
        }

        $media = $this->getMediaService()->getMediaMapper()->findByid($mediaId);

        foreach (get_headers($media->getUrl()) as $value) {
            if(preg_match('%Content-Type%', $value)) {
                $typeMine = explode(':', $value);
                $typeMine = explode('/', end($typeMine));
                $mime = trim($typeMine[0]);
                $type = trim($typeMine[1]);
            }
        }

        $filename = $media->getName().'.'.$type;

        $response = $this->getResponse();

        $headers = $response->getHeaders();
        $headers->addHeaderLine('Content-Type', $mime)
                ->addHeaderLine(
                    'Content-Disposition', 
                    sprintf("attachment; filename=\"%s\"", $filename)
                )
                ->addHeaderLine('Accept-Ranges', 'bytes')
                ->addHeaderLine('Content-Length', strlen(file_get_contents($media->getUrl())));

        $response->setContent(file_get_contents($media->getUrl()));

        return $response;
    }

    /**
    * Creation d'une Categorie 
    *
    * @redirect vers la liste des medias
    */
    public function createCategoryAction() {
        $form = $this->getServiceLocator()->get('playgroundgallery_category_form');
        $form->setAttribute('method', 'post');

        if ($this->getRequest()->isPost()) {
            $category = $this->getCategoryService()->create($this->getRequest()->getPost()->toArray());
            
            if($category) {
                return $this->redirect()->toRoute('admin/playgroundgallery');
            }
            else {
                $this->flashMessenger()->setNamespace('playgroundgallery')->addMessage('Error');
            }
          
        }
    
        return $this->redirect()->toRoute('admin/playgroundgallery');
    }

    /**
    * Edition d'une Categorie 
    *
    * @redirect vers la liste des medias
    */
    public function editCategoryAction() {
        $categoryId = $this->getEvent()->getRouteMatch()->getParam('categoryId');
        if (!$categoryId) {
            return $this->redirect()->toRoute('admin/playgroundgallery');
        }
        $category = $this->getCategoryService()->getCategoryMapper()->findByid($categoryId);


        $form = $this->getServiceLocator()->get('playgroundgallery_category_form');
        $form->bind($category);

        if ($this->getRequest()->isPost()) {

            $data = $this->getRequest()->getPost()->toArray();
            $category = $this->getCategoryService()->edit($data, $category);
            if($category) {
                return $this->redirect()->toRoute('admin/playgroundgallery');
            }
            else {
                $this->flashMessenger()->setNamespace('playgroundgallery')->addMessage('Error');
            }
        }

        return $this->redirect()->toRoute('admin/playgroundgallery');
    }

    /**
    * Suppresion d'une Categorie 
    *
    * @redirect vers la liste des medias
    */
    public function removeCategoryAction() {
        $categoryId = $this->getEvent()->getRouteMatch()->getParam('categoryId');
        if (!$categoryId) {
            return $this->redirect()->toRoute('admin/playgroundgallery');
        }
        $category = $this->getCategoryService()->getCategoryMapper()->findByid($categoryId);
        
        if(count($category->getChildren())==0 && count($category->getMedias())==0) {
            $category = $this->getCategoryService()->getCategoryMapper()->remove($category);
        } else {
            $this->flashMessenger()->setNamespace('playgroundgallery')->addMessage('Error : you can\'t remove a category who contains one or more categories.');
        }
        return $this->redirect()->toRoute('admin/playgroundgallery');
    }
    
    /**
    * Recuperation du Service Category
    *
    * @return PlaygroundGallery\Service\Category $categoryService
    */
    public function getCategoryService()
    {
        if (!$this->categoryService) {
            $this->categoryService = $this->getServiceLocator()->get('playgroundgallery_category_service');
        }
        return $this->categoryService;
    }

    /**
    * Recuperation du Service Media
    *
    * @return PlaygroundGallery\Service\Media $mediaService
    */
    public function getMediaService()
    {
        if (!$this->mediaService) {
            $this->mediaService = $this->getServiceLocator()->get('playgroundgallery_media_service');
        }
        return $this->mediaService;
    }
}