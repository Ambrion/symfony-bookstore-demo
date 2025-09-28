<?php

namespace App\Admin\Main;

use App\Book\Entity\Book;
use App\Category\Entity\Category;
use App\Contact\Entity\Contact;
use App\Settings\Entity\Settings;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use League\CommonMark\CommonMarkConverter;
use Symfony\Component\HttpFoundation\Response;

#[AdminDashboard(routePath: '/admin/dashboard', routeName: 'app-admin-dashboard-index')]
class AdminMainDashboardController extends AbstractDashboardController
{
    public function index(): Response
    {
        $taskFilePath = $this->getParameter('kernel.project_dir').'/TASK_README.md';
        if (!file_exists($taskFilePath)) {
            throw $this->createNotFoundException('Файл TASK_README.md не найден.');
        }

        $resultFilePath = $this->getParameter('kernel.project_dir').'/RESULT_README.md';
        if (!file_exists($resultFilePath)) {
            throw $this->createNotFoundException('Файл RESULT_README.md не найден.');
        }

        $markdownTaskContent = file_get_contents($taskFilePath);
        $markdownResultContent = file_get_contents($resultFilePath);

        $converter = new CommonMarkConverter();
        $htmlContentTask = $converter->convert($markdownTaskContent);
        $htmlContentResult = $converter->convert($markdownResultContent);

        return $this->render('/admin/dashboard/index.html.twig', [
            'htmlContentResult' => $htmlContentResult,
            'htmlContentTask' => $htmlContentTask,
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Панель администратора');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Сводная', 'fa fa-dashboard');
        yield MenuItem::linkToCrud('Категории', 'fa fa-bars', Category::class);
        yield MenuItem::linkToCrud('Книги', 'fa fa-book', Book::class);
        yield MenuItem::linkToCrud('Обратная связь', 'fa fa-message', Contact::class);
        yield MenuItem::linkToCrud('Настройки', 'fa fa-gears', Settings::class);
        yield MenuItem::linkToUrl('Посмотреть сайт', 'fa fa-home', $this->generateUrl('app_home'));
    }
}
