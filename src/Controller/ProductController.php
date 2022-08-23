<?php
namespace App\Controller;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    #[Route('/product', name: 'app_product_list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        return $this->json(['products' => 'soon...']);
    }

    #[Route('/product', name: 'app_product_upload', methods: ['POST'])]
    public function upload(Request $request): JsonResponse
    {
        $file = $request->files->get('products');
        $fileFolder = __DIR__ . '/../../public/uploads/';
        $filePathName = md5(uniqid()) . $file->getClientOriginalName();

        try {
            $file->move($fileFolder, $filePathName);
        } catch (FileException $e) {
            return new JsonResponse(['error' => 'Impossible to move the file'], 400);
        }

        try {
            $spreadsheet = IOFactory::load($fileFolder . $filePathName);
            $data = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
        } catch (Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }

        $list = [];
        foreach ($data as $row)
        {
            $model = $row['A'];                        
            $ram = $row['B'];
            $storage = $row['C'];
            $location = $row['D'];
            $price = $row['E'];

            if ($model === 'Model') {
                continue;
            }

            if (!$model || !$ram || !$location || !$price ) {
                continue;
            }
            array_push($list, [$model, $ram, $storage, $location, $price]);
        }
        return $this->json(['list' => $list]);
        
    }
}