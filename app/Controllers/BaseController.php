<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Models\UserModel;

/**
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 *
 * Extend this class in any new controllers:
 * ```
 *     class Home extends BaseController
 * ```
 *
 * For security, be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    protected $helpers = ['url', 'app', 'form'];

    /**
     * Render a page view with the data supplied by its controller.
     * Page views extend layouts/main and provide the shared layout themselves.
     *
     * @param array<string, mixed> $data
     */
    protected function renderPage(string $page, array $data = []): string
    {
        // The shared sidebar needs the profile on every page. Reuse the profile
        // query when ProfileController already supplied it.
        if (! array_key_exists('sidebarUser', $data)) {
            $data['sidebarUser'] = array_key_exists('user', $data)
                ? $data['user']
                : model(UserModel::class)->currentPublicUser();
        }

        return view($page, $data);
    }

    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */

    // protected $session;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Load here all helpers you want to be available in your controllers that extend BaseController.
        // Caution: Do not put the this below the parent::initController() call below.
        // $this->helpers = ['form', 'url'];

        // Caution: Do not edit this line.
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.
        // $this->session = service('session');
    }
}
