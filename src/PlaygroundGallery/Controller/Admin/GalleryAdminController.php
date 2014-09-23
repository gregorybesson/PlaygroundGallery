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
     const MAX_PER_PAGE = 20;
    /**
    * @var $mediaService Service de l'entity media
    */
    protected $mediaService;
    protected $tagService;

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
        $filters = array();
        $order = 'ASC';
        $request = $this->getRequest();        
        $filtersGet = $this->getEvent()->getRouteMatch()->getParam('filters');
        if (!empty($filtersGet)) {
            $filters = unserialize(urldecode($filtersGet)); 
            $page = $this->getEvent()->getRouteMatch()->getParam('p');
        }
        if(empty($page)){
            $page = 1;
        }
       
        if ($request->isPost()) {
            $data = array_merge(
                    $request->getPost()->toArray(),
                    $request->getFiles()->toArray()
            );
            if(!empty($data["filters"])) {
                if(!empty($data["filters"]['type'])) {
                    $filters['type'] = $data["filters"]['type'];
                } 
                if(!empty($data["filters"]['category'])) {
                    $filters['category'] = $data["filters"]['category'];
                }   
                if(!empty($data["filters"]['order'])) {
                    $order = $data["filters"]['order'];
                    $filters['order'] = $order;
                }   
            }
        }
        $config = $this->getServiceLocator()->get('Config');
        if (!array_key_exists('autorize_user', $config) || !$config['autorize_user']) {
            $user = null;
        } else {
            $user = $this->zfcUserAuthentication()->getIdentity();
        }
        if (!array_key_exists('arbo', $config) || !$config['arbo']) {
            $arbo = null;
        } else {
            $arbo = $config['arbo'];
        }
        $categories = $this->getCategoryService()->getCategoryMapper()->findBy(array('parent' => null));
        $allMedias = $this->getMediasFromSQL($filters, $order);
        $nbResults = count($allMedias);
        $medias = array_slice($allMedias, ($page-1)*self::MAX_PER_PAGE, self::MAX_PER_PAGE);
       
        $mediasPaginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\ArrayAdapter($allMedias));
        $mediasPaginator->setItemCountPerPage(self::MAX_PER_PAGE);
        $mediasPaginator->setCurrentPageNumber($page);
        $formMedia = $this->getServiceLocator()->get('playgroundgallery_media_form');
        $formMedia->setAttribute('method', 'post');
        $formTag = $this->getServiceLocator()->get('playgroundgallery_tag_form');
        $formTag->setAttribute('method', 'post');
        $formTag->setAttribute('action', $this->url()->fromRoute('admin/playgroundgallery/tag/create'));
        $formCategory = $this->getServiceLocator()->get('playgroundgallery_category_form');
        $formCategory->setAttribute('method', 'post');
        $formCategory->setAttribute('action', $this->url()->fromRoute('admin/playgroundgallery/category/create'));
        $viewModel = new ViewModel();
        return $viewModel->setVariables(array('medias' => $medias,'categories' => $categories,'formMedia' => $formMedia, 'formTag' => $formTag, 'formCategory' => $formCategory,'user' => $user,'mediasPaginator' => $mediasPaginator,'nbResults' => $nbResults,'filters' => $filters));
    }

     public function getMediasFromSQL($filters, $order)
    {
        $entityManager = $this->getServiceLocator()->get('playgroundgallery_doctrine_em');

        $select = " SELECT m ";
        $from = " FROM PlaygroundGallery\Entity\Media m ";
        $where = " WHERE  1 = 1";
        $order = " ORDER BY m.name ".$order;

        if(!empty($filters['category'])) {
            $where .= " AND m.category = ".$filters['category'];
        }

        if(!empty($filters['type'])) {
            if($filters['type'] == 'pictures'){
               $where .= " AND m.url NOT LIKE '%youtube.com%'";  
            }
            if($filters['type'] == 'videos'){
               $where .= " AND m.url LIKE '%youtube.com%'";  
            }
        }

        $query = $select.$from.$where.$order;

        $medias = $entityManager->createQuery($query)->getResult();

        
        
        return $medias;
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
            $data = array_merge(
                $this->getRequest()->getPost()->toArray(),
                $this->getRequest()->getFiles()->toArray()
            );
            
            $checkUrl = true;
            if (isset($data['upload_or_paste']) && $data['upload_or_paste'] == 'upload') {
                $form->getInputFilter()->get('url')->setRequired(false);
                $form->getInputFilter()->get('poster')->setRequired(false);
                $checkUrl = false;
            } else {
                $form->getInputFilter()->get('uploadImage')->setRequired(false);
            }
            $form->getInputFilter()->get('tags')->setRequired(false);
            
            $form->bind($this->getRequest()->getPost());
            
            if($form->isValid() && (!$checkUrl || $this->checkValidUrl($data['url']))) {
                $media = $this->getMediaService()->create($data);
                if($media) {
                    $media->removeTag();
                    foreach ($this->getRequest()->getPost('tags') as $tagId) {
                        if ($tag = $this->getTagService()->getTagMapper()->findById($tagId)) {
                            $media->addTag($tag);
                        }
                    }
                    $this->getMediaService()->getMediaMapper()->update($media);
                    return $this->redirect()->toRoute('admin/playgroundgallery');
                }
                else {
                    $this->flashMessenger()->setNamespace('playgroundgallery')->addMessage('Error creating the media');
                }
            }
            else {
                $this->flashMessenger()->setNamespace('playgroundgallery')->addMessage('Invalid form');
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
        $media = $this->getMediaService()->getMediaMapper()->findById($mediaId);


        $form = $this->getServiceLocator()->get('playgroundgallery_media_form');
        
        $form->bind($media);

        if ($this->getRequest()->isPost()) {
            
            $checkUrl = true;
            if (isset($data['upload_or_paste']) && $data['upload_or_paste'] == 'upload') {
                $form->getInputFilter()->get('url')->setRequired(false);
                $form->getInputFilter()->get('poster')->setRequired(false);
                $checkUrl = false;
            } else {
                $form->getInputFilter()->get('uploadImage')->setRequired(false);
            }
            $form->getInputFilter()->get('tags')->setRequired(false);
            
            $form->bind($this->getRequest()->getPost());
            $data = $this->getRequest()->getPost()->toArray();
            if($form->isValid() && (!$checkUrl || $this->checkValidUrl($data['url']))) {
                $media = $this->getMediaService()->edit($data, $media);
                
                if($media) {
                    $media->removeTag();
                    foreach ($this->getRequest()->getPost('tags') as $tagId) {
                        if ($tag = $this->getTagService()->getTagMapper()->findById($tagId)) {
                            $media->addTag($tag);
                        }
                    }
                    $this->getMediaService()->getMediaMapper()->update($media);
                }
                
                if($media) {
                    return $this->redirect()->toRoute('admin/playgroundgallery');
                }
                else {
                    $this->flashMessenger()->setNamespace('playgroundgallery')->addMessage('Error saving the media');
                }
            }
            else {
                $this->flashMessenger()->setNamespace('playgroundgallery')->addMessage('Invalid form');
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

        $media = $this->getMediaService()->getMediaMapper()->findById($mediaId);

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

        $media = $this->getMediaService()->getMediaMapper()->findById($mediaId);

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
        $category = $this->getCategoryService()->getCategoryMapper()->findById($categoryId);


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
        $category = $this->getCategoryService()->getCategoryMapper()->findById($categoryId);
        
        if(count($category->getChildren())==0 && count($category->getMedias())==0) {
            $category = $this->getCategoryService()->getCategoryMapper()->remove($category);
        } else {
            $this->flashMessenger()->setNamespace('playgroundgallery')->addMessage('Error : you can\'t remove a category who contains one or more categories.');
        }
        return $this->redirect()->toRoute('admin/playgroundgallery');
    }
    
    /**
     * Creation d'un tag
     *
     * @redirect vers la liste des medias
     */
    public function createTagAction() {
        $form = $this->getServiceLocator()->get('playgroundgallery_tag_form');
        $form->setAttribute('method', 'post');
    
        if ($this->getRequest()->isPost()) {
            $exists = $this->getTagService()->getTagMapper()->findBy(array('name' => $this->getRequest()->getPost('name')));
            if (count($exists)) {
                $this->flashMessenger()->setNamespace('playgroundgallery')->addMessage('A tag already exists with the same name');
                return $this->redirect()->toRoute('admin/playgroundgallery');
            }
            $tag = $this->getTagService()->create($this->getRequest()->getPost()->toArray());

            if($tag) {
                return $this->redirect()->toRoute('admin/playgroundgallery');
            }
            else {
                $this->flashMessenger()->setNamespace('playgroundgallery')->addMessage('Error');
            }
    
        }
    
        return $this->redirect()->toRoute('admin/playgroundgallery');
    }
    
    /**
     * Edition d'un tag
     *
     * @redirect vers la liste des medias
     */
    public function editTagAction() {
        $tagId = $this->getEvent()->getRouteMatch()->getParam('tagId');
        if (!$tagId) {
            return $this->redirect()->toRoute('admin/playgroundgallery');
        }
        $tag = $this->getTagService()->getTagMapper()->findById($tagId);
    
    
        $form = $this->getServiceLocator()->get('playgroundgallery_tag_form');
        $form->bind($tag);
    
        if ($this->getRequest()->isPost()) {
    
            $data = $this->getRequest()->getPost()->toArray();
            $tag = $this->getTagService()->edit($data, $tag);
            if($tag) {
                return $this->redirect()->toRoute('admin/playgroundgallery');
            }
            else {
                $this->flashMessenger()->setNamespace('playgroundgallery')->addMessage('Error');
            }
        }
    
        return $this->redirect()->toRoute('admin/playgroundgallery');
    }
    
    /**
     * Suppresion d'un tag
     *
     * @redirect vers la liste des medias
     */
    public function removeTagAction() {
        $tagId = $this->getEvent()->getRouteMatch()->getParam('tagId');
        if (!$tagId) {
            return $this->redirect()->toRoute('admin/playgroundgallery');
        }
        $tag = $this->getTagService()->getTagMapper()->findById($tagId);
    
        $tag = $this->getTagService()->getTagMapper()->remove($tag);
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
    
    public function getTagService()
    {
        if (!$this->tagService) {
            $this->tagService = $this->getServiceLocator()->get('playgroundgallery_tag_service');
        }
        return $this->tagService;
    }
}