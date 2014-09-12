<?php
namespace PlaygroundGallery\Controller\Api;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Http\Response;

class ApiController extends AbstractActionController
{
   /**
    * @var ServiceLocator $serviceLocator
    */
    protected $serviceLocator;

   /**
    * @return Reponse $response
    */
    public function indexAction()
    {
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $routes = array();

        $routes['gallery'][] = array(
            'routes' => '/gallery/country/:country[/offset/:offset][/limit/:limit][/tag/:tag][/type/:type]',
            'message' => 'liste les médias de la gallery',
            'required params' => array(
                'country' => '[a-z]*',
            ),
            'optionnals params' => array(
                'offset' => '[0-9]*',
                'tag' => '[0-9]*',
                'type' => '[a-z]*',
                'limit' => '[0-9]*',
            ),
            'examples' => array(
                'api/gallery/country/fr/offset/0/limit/20/tag/8/type/video',
                'api/gallery/country/fr/type/picture',
            )
        );
        $return = array();
        $return['status'] = 0;
        $return['routes'] = $routes;
        
        $response->setContent(json_encode($return));
        $config = $this->getServiceLocator()->get('Config');

        $response = $this->getResponse();
        $response->getHeaders()->addHeaderLine('Cache-Control', 'max-age='.$config['cache_time'])
                               ->addHeaderLine('User-Cache-Control', 'max-age='.$config['cache_time'])
                               ->addHeaderLine('Expires', gmdate("D, d M Y H:i:s", time() + $config['cache_time']))
                               ->addHeaderLine('Pragma', 'cache');
        
        return $response;
    }
}