<?php

namespace Oktolab\Bundle\RentBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration;
use Oktolab\Bundle\RentBundle\Entity\Security\User;
use Oktolab\Bundle\RentBundle\Form\Security\UserType;

/**
 * Security\User controller
 *
 * @Configuration\Route("/admin/user")
 */
class UserManagementController extends Controller
{
    /**
     * Lists all Users
     *
     * @Configuration\Route("/", name="security_user")
     * @Configuration\Method("GET")
     * @Configuration\Template("OktolabRentBundle:Admin\User:index.html.twig")
     */
    public function indexAction()
    {
        $users = $this->getDoctrine()->getManager()->getRepository('OktolabRentBundle:Security\User')->findAll();
        return array('entities' => $users);
    }

    /**
     * Change Role of User
     * @Configuration\Route("/{id}/edit", name="security_user_edit")
     * @Configuration\ParamConverter("user", class="OktolabRentBundle:Security\User")
     * @Configuration\Method("GET")
     * @Configuration\Template("OktolabRentBundle:Admin\User:edit.html.twig")
     */
    public function editAction(User $user)
    {
        $editForm = $this->createForm(
            new UserType(),
            $user,
            array(
                'action' => $this->generateUrl('security_user_update', array('id' => $user->getId())),
                'method' => 'PUT',
            )
        );

        return array('user' => $user, 'edit_form' => $editForm->createView());
    }

    /**
     * @Configuration\Route("/{id}", name="security_user_update")
     * @Configuration\ParamConverter("user", class="OktolabRentBundle:Security\User")
     * @Configuration\Method("PUT")
     * @Configuration\Template("OktolabRentBundle:Admin\User:edit.html.twig")
     */
    public function updateAction(Request $request, User $user)
    {
        //Admins can edit themselves
        if ($user->getUsername() == $this->get('security.context')->getToken()->getUser()->getUsername()) {
            $this->get('session')->getFlashBag()->add('error', "admin.user.no_self_edit");
            return $this->redirect($this->generateUrl('security_user'));
        }

        $editForm = $this->createForm(
            new UserType(),
            $user,
            array(
                'action' => $this->generateUrl('security_user_update', array('id' => $user->getId())),
                'method' => 'PUT',
            )
        );

        $editForm->handleRequest($request);
        if ($editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $message = $this
                ->get('translator')
                ->trans('user.message.updateSuccess',array('%username%' => $user->getDisplayname()));
            $this->get('session')->getFlashBag()->add('success', $message);

            return $this->redirect($this->generateUrl('security_user'));
        }

        $this->get('session')->getFlashBag()->add('error', 'user.message.updateFailure');

        return array('user' => $user, 'edit_form' => $editForm->createView());
    }

    /**
     * Show User (and his log)
     * @Configuration\Route("/{id}/show", name="security_user_show")
     * @Configuration\ParamConverter("user", class="OktolabRentBundle:Security\User")
     * @Configuration\Method("GET")
     * @Configuration\Template("OktolabRentBundle:Admin\User:show.html.twig", vars={"user"})
     */
    public function showAction()
    {
    }
}
