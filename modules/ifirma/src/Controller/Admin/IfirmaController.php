<?php

namespace Ifirma\Controller\Admin;

use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Ifirma\ApiManager;
use Ifirma\InternalComunicationManager;

class IfirmaController extends FrameworkBundleAdminController
{
    public function sendInvoiceAction(Request $request)
    {
        $id = $request->attributes->get('id');
        $type = $request->attributes->get('type');
        $hash = $request->query->get('h');

        if (!$this->checkHash($hash)) {
            return $this->redirectToRoute('admin_orders_index');
        }

        $sendResult = ApiManager::getInstance()->sendInvoice($id, $type);
        InternalComunicationManager::getInstance()->{InternalComunicationManager::KEY_SEND_RESULT} = $sendResult;

        return $this->redirect($request->headers->get('referer'));
    }

    public function getInvoiceAction(Request $request)
    {
        $id = $request->attributes->get('id');
        $hash = $request->query->get('h');

        if (!$this->checkHash($hash)) {
            return $this->redirectToRoute('admin_orders_index');
        }

        $pdfContent = ApiManager::getInstance()->getDocumentAsPdf($id);
        if ($pdfContent === null) {
            $this->addFlash('error', 'Nie można pobrać faktury');
            return $this->redirect($request->headers->get('referer'));
        }

        $filename = ApiManager::getInstance()->getDocumentPdfName($id);

        $response = new Response($pdfContent);
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');

        return $response;
    }

    private function checkHash($hash)
    {
        return ($hash == \Configuration::get(\Ifirma::API_HASH));
    }
}