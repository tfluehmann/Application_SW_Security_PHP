<?php

$customers = json_decode(file_get_contents("customers.json"),true);

$uri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($uri)['path'];
$paths = explode('/', $path);
for ($i = 1; $i <= 2; $i ++)
    array_shift($paths);
$p1 = array_shift($paths);
$p2 = array_shift($paths);

if ($p1 == 'customer') {
    if (isset($p2)) {
        switch ($method) {
            case 'PUT':
                create_customer($p2);
                break;
            
            case 'DELETE':
                delete_customer($p2);
                break;
            
            case 'GET':
                display_customer($p2);
                break;
            
            default:
                header('HTTP/1.1 405 Method Not Allowed');
                header('Allow: GET, PUT, DELETE');
                break;
        }
    } else {
        switch ($method) {
            case 'GET':
                header('Content-type: application/json');
                echo json_encode($customers);
                break;
            default:
                header('HTTP/1.1 405 Method Not Allowed');
                header('Allow: GET');
                break;
        }
    }
} else {
    header('HTTP/1.1 404 Not Found');
}

function create_customer($name)
{
    global $customers;
    if (isset($customers[$name])) {
        header('HTTP/1.1 409 Conflict');
        return;
    }
    $data = json_decode(file_get_contents('php://input'));
    if (is_null($data)) {
        header('HTTP/1.1 400 Bad Request');
        return;
    }
    $customers[$name] = $data;
    file_put_contents("customers.json",json_encode($customers));
    echo "customer created\n";
}

function delete_customer($name)
{
    global $customers;
    if (isset($customers[$name])) {
        unset($customers[$name]);
        file_put_contents("customers.json",json_encode($customers));
        echo "customer deleted\n";
    } else {
        header('HTTP/1.1 404 Not Found');
        echo "customer not found\n";
    }
}

function display_customer($name)
{
    global $customers;
    if (array_key_exists($name, $customers)) {
    header('Content-type: application/json');
    echo json_encode($customers[$name]);
    } else {
        header('HTTP/1.1 404 Not Found');
    }
}

 
