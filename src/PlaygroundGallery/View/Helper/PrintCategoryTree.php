<?php

namespace PlaygroundGallery\View\Helper;

use Zend\View\Helper\AbstractHelper;

class PrintCategoryTree extends AbstractHelper
{
    
    protected $categoryService;

    /**
     * __invoke
     *
     * @access public
     * @param  array  $options array of options
     * @return string
     */
    public function __invoke($categories)
    {
        foreach ($categories as $category):
			$this->printChildren($category);
		endforeach;
    }

    private function printChildren($category, $wave = 0) {
		echo '<span href="#filter" data-option-value=".cat-'.strtolower($category->getId()).'" type="button" class="folder-btn" data-id="'.$category->getId().'" data-option-key="filter" style="margin-left: '. ( $wave*5 ) .'px;"><span class="glyphicon glyphicon-folder-close"></span>'.$category->getName().'<span class="border">&nbsp;</span></span>';
		echo '<div class="folder-'.$category->getId().'" style="display: none;">';
        foreach ($category->getChildren() as $children):
			$this->printChildren($children, $wave+2);
		endforeach;
        echo '</div>';
    }


     /**
    * getLocaleService : Recuperer le service des locales
    *
    * @return Service/Locale $localeService
    */
    public function getCategoryService()
    {
        if($this->categoryService === null){
            $this->categoryService = $this->getServiceLocator()->get('playgroundgallery_category_service');
        }
        return $this->categoryService;
    }

    /**
    * setLocaleService : set le service locale
    */
    public function setCategoryService ($categoryService)
    {
        $this->categoryService = $categoryService;
    }
}