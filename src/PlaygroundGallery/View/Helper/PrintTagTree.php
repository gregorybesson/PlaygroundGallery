<?php

namespace PlaygroundGallery\View\Helper;

use Zend\View\Helper\AbstractHelper;

class PrintTagTree extends AbstractHelper
{
    
    protected $tagService;

    /**
     * @param  int  parent tag id
     */
    public function __invoke($parent = null)
    {
        $tags = $this->getTagService()->getTagMapper()->findBy(array('parent' => $parent));
        foreach ($tags as $tag):
			$this->printChildren($tag);
		endforeach;
    }

    private function printChildren($tag, $wave = 0)
    {
		$glyphiconFolder = "glyphicon-folder-close";
        echo '<span data-toggle="modal" href="#modal_edit_tag_'.$tag->getId().'" class="glyphicon glyphicon-pencil" style="float: right;margin-left: 5px;margin-right: 2px;cursor: pointer;z-index:512"></span><a href="#" class="folder-btn" style="margin-left: '. ( $wave*5 ) .'px;"><span class="glyphicon '.$glyphiconFolder.' "></span>'.$tag->getName().'<span class="border"></span></a>';
        echo '<div class="folder-'.$tag->getId().'" style="display: block;">';
        foreach ($tag->getChildren() as $child) {
            $this->printChildren($child, $wave+2);
        }
        echo '</div>';
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
