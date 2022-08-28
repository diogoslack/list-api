<?php
namespace App\Controller;

use App\Builder\Product\Director;
use App\Builder\Product\ProductBuilder;
use App\Builder\Product\ProductNoStorageBuilder;
use App\Entity\Product;
use App\Helper\RamHelper;
use App\Helper\StorageHelper;
use Doctrine\Persistence\ManagerRegistry;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    const HARD_DISK_FILTER_TYPES = ['SAS', 'SATA', 'SSD'];    

    /**
     * Get the most recent product list 
     * 
     * Return a list of products of the most recent version,
     * based on the applied filters (location, storage, ram, harddisktype)
     */
    #[Route('/product', name: 'app_product_list', methods: ['GET'])]
    public function list(Request $request, ManagerRegistry $doctrine): JsonResponse
    {
        $filterLocation = $request->query->get('location');
        $filterStorage = $request->query->get('storage');
        $filterRam = $request->query->get('ram');
        $filterHardDiskType = $request->query->get('harddisktype');

        $version = $doctrine->getRepository(Product::class)->getLastProductVersion();

        if (!$version || !$version[1]) {
            return new JsonResponse([], Response::HTTP_OK);
        } else {
            $version = new \DateTime($version[1]);
        }

        if ($filterStorage) {
            $filterStorage = strtoupper($filterStorage);
            list($min, $max) = explode(',', $filterStorage);
            $minStorage = StorageHelper::teraToGiga($min);
            $maxStorage = StorageHelper::teraToGiga($max);
        } else {
            $minStorage = null;
            $maxStorage = null;
        }

        if ($filterRam) {
            $ram = [];
            $filterRam = strtoupper($filterRam);
            $ramList = explode(',', $filterRam);
            foreach($ramList as $ramItem) {
                $item = RamHelper::teraToGiga($ramItem);
                array_push($ram, $item);
            }
        } else {
            $ram = null;
        }

        if ($filterHardDiskType) {
            $filterHardDiskType = strtoupper($filterHardDiskType);
            $hardDiskType = in_array($filterHardDiskType, self::HARD_DISK_FILTER_TYPES) ? $filterHardDiskType : null;
        } else {
            $hardDiskType = null;
        }
        
        try {
            $result = $doctrine->getRepository(Product::class)->getAllProductsFiltered(
                $version,
                $filterLocation,
                $minStorage,
                $maxStorage,
                $ram,
                $hardDiskType
            );
    
            return new JsonResponse($result, Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }        
    }

    /**
     * Get the location list 
     * 
     * Return a list of locations for that version
     */
    #[Route('/product/location', name: 'app_product_location_list', methods: ['GET'])]
    public function location(Request $request, ManagerRegistry $doctrine): JsonResponse
    {
        $version = $doctrine->getRepository(Product::class)->getLastProductVersion();

        if (!$version || !$version[1]) {
            return new JsonResponse([], Response::HTTP_OK);  
        } else {
            $version = new \DateTime($version[1]);
        }        

        try {
            $result = $doctrine->getRepository(Product::class)->getAllProductsLocations($version);
            return new JsonResponse($result, Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Import a product list from a excel file
     * 
     * Get a list of products from a excel file and populate the database
     * Since the list doesn't have a product identifier (i.e: SKU), all 
     * products are inserted but controlled by a version
     */
    #[Route('/product', name: 'app_product_upload', methods: ['POST'])]
    public function upload(Request $request, ManagerRegistry $doctrine): JsonResponse
    {
        // The file must be sent within the 'products' field
        $file = $request->files->get('products');
        $fileFolder = __DIR__ . '/../../public/uploads/';
        $filePathName = md5(uniqid()) . $file->getClientOriginalName();

        try {
            $file->move($fileFolder, $filePathName);
        } catch (FileException $e) {
            return new JsonResponse(['error' => 'Impossible to move the file'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // Load the file 
        try {
            $spreadsheet = IOFactory::load($fileFolder . $filePathName);
            $data = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
        } catch (Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
        
        $entityManager = $doctrine->getManager();

        $line = 0;
        $version = new \DateTime();
        $success = 0;
        $failed = 0;
        $errorRows = [];

        foreach ($data as $row) {
            if ($line === 0 && $row['A'] === 'Model') {
                $line++;
                continue;
            }

            $fileModel = trim($row['A']);
            $fileRam = trim($row['B']);
            $fileStorage = trim($row['C']);
            $fileLocation = trim($row['D']);
            $filePrice = trim($row['E']);

            if (!$fileModel || !$fileRam || !$fileLocation || !$filePrice ) {
                continue;
            }

            try {
                $builder = $fileStorage ? new ProductBuilder($doctrine) : new ProductNoStorageBuilder($doctrine);
                $product = (new Director())->build($builder, $version, $filePrice, $fileModel, $fileLocation, $fileRam, $fileStorage);
                $entityManager->persist($product);
                $entityManager->flush();
                $success++;
                unset($builder, $product);
            } catch (Exception $e) {
                array_push($errorRows, $line);
                $failed++;
            }            

            $line++;
        }

        $result = [
            'Version' => $version->format('Y-m-d H:i:s'),
            'Success' => $success,
            'Failed' => $failed,
            'Failed rows' => join(',', $errorRows)
        ];
        return new JsonResponse($result, Response::HTTP_OK);
    }

    /**
     * GET root (/)
     * 
     * Return the current version
     */
    #[Route('/', name: 'app_home_page', methods: ['GET'])]
    public function index(ManagerRegistry $doctrine): JsonResponse
    {
        $version = $doctrine->getRepository(Product::class)->getLastProductVersion();

        if (!$version || !$version[1]) {
            return new JsonResponse([], Response::HTTP_OK);  
        } else {
            $version = new \DateTime($version[1]);
        }     
        return new JsonResponse(['version' => $version->format('Y-m-d H:i:s')], Response::HTTP_OK);
    }
}