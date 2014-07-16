<?php

namespace SymfonyContrib\Bundle\CronBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use SymfonyContrib\Bundle\CronBundle\Entity\Cron;

class AdminController extends Controller
{
    /**
     * Lists all cron entries.
     */
    public function listAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $dql = "SELECT c
                FROM CronBundle:Cron c
                ORDER BY c.weight ASC";
        $result = $em->createQuery($dql)->getResult();

        $crons = [];
        foreach ($result as $cron) {
            $crons[$cron->getGroup() ?: 'Default'][] = $cron;
        }

        return $this->render('CronBundle:Admin:list.html.twig', [
            'crons' => $crons,
        ]);
    }

    /**
     * Add/Edit form callback.
     *
     * @param Request $request
     * @param null|int $id
     *
     * @return RedirectResponse|Response
     */
    public function formAction(Request $request, $id = null)
    {
        $em = $this->getDoctrine()->getManager();

        if ($id) {
            $cron = $em->find('CronBundle:Cron', $id);
        } else {
            $cron = new Cron();
        }

        $form = $this->createForm('cron_form', $cron, [
            'cancel_url' => $this->generateUrl('cron_admin_list'),
        ]);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($cron);
            $em->flush();

            // Create a success message for the user.
            $msg = ($id ? 'Updated ' : 'Created ') . $cron->getName();
            $this->get('session')->getFlashBag()->add('success', $msg);

            // Redirect to the admin list page.
            return $this->redirect($this->generateUrl('cron_admin_list'));
        }

        return $this->render('CronBundle:Admin:form.html.twig', [
            'cron' => $cron,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Delete an cron with confirmation.
     *
     * @param string $id ID of cron.
     *
     * @return Response
     */
    public function deleteAction($id)
    {
        $cron = $this->getDoctrine()
            ->getRepository('CronBundle:Cron')
            ->find($id);

        $options = [
            'message' => 'Are you sure you want to <strong>DELETE "' . $cron->getName() . '"</strong>?',
            'warning' => 'This can not be undone!',
            'confirm_button_text' => 'Delete',
            'cancel_link_text' => 'Cancel',
            'confirm_action' => [$this, 'cronDelete'],
            'confirm_action_args' => [
                'cron' => $cron,
            ],
            'cancel_url' => $this->generateUrl('cron_admin_list'),
        ];

        return $this->forward('ConfirmBundle:Confirm:confirm', ['options' => $options]);
    }

    /**
     * Delete confirmation callback.
     *
     * @param array $args
     *
     * @return RedirectResponse
     */
    public function cronDelete(array $args)
    {
        /** @var Cron $cron */
        $cron = $args['cron'];
        $em   = $this->getDoctrine()->getManager();
        $em->remove($cron);
        $em->flush();

        $msg = 'Deleted ' . $cron->getLabel();
        $this->get('session')->getFlashBag()->add('success', $msg);

        return $this->redirect($this->generateUrl('cron_admin_list'));
    }

    public function runAction($id)
    {
        $cron = $this->getDoctrine()
            ->getRepository('CronBundle:Cron')
            ->find($id);
        $executer = $this->get('cron.executer');
        $executer->runCron($cron);

        // Create a success message for the user.
        $msg = 'Ran ' . $cron->getName();
        $this->get('session')->getFlashBag()->add('success', $msg);

        // Redirect to the admin list page.
        return $this->redirect($this->generateUrl('cron_admin_list'));
    }
}
