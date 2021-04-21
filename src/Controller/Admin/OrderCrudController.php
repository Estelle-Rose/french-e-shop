<?php

namespace App\Controller\Admin;

use App\Classes\MailJet;
use App\Entity\Order;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\CrudUrlGenerator;

class OrderCrudController extends AbstractCrudController
{
    private $entityManager;
    private $crudUrlGenerator; // permet de manager l'url de redirection

    public function __construct(EntityManagerInterface $entityManager, CrudUrlGenerator $crudUrlGenerator)
    {
        $this->entityManager = $entityManager;
        $this->crudUrlGenerator = $crudUrlGenerator;
    }

    public static function getEntityFqcn(): string
    {
        return Order::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        $preparationState = Action::new('preparationState', 'En préparation', 'fas fa-box-open')->linkToCrudAction('preparationState');
        $deliveryState = Action::new('deliveryState', 'Livraison en cours', 'fas fa-truck')->linkToCrudAction('deliveryState');
        return $actions
            ->add('index', 'detail')
            ->add('detail', $preparationState)
            ->add('detail', $deliveryState)
            ->setPermission(Action::DELETE, 'ROLE_SUPER_ADMIN');
    }
    public function preparationState(AdminContext $adminContext)
    {
    $order = $adminContext->getEntity()->getInstance();
    if($order->getState() == 1) {
        $order->setState(2);
        $this->entityManager->flush();
        $this->addFlash('notice', "<div class='alert alert-success'>La commande ".$order->getReference()." est bien en cours de préparation</div>");
        $url = $this->crudUrlGenerator->build()
            ->setController(OrderCrudController::class)
            ->setAction('index')
            ->generateUrl();

        // envoi du mail au user
        $mail = new MailJet();
        $content = "Bonjour ".$order->getUser()->getFullname()."<br/><p>Votre commande".$order->getReference()." est en cours de préparation, nous vous enverrons un mail dès que votre commande sera prise en charge par le transporteur</p><p>Merci et à bientôt !</p>";
        $mail->send($order->getUser()->getEmail(), $order->getUser()->getFullname(),'Commande envoyée', $content);

        return $this->redirect($url);
    } else {
        $urlError = $this->crudUrlGenerator->build()
            ->setController(OrderCrudController::class)
            ->setAction('index', 'detail')
            ->generateUrl();
    return $this->redirect($urlError);
    }
    }
    public function deliveryState(AdminContext $adminContext)
    {
        $order = $adminContext->getEntity()->getInstance();
        if ($order->getState() == 2) {
            $order->setState(3);
            $this->entityManager->flush();
            $this->addFlash('notice', "<div class='alert alert-success'>La commande " . $order->getReference() . " est en cours de livraison</div>");
            $url = $this->crudUrlGenerator->build()
                ->setController(OrderCrudController::class)
                ->setAction('index')
                ->generateUrl();

            $mail = new MailJet();
            $content = "Bonjour ".$order->getUser()->getFullname()."<br/><p>Votre commande".$order->getReference()." est en cours de livraison</p><p>Merci et à bientôt !</p>";
            $mail->send($order->getUser()->getEmail(), $order->getUser()->getFullname(),'Commande envoyée', $content);

            return $this->redirect($url);
        } else {
           $this->addFlash('notice', "<div class='alert alert-warning'>Le statut de la commande ne permet pas cette action</div>");
            $urlError = $this->crudUrlGenerator->build()
                ->setController(OrderCrudController::class)
                ->setAction('index','detail')
                ->generateUrl();
            return $this->redirect($urlError);
        }
    }
    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setDefaultSort(['id' => 'DESC']);

    }

    public function configureFields(string $pageName): iterable
    {
        return [

            IdField::new('id')->hideOnForm(),
            DateField::new('createdAt', 'Passée le'),
            TextField::new('user.getFullName', 'Nom client'),
            TextField::new('carrierName', 'Transporteur'),
            MoneyField::new('carrierPrice', 'Frais de livraison')->setCurrency('EUR'),
            MoneyField::new('total')->setCurrency('EUR'),
            TextEditorField::new('delivery', 'Adresse de livraison')->hideOnIndex(),
            ChoiceField::new('state')->setChoices([
                'Non payée' => 0,
                'Payée' => 1,
                'En préparation' => 2,
                "Envoyée" => 3,
                "Annulée" => 4,
            ]),
            ArrayField::new('orderDetails', 'Produits achetés')->hideOnIndex()
        ];
    }


}