<?php

namespace Icap\DropzoneBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Claroline\CoreBundle\Entity\Workspace\AbstractWorkspace;

class DropzoneCommonType extends AbstractType
{
	private $workspace;
	
	public function __construct(AbstractWorkspace $workspace) {
		$this->workspace = $workspace;
	}	
	
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('stayHere', 'hidden', array(
                'mapped' => false
            ))
            ->add('autoCloseForManualStates', 'hidden', array(
                "mapped" => false
            ))
            ->add('instruction', 'tinymce', array(
                'required' => false
            ))
            ->add('correctionInstruction','tinymce',array('required' => false))

            ->add('allowWorkspaceResource', 'checkbox', array('required' => false))
            ->add('allowUpload', 'checkbox', array('required' => false))
            ->add('allowUrl', 'checkbox', array('required' => false))
            ->add('allowRichText', 'checkbox', array('required' => false))

            ->add('forumCategory', 'entity', array(
            		'label' => false,
            		'property' => 'forumNameAndCategoryName',
            		'empty_value' => 'dropzone_choose_forum_category',
            		'class' => 'ClarolineForumBundle:Category',
            		'required' => false,
            		'query_builder' => function ( \Doctrine\ORM\EntityRepository $er )  {
            			return $er->getQueryFindCategoriesByWorkspace($this->workspace);
            		}
            ))

            ->add('peerReview', 'choice', array(
                'required' => true,
                'choices' => array(
                    false => 'Standard evaluation',
                    true => 'Peer review evaluation'
                ),
                'expanded' => true,
                'multiple' => false
            ))
            ->add('expectedTotalCorrection', 'integer', array('required' => true))

            ->add('displayNotationToLearners', 'checkbox', array('required' => false))
            ->add('diplayCorrectionsToLearners','checkbox', array('required' => false))
            ->add('allowCorrectionDeny','checkbox',array('required'=>false))
            ->add('displayNotationMessageToLearners', 'checkbox', array('required' => false))
            ->add('successMessage','tinymce',array('required' => false))
            ->add('failMessage','tinymce',array('required' => false))
            ->add('minimumScoreToPass', 'integer', array('required' => true))

            ->add('manualPlanning', 'choice', array(
                'required' => true,
                'choices' => array(
                    true => 'manualPlanning',
                    false => 'sheduleByDate'
                ),
                'expanded' => true,
                'multiple' => false,
                'empty_data'  => false
            ))

            ->add('manualState', 'choice', array(
                'choices' => array(
                    'notStarted' => 'notStartedManualState',
                    'allowDrop' => 'allowDropManualState',
                    'peerReview' => 'peerReviewManualState',
                    'allowDropAndPeerReview' => 'allowDropAndPeerReviewManualState',
                    'finished' => 'finishedManualState',
                ),
                'expanded' => true,
                'multiple' => false
            ))
            ->add('autoCloseOpenedDropsWhenTimeIsUp','checkbox', array('required' => false))
            ->add('startAllowDrop', 'datetime', array('date_widget' => 'single_text', 'time_widget' => 'single_text', 'with_seconds' => false, 'required' => true))
            ->add('endAllowDrop', 'datetime', array('date_widget' => 'single_text', 'time_widget' => 'single_text', 'with_seconds' => false, 'required' => true))
            ->add('startReview', 'datetime', array('date_widget' => 'single_text', 'time_widget' => 'single_text', 'with_seconds' => false, 'required' => true))
            ->add('endReview', 'datetime', array('date_widget' => 'single_text', 'time_widget' => 'single_text', 'with_seconds' => false, 'required' => true));
    }

    public function getName()
    {
        return 'icap_dropzone_common_form';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'language' => 'en',
                'translation_domain' => 'icap_dropzone',
            )
        );
    }
}