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

    protected $mediaService;
    protected $categoryService;

    protected $moduleOptions;

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

    public function createAction()
    {
        $form = $this->getServiceLocator()->get('playgroundgallery_media_form');
        $form->setAttribute('method', 'post');
        $form->setAttribute('action', $this->url()->fromRoute('admin/playgroundgallery/create'));

        if ($this->getRequest()->isPost()) {
            $form->bind($this->getRequest()->getPost());
            $data = $this->getRequest()->getPost()->toArray();
            try {
                $headers = get_headers($data['url']);
                $headersBool = true;
            }
            catch(Exception $e) {
                $headersBool = false;
            }
            if($form->isValid() && $headersBool) {
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
            try {
                $headers = get_headers($data['url']);
                $headersBool = true;
            }
            catch(Exception $e) {
                $headersBool = false;
            }
            if($form->isValid() && $headersBool) {
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

    public function removeAction() {
        $mediaId = $this->getEvent()->getRouteMatch()->getParam('mediaId');
        if (!$mediaId) {
            return $this->redirect()->toRoute('admin/playgroundgallery/create');
        }

        $media = $this->getMediaService()->getMediaMapper()->findByid($mediaId);

        $this->getMediaService()->getMediaMapper()->remove($media);

        return $this->redirect()->toRoute('admin/playgroundgallery');
    }

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

    public function createCategoryAction() {
        $form = $this->getServiceLocator()->get('playgroundgallery_category_form');
        $form->setAttribute('method', 'post');

        if ($this->getRequest()->isPost()) {
            $contact = $this->getCategoryService()->create($this->getRequest()->getPost()->toArray());
            
            if($contact) {
                return $this->redirect()->toRoute('admin/playgroundgallery');
            }
            else {
                $this->flashMessenger()->setNamespace('playgroundgallery')->addMessage('Error');
            }
          
        }
    
        return $this->redirect()->toRoute('admin/playgroundgallery');
    }
    
    public function getCategoryService()
    {
        if (!$this->categoryService) {
            $this->categoryService = $this->getServiceLocator()->get('playgroundgallery_category_service');
        }
        return $this->categoryService;
    }

    public function getMediaService()
    {
        if (!$this->mediaService) {
            $this->mediaService = $this->getServiceLocator()->get('playgroundgallery_media_service');
        }
        return $this->mediaService;
    }

    public function getModuleOptions()
    {
        if (!$this->moduleOptions) {
            $this->moduleOptions = $this->getServiceLocator()->get('playgroundgallery_module_options');
        }
        return $this->moduleOptions;
    }
}