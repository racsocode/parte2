<?php
// Routes  $app->get('/[{name}]', function ($request, $response, $args) use ($app) {



$app->get('/[{email}]', function ($request, $response, $args) use ($app) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");

    $data = file_get_contents("data/employees.json");
    $employees = json_decode($data, true);

    if (!empty($_GET["email"])) {
        $data = array();
        foreach ($employees as $employee) {
            if ($employee['email'] == $_GET["email"]) {
                $data = $employee;
                break;
            }
        }

        $employees = array();
        if (!empty($data)) {
            $employees = array($data);
        }
    }

    return $this->renderer->render($response, 'index.phtml', [ 'employees' => $employees]);
});


$app->get('/detail/[{id}]', function ($request, $response, $args) use ($app) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");

    $data = file_get_contents("data/employees.json");
    $employees = json_decode($data, true);

    $data = array();
    foreach ($employees as $employee) {
        if ($employee['id'] == $args['id']) {
            $data = $employee;
            break;
        }
    }

    return $this->renderer->render($response, 'detail.phtml', [ 'colecciones' => $data]);
});


$app->get('/api/v1/employees[/{min:[0-9]+}[/{max:[0-9]+}]]', function ($request, $response, $args) use ($app) {

    $this->logger->info("Slim-Skeleton '/' route");

    $data = file_get_contents("data/employees.json");
    $employees = json_decode($data, true);
    $xml = new SimpleXMLElement('<employees/>');

    if (!empty( $args['min']) && !empty( $args['max'])) {
        if ($args['max'] > $args['min']) {

            foreach ($employees as $employee) {
                $salary = $employee['salary'];
                $subcadena = ".";
                $posicionsubcadena = strpos ($salary, $subcadena);
                $dominio = substr ($salary, ($posicionsubcadena+1));
                $vowels = array("$", ".", ",", $dominio);
                $salary = intval(str_replace($vowels, "", $employee['salary']));
                $min = intval($args['min']);
                $max = intval($args['max']);

                if ($min <= $salary && $salary <= $max ) {
                    $item = $xml->addChild('employee');
                    $item->addChild('id', $employee['id']);
                    $item->addChild('name', $employee['name']);
                    $item->addChild('phone', $employee['phone']);
                    $item->addChild('address', $employee['address']);
                    $item->addChild('position', $employee['position']);
                    $item->addChild('salary', $employee['salary']);
                    $skills = $item->addChild('skills');

                    foreach($employee['skills'] as $skill) {
                        $skills->addChild('skill', $skill ["skill"]);
                    }
                }
            }

        } else {
            $error = $xml->addChild('error');
            $error->addChild('mensaje', 'El parametro minimo no debe exceder al mayor');
        }
    } else {
        foreach ($employees as $employee) {
            $item = $xml->addChild('employee');
            $item->addChild('id', $employee['id']);
            $item->addChild('name', $employee['name']);
            $item->addChild('phone', $employee['phone']);
            $item->addChild('address', $employee['address']);
            $item->addChild('position', $employee['position']);
            $item->addChild('salary', $employee['salary']);
            $skills = $item->addChild('skills');

            foreach($employee['skills'] as $skill) {
                $skills->addChild('skill', $skill ["skill"]);
            }
        }
    }

    header("Content-type: text/xml");
    echo $xml->asXml(); exit();
});