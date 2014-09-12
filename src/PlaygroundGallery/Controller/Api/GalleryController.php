<?php
namespace PlaygroundGallery\Controller\Api;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Http\Response;
use Doctrine\ORM\Tools\Pagination\Paginator;

class GalleryController extends AbstractActionController
{
    const LIMIT = 20;
    /**
    * @var ServiceLocator $serviceLocator
    */
    protected $serviceLocator;


    /**
    * Permet de récuperer la liste des medias
    *  
    * locale : locale de traduction
    *
    * Retour un tableau JSON des traductions
    *
    * @return Reponse $response
    */
    public function listAction()
    {
        $return = array();
        $response = $this->getResponse();
        $response->setStatusCode(200);

//         $country = strtolower($this->getEvent()->getRouteMatch()->getParam('country'));
        $offset = strtolower($this->getEvent()->getRouteMatch()->getParam('offset'));
        $limit = strtolower($this->getEvent()->getRouteMatch()->getParam('limit'));
        $tag = strtolower($this->getEvent()->getRouteMatch()->getParam('tag'));
        $type = strtolower($this->getEvent()->getRouteMatch()->getParam('type'));

//         if (empty($country)) {
//             $return['status'] = 1;
//             $return['message'] = "invalid argument : country is required";
//             $response->setContent(json_encode($return));
        
//             return $response;
//         }

        $websites = $this->getServiceLocator()->get('playgroundcore_website_service')->getWebsiteMapper()->findBy(array('code' => strtoupper($country)));
        if (count($websites) == 0) {
            $return['status'] = 1;
            $return['message'] = "invalid argument : country is not verified";
            $response->setContent(json_encode($return));
        
            return $response;
        }

        $website = $websites[0];

        if (empty($offset)) {
            $offset = 0;
        }

        if(empty($limit) || $limit > 100) {
            $limit = self::LIMIT;
        }


        $em = $this->getServiceLocator()->get('playgroundgallery_doctrine_em');
        /* @var $qb \Doctrine\ORM\QueryBuilder */
        $qb = $em->createQueryBuilder();
        $qb->select('m AS media', '(CASE WHEN (m.url LIKE \'%youtube.com%\') THEN \'video\' ELSE \'picture\' END) AS type')
                ->from('PlaygroundGallery\Entity\Media', 'm')
//                 ->innerJoin('m.category', 'c', \Doctrine\ORM\Query\Expr\Join::WITH)
//                 ->innerJoin('c.websites', 'w', \Doctrine\ORM\Query\Expr\Join::WITH, 'w.code = :code')
        ;
        
//         $bind = array('code' => $country);
        
        if ($tag && is_numeric($tag)) {
            $qb->innerJoin(
                'm.tags', 
                't',
                \Doctrine\ORM\Query\Expr\Join::WITH,
                't.id = :tag'
            );
            $bind['tag'] = $tag;
        }
        
        if (!$type) {
            $qb->orderBy('type', 'DESC');
        } else if (is_string($type)){
            $qb->having($qb->expr()->eq('type', ':type'));
            $bind['type'] = $type;
        }
        
        $query = $em->createQuery($qb);
        $query->setFirstResult($offset)
                ->setMaxResults($limit);

        foreach ($bind as $key => $value) {
            $query->setParameter($key, $value);
        }
        
        $paginator = new Paginator($query, $fetchJoinCollection = true);
        
        $mediasTab = array();

        foreach ($paginator as $row) {
            $media = $row['media'];
            $mediasTab[] = array(
                'id'            => $media->getId(),
                'credit'        => $media->getCredit(),
                'description'   => $media->getDescription(),
                'url'           => $media->getUrl(),
                'poster'        => $media->getPoster(),
                'type'          => $row['type']
            );
        }

        $return['status'] = 0;
        $return['nb_results'] = count($mediasTab);
        $return['medias'] = $mediasTab;

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