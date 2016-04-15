<?php
/**
 * @author Emosys Team
 * @copyright Copyright (c) 2016 Emosys Ltd (http://www.emosys.com)
 * @package Emosys_Banner
 */
namespace Emosys\Banner\Controller\Adminhtml\Photo;

use Magento\Backend\App\Action;
use Magento\TestFramework\ErrorLog\Logger;
use Magento\Framework\Controller\Result\JsonFactory;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Image\AdapterFactory
     */
    protected $_adapterFactory;

    /**
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    protected $_uploader;

    /**
     * @var \Emosys\Banner\Model\Photo\Media\Config
     */
    protected $_mediaConfig;

    /**
     * Filesystem facade
     *
     * @var \Magento\Framework\Filesystem
     */
    protected $_filesystem;

    /**
     * @var JsonFactory
     */
    protected $_resultJsonFactory;

    /**
     * @param Action\Context $context
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\Image\AdapterFactory $adapterFactory,
        \Magento\MediaStorage\Model\File\UploaderFactory $uploader,
        \Emosys\Banner\Model\Photo\Media\Config $mediaConfig,
        \Magento\Framework\Filesystem $filesystem,
        JsonFactory $resultJsonFactory
    )
    {
        $this->_adapterFactory = $adapterFactory;
        $this->_uploader = $uploader;
        $this->_mediaConfig = $mediaConfig;
        $this->_filesystem = $filesystem;
        $this->_resultJsonFactory = $resultJsonFactory;

        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Emosys_Banner::save');
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            /** @var \Ashsmith\Blog\Model\Post $model */
            $model = $this->_objectManager->create('Emosys\Banner\Model\Photo');

            $id = $this->getRequest()->getParam('photo_id');
            if ($id) {
                $model->load($id);
            }

            if (isset($_FILES['file']) && isset($_FILES['file']['name']) && strlen($_FILES['file']['name'])) {
                /*
                * Save image upload
                */
                try {
                    $uploader = $this->_uploader->create(
                        ['fileId' => 'file']
                    );
                    $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
                    $imageAdapter = $this->_adapterFactory->create();
                    $uploader->addValidateCallback('image', $imageAdapter, 'validateUploadFile');
                    $uploader->setAllowRenameFiles(true);
                    $uploader->setFilesDispersion(true);
                    $mediaDirectory = $this->_filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
                    $result = $uploader->save(
                        $mediaDirectory->getAbsolutePath( $this->_mediaConfig->getBaseMediaPath() )
                    );
                    $data['file'] = $this->_mediaConfig->getBaseMediaPath() . $result['file'];
                } catch (\Exception $e) {
                    if ($e->getCode() == 0) {
                        $this->messageManager->addError($e->getMessage());
                    }
                }
            } else {
                if (isset($data['file']) && isset($data['file']['value'])) {
                    if (isset($data['file']['delete'])) {
                        $data['file'] = null;
                        $data['delete_image'] = true;
                    } elseif (isset($data['file']['value'])) {
                        $data['file'] = $data['file']['value'];
                    } else {
                        $data['file'] = null;
                    }
                }
            }

            /** @var \Magento\Framework\Controller\Result\Json $resultJson */
            $resultJson = $this->_resultJsonFactory->create();
            //$data['extra_option'] = $resultJson->setData($data['extra_option']);

            $model->setData($data);

            $this->_eventManager->dispatch(
                'emosys_banner_photo_prepare_save',
                ['photo' => $model, 'request' => $this->getRequest()]
            );

            try {
                $model->save();
                $this->messageManager->addSuccess(__('You saved this photo.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['photo_id' => $model->getId(), '_current' => true]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the photo.'));
            }

            $this->_getSession()->setFormData($data);
            return $resultRedirect->setPath('*/*/edit', ['photo_id' => $this->getRequest()->getParam('photo_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}