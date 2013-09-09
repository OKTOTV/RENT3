<?php

namespace Oktolab\Bundle\RentBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Oktolab\Bundle\RentBundle\Entity\Security\User;
use Oktolab\Bundle\RentBundle\Form\Security\UserType;

/**
 * Security\User controller
 *
 * @Route("/admin/user")
 */
class UserManagementController extends Controller
{
    /**
     * Lists all Users
     *
     * @Route("/", name="security_user")
     * @Method("GET")
     * @Template("OktolabRentBundle:Admin\User:index.html.twig")
     */
    public function indexAction ()
    {
        $users = $this->getDoctrine()->getManager()->getRepository('OktolabRentBundle:Security\User')->findAll();
        return array('entities' => $users);
    }

    /**
     * Change Role of User
     * @Route("/{id}/edit", name="security_user_edit")
     * @ParamConverter("user", class="OktolabRentBundle:Security\User")
     * @Method("GET")
     * @Template("OktolabRentBundle:Admin\User:edit.html.twig")
     */
    public function editAction (User $user)
    {

        $editForm = $this->createForm(
            new UserType(),
            $user,
            array(
                'action' => $this->generateUrl('security_user_update', array('id' => $user->getId())),
                'method' => 'PUT',
            )
        );

        return array(
            'user'      => $user,
            'edit_form' => $editForm->createView()
        );
    }

    /**
     * @Route("/{id}", name="security_user_update")
     * @ParamConverter("user", class="OktolabRentBundle:Security\User")
     * @Method("PUT")
     * @Template("OktolabRentBundle:Admin\User:edit.html.twig")
     */
    public function updateAction (Request $request, User $user)
    {
        //Admins can edit themselves
        if ($user->getUsername() == $this->get('security.context')->getToken()->getUser()->getUsername()) {
            $this->get('session')->getFlashBag()->add('error', "Admins can't edit themselves");
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

            $this->get('session')->getFlashBag()->add('success', 'Successfully updated User.');
            return $this->redirect($this->generateUrl('security_user'));
        }

        $this->get('session')->getFlashBag()->add('error', 'There was an error while saving the form.');
        return array(
            'user'      => $user,
            'edit_form'   => $editForm->createView(),
        );
    }

    /**
     * Show User (and his log)
     * @Route("/{id}/show", name="security_user_show")
     * @ParamConverter("user", class="OktolabRentBundle:Security\User")
     * @Method("Get")
     * @Template("OktolabRentBundle:Admin\User:show.html.twig", vars={"user"})
     */
    public function showAction (User $user)
    {
    }
}
