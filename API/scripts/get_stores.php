<?php
#get_stores.php
require __DIR__ . '/../bootstrap.php';

use Entity\Stores;
use Entity\Employees;
use Entity\Stocks;

$storesRepository = $entityManager->getRepository(Stores::Class);
$getStores = $storesRepository->findAll();

echo "Tout les magasins :\n";
foreach ($getStores as $store) {
    echo $store->getStoreName()."\n";
}

echo "Tout les employés de chaques magasins:\n";
foreach ($getStores as $store) {
    echo "Les employes de ".$store->getStoreName()."\n";
    if($store->getEmployees()->isEmpty()){
        echo "  - Aucun employé n'as été enregistré\n";
    }
    foreach ($store->getEmployees() as $employee) {
        echo "  -".$employee->getEmployeesName()."\n";
    }
}

?>