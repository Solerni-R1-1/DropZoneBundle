<?php
namespace Icap\DropzoneBundle\Manager;

use Claroline\CoreBundle\Entity\Resource\ResourceNode;
use Claroline\CoreBundle\Manager\MaskManager;
use Claroline\CoreBundle\Entity\User;
use Icap\DropzoneBundle\Entity\Dropzone;
use JMS\DiExtraBundle\Annotation as DI;
use Doctrine\ORM\EntityManager;

/**
 * @DI\Service("icap.manager.correction_manager")
 */

class CorrectionManager
{
    private $container;
    private $maskManager;
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;
        /**
     * @DI\InjectParams({
     *      "container"     = @DI\Inject("service_container"),
     * 		"maskManager"   = @DI\Inject("claroline.manager.mask_manager"),
     *      "entityManager" = @DI\Inject("doctrine.orm.entity_manager")
     * })
     */
    public function __construct($container,MaskManager $maskManager, EntityManager $entityManager )
    {
        $this->container        =  $container;
        $this->maskManager      =  $maskManager;
        $this->entityManager    =  $entityManager;
    }


    public function getExaminersByCorrectionMade($corrections)
    {
        
        foreach ($corrections as $correction) {
              var_dump($correction->getUser()->getId());
        }
      
        die;
    }

    /* return one correction or null or false if this is a non-logical issue */
    public function getOneUnfinishedCorrection( $dropzone, $user ) {

        $corrections = $this->entityManager->getRepository('IcapDropzoneBundle:Correction')->getNotFinished($dropzone, $user);
               
        if ( count ($corrections) == 0 ) {
            $correction = null;
            
        } else if ( count ($corrections) > 1 ) {
            $correction = $this->chooseOneCorrection($corrections);
            
        } else if ( count ($corrections) == 1 ){
            $correction = $corrections[0];
            
        } else {
            $correction = false;
            
        }
        
        return $correction;
        
    }
    
    /* Deal with special case where a user has several unfinished corrections 
     * 
     */
    public function chooseOneCorrection(Array $corrections) {
        
        $tempClassification = array();
        
        /* Just separate corrections with grade and those without */
        foreach ( $corrections as $faultedCorrection ) {
            
            if ( count( $faultedCorrection->getGrades() ) <= 0 ) {
                $tempClassification['no-grade'][] = $faultedCorrection;
            } else {
                $tempClassification['with-grade'][] = $faultedCorrection;
            }
        }
        
        // If we have only one correction with correct grade, it's the one, remove the others
        if ( array_key_exists( 'with-grade', $tempClassification ) && count ( $tempClassification['with-grade'] == 1 ) ) {
            $correction = $tempClassification['with-grade'][0];
            $this->removeCorrections( $tempClassification['no-grade'] );
        // We have different corrections with a grade = we have a problem, but quickfix is : take the first one, and remove the others
        } else if ( array_key_exists( 'with-grade', $tempClassification ) && count ( $tempClassification['with-grade'] > 1 ) ) {
            $correction = $tempClassification['with-grade'][0];
            unset ( $tempClassification['with-grade'][0] );
            $correctionsToRemove = array_merge( $tempClassification['with-grade'], $tempClassification['no-grade'] );
            // Only no-grades corrections, take the first one...
        } else { 
             $correction = $tempClassification['no-grade'][0];
             unset ( $tempClassification['no-grade'][0] );
             $this->removeCorrections( $tempClassification['no-grade'] );
        }
        
        return $correction;
    }
    
    public function removeCorrections( Array $corrections ) {
        
        foreach ( $corrections as $correction ) {
            $this->entityManager->remove($correction);
        }
        
        $this->entityManager->flush();
        
    }

}