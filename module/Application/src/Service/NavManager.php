<?php
namespace Application\Service;

/**
 * This service is responsible for determining which items should be in the main menu.
 * The items may be different depending on whether the user is authenticated or not.
 */
class NavManager
{
    /**
     * Auth service.
     *
     * @var Laminas\Authentication\Authentication
     */
    private $authService;
    
    /**
     * Url view helper.
     *
     * @var Laminas\View\Helper\Url
     */
    private $urlHelper;
    
    /**
     * RBAC manager.
     *
     * @var User\Service\RbacManager
     */
    private $rbacManager;
    
    private $sso_manager;
    
    /**
     * Constructs the service.
     */
    public function __construct($authService, $urlHelper, $rbacManager, $sso_manager) 
    {
        $this->authService = $authService;
        $this->urlHelper = $urlHelper;
        $this->rbacManager = $rbacManager;
        $this->sso_manager = $sso_manager;
    }
    
    /**
     * This method returns menu items depending on whether user has logged in or not.
     */
    public function getMenuItems() 
    {
        $url = $this->urlHelper;
        $items = [];

        if ($this->authService->hasIdentity()) {
            $items[] = [
            'id' => 'struct',
            'label' => 'Dashboard',
            'link'  => $url('struct')
            ];
        }
        
        
        if ($this->authService->hasIdentity()) {
            $items[] = [
            'id' => 'vposmoon',
            'label' => 'Moons',
            'link'  => $url('vposmoon')
            ];
        }
        
        if ($this->authService->hasIdentity()) {
            $items[] = [
            'id' => 'vpos',
            'label' => '(D)Scan',
            'link'  => $url('vpos')
            ];
        }

        if ($this->rbacManager->isGranted(null, 'moon.manage')) {
            $items[] = [
            'id' => 'ledger',
            'label' => 'Ledger',
            'link'  => $url('ledger')
            ];
        }

        $items[] = [
            'id' => 'about',
            'label' => 'About',
            'link'  => $url('about')
        ];
        
        // Display "Login" menu item for not authorized user only. On the other hand,
        // display "Admin" and "Logout" menu items only for authorized users.
        if (!$this->authService->hasIdentity()) {
            $items[] = [
                'id' => 'auth',
                'label' => 'Login with Eve SSO',
                'link'  => $url('auth'),
                'float' => 'right'
            ];
        } else {
            
            // Determine which items must be displayed in Admin dropdown.
            $adminDropdownItems = [];
            
            if ($this->rbacManager->isGranted(null, 'user.manage')) {
                $adminDropdownItems[] = [
                            'id' => 'users',
                            'label' => 'Manage Users',
                            'link' => $url('users')
                        ];
            }
            
            if ($this->rbacManager->isGranted(null, 'permission.manage')) {
                $adminDropdownItems[] = [
                            'id' => 'permissions',
                            'label' => 'Manage Permissions',
                            'link' => $url('permissions')
                        ];
            }
            
            if ($this->rbacManager->isGranted(null, 'role.manage')) {
                $adminDropdownItems[] = [
                            'id' => 'roles',
                            'label' => 'Manage Roles',
                            'link' => $url('roles')
                        ];
            }
            
            if (count($adminDropdownItems)!=0) {
                $items[] = [
                    'id' => 'admin',
                    'label' => 'Admin',
                    'dropdown' => $adminDropdownItems
                ];
            }
            
            $items[] = [
                'id' => 'logout',
                'label' => $this->authService->getIdentity(),
            'avatar' => 'https://imageserver.eveonline.com/Character/'.$this->sso_manager->getIdentityID().'_32.jpg',
                'float' => 'right',
                'dropdown' => [
                    [
                        'id' => 'logout',
                        'label' => 'Sign out',
            'link' => $url('auth', ['action'=>'logout'])
                    ],
                ]
            ];
        }
        
        return $items;
    }
}


