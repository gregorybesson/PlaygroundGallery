<?php

namespace PlaygroundGallery\View\Helper;

use Zend\View\Helper\AbstractHelper;

class GetTags extends AbstractHelper
{
    
    protected $tagService;

    /**
     * @param  int  parent tag id
     */
    public function __invoke($parent = null)
    {
        return $this->getTagService()->getTagMapper()->findAll();
    }

    /**
    * getLocaleService : Recuperer le service des locales
    *
    * @return Service/Locale $localeService
    */
    public function getTagService()
    {
        if($this->tagService === null){
            $this->tagService = $this->getServiceLocator()->get('playgroundgallery_tag_service');
        }
        return $this->tagService;
    }

    /**
    * setLocaleService : set le service locale
    */
    public function setTagService ($tagService)
    {
        $this->tagService = $tagService;
    }
}
