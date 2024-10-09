<?php

// src/Menu/MenuBuilder.php

namespace App\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;

class MenuBuilder
{
    private $factory;

    /**
     * Add any other dependency you need...
     */
    public function __construct(FactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public function createMainMenu(array $options): ItemInterface
    {
        $menu = $this->factory->createItem('main');

//        $menu->addChild('Home', ['route' => 'homepage']);
        $menu->addChild('Invoices', ['route' => 'invoices']);
        $menu->addChild('Admin', ['route' => 'admin']);
        // ... add more children

        return $menu;
    }

    public function createSidebarMenu(array $options): ItemInterface
    {
        $menu = $this->factory->createItem('sidebar');

        if (isset($options['include_invoices']) && $options['include_invoices']) {

            $invoicesItem = $menu->addChild('invoices')
                ->setLabel('<i class="bi bi-file-earmark-text mr-2"></i>&nbsp; Factures &nbsp;<span class="dropdown-chevron"></span>')
                ->setExtras(['safe_label' => true])
                ->setAttribute('class', 'dropdown-sidebar-menu');

            $invoicesItem->addChild('All', ['route' => 'invoices'])
                ->setLabel('&nbsp; Voir tout &nbsp;')
                ->setExtras(['safe_label' => true]);

            $invoicesItem->addChild('New Invoice', ['route' => 'create_invoice'])
                ->setLabel('&nbsp; Nouvelle facture &nbsp;')
                ->setExtras(['safe_label' => true]);

            $menu->addChild('Admin', ['route' => 'admin'])
                ->setLabel('<i class="bi bi-gear mr-2"></i>&nbsp; Admin &nbsp;')
// <i class="bi bi-chevron-right small"></i>
                ->setExtras(['safe_label' => true]);

            $menu->addChild('Se déconnecter', ['route' => 'app_logout']) // Assurez-vous que 'app_logout' est la route correcte designée pour gérer la déconnexion dans votre application
            ->setLabel('<i class="bi bi-box-arrow-right mr-2"></i>&nbsp; Se déconnecter &nbsp;') // Vous pouvez changer l'icône et le texte ici
            ->setExtras(['safe_label' => true])
                ->setAttribute('class', 'logout-btn btn btn-outline-secondary btn-sm'); // Vous pouvez utiliser cette classe pour styler le bouton dans votre CSS
        }

        // ... add more children

        return $menu;
    }
}