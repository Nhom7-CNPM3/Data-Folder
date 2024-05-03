<?php

declare(strict_types=1);

namespace venndev\restapi\controllers\subuser;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Throwable;
use venndev\restapi\provider\database\ResultQuery;
use venndev\restapi\provider\Provider;
use venndev\restapi\utils\attributes\RequestMapping;
use venndev\restapi\utils\HttpMethod;

#[RequestMapping(["value" => "/subuser"])]
final class subuser
{

    /**
     * @throws Throwable
     */
    #[RequestMapping([
        "value" => "/get",
        "method" => HttpMethod::GET,
        "produces" => ["application/json"],
      ])]
      public function get(Request $request, Response $response): Response
      {
        $result = Provider::getMySQL()->execute("SELECT * FROM `subuser` ");
    
        $result->then(function (ResultQuery $result) use ($response) {
          $response->getBody()->write(json_encode($result->getResult()));
        });
    
        return $response;
      }
      
    #[RequestMapping([
        "value" => "/search/{id}",
        "method" => HttpMethod::GET,
        "produces" => ["application/json"]
    ])]

    public function search(Request $request, Response $response, array $args, array $queryParams) : Response
    {
        
        $id = $args["id"];

        Provider::getMySQL()->execute("SELECT * FROM `subuser` WHERE `id` = ':a';",
        [
            "a"=> $id
        ])
       
        ->then(function(ResultQuery $result) use ($response)
        {
            $data = $result->getResult();
            if (empty($data)) 
            {
                $response->getBody()->write(json_encode(["message" => base64_encode("Tìm không thấy hoặc do nhập sai thông tin cần tìm ")]));
                return $response->withStatus(404);
            } else 
                {
                    $response->getBody()->write(json_encode
                    ([
                        "status" => $result->getStatus(),
                        "result" => $data
                    ]));
                    return $response->withStatus(200);
                }
        });

        return $response->withStatus(200);
    }

    #[RequestMapping([
        "value" => "/delete/{id}",
        "method" => HttpMethod::DELETE,
        "produces" => ["application/json"]
    ])]

    public function delete(Request $request, Response $response, array $args) : Response
    {
        
        $id = $args["id"];
        try
        {
            Provider::getMySQL()->execute("DELETE FROM `subuser` WHERE `id` = ':a';",
            [
                "a"=> $id
            ])

            ->then(function(ResultQuery $result) use ($response)
            {
            $data = $result->getResult();
            if (empty($data)) 
            {
                $response->getBody()->write(json_encode(["message" => base64_encode("Tìm không thấy hoặc do nhập sai thông tin cần tìm")]));
                return $response->withStatus(404);
            } else 
                {
                    $response->getBody()->write(json_encode([
                        "message" => base64_encode("Xoá thành công"),
                        "status" => $result->getStatus(),
                        "result" => $data
                    ]));
                    return $response->withStatus(200);
                }
            });

                return $response->withStatus(200);
        }
        catch(\Exception $error)
        {
            $response->getBody()->write(json_encode(["message" => base64_encode("Không thể xoá do liên quan đến các mối quan hệ")]));
            return $response->withStatus(400);
        }
    }
    
    #[RequestMapping([
        "value" => "/edit/{id}/{MaKhachSan}/{name}/{phone_number}/{name/{address}/{password}/{email}/{createdAT}/{updateAT}/{role}",
        "method" => HttpMethod::POST,
        "produces" => ["application/json"],

    ])]
    public function edit(Request $request, Response $response, array $args) : Response
    {
        if (!isset($args["id"])) {
            $response->getBody()->write(json_encode(["message" => base64_encode("yêu cầu cần id")]));
            return $response->withStatus(400);
        }

        $id = $args["id"];
        $makhachsan = $args["MaKhachSan"];
        $name = $args["name"];
        $phone_number = $args["phone_number"];
        $address = $args["address"];
        $password = $args["password"];
        $email = $args["email"];
        $createdat = $args["createdAT"];
        $updateat = $args["updateAT"];
        $role = $args["role"];

        if (!is_string($id)) {
            $response->getBody()->write(json_encode(["message" => base64_encode("id phải là chuỗi")]));
            return $response->withStatus(400);
        }
        
        Provider::getMySQL()->execute(
            "UPDATE `user` SET `MaKhachSan` = ':b' `name` = ':c', `phone_number` = :d, `address` = ':e', `password` = ':f', `email` = ':g', `createdAT` = ':h', `updatedAT` = ':i', `role` = ':l' WHERE `id` = ':a';", 
            [
                "a" => $id,
                "b" => $makhachsan,
                "c" => $name,
                "d" => $phone_number,
                "e" => $address,
                "f" => $password,
                "g" => $email,
                "h" => $createdat,
                "i" => $updateat,
                "l" => $role
            ])
        ->then(function(ResultQuery $result) use ($response) {
            $result = [
                "status" => $result->getStatus(),
                "result" => $result->getResult()
            ];

            $response->getBody()->write(json_encode($result));
        });

        return $response;
    }

    #[RequestMapping([
        "value" => "/add/{id}/{MaKhachSan}/{name}/{phone_number}/{address}/{password}/{email}/{createddAT}/{updatedAT}/{role}",
        "method" => HttpMethod::POST,
        "produces" => ["application/json"],

    ])]
    public function add(Request $request, Response $response, array $args) : Response
    {
        if (!isset($args["id"])) {
            $response->getBody()->write(json_encode(["message" => base64_encode("yêu cầu cần id")]));
            return $response->withStatus(400);
        }

        $id = $args["id"];
        $makhachsan = $args["MaKhachSan"];
        $name = $args["name"];
        $phone_number = $args["phone_number"];
        $address = $args["address"];
        $password = $args["password"];
        $email = $args["email"];
        $createdat = $args["createdAT"];
        $updateat = $args["updateAT"];
        $role = $args["role"];
        if (!is_string($id)) {
            $response->getBody()->write(json_encode(["message" => base64_encode("id phải là chuỗi")]));
            return $response->withStatus(400);
        }
        
        Provider::getMySQL()->execute(
            "INSERT INTO `user` (id, MaKhachSan , name , phone_number, address, password, email, createdAT, updatedAT, role) VALUES (':a',':b' ,':c', :d, ':e', ':f', ':g', ':h', ':i', ':l')", 
            [
                "a" => $id,
                "b" => $makhachsan,
                "c" => $name,
                "d" => $phone_number,
                "e" => $address,
                "f" => $password,
                "g" => $email,
                "h" => $createdat,
                "i" => $updateat,
                "l" => $role
            ])
        ->then(function(ResultQuery $result) use ($response) {
            $result = [
                "status" => $result->getStatus(),
                "result" => $result->getResult()
            ];

            $response->getBody()->write(json_encode($result));
        });

        return $response;
    }

    #[RequestMapping([
        "value" => "/select/{TenCot}",
        "method" => HttpMethod::POST,
        "produces" => ["application/json"]
    ])]
    public function select(Request $request, Response $response, array $args, array $queryParams) : Response
    {
        $tencot = $args["TenCot"];
        $result = Provider::getMySQL()->execute("SELECT :a FROM `subuser`",
            [
                "a" => $tencot
            ]);

        $result->then(function(ResultQuery $result) use ($response) {
            $response->getBody()->write(json_encode($result->getResult()));
        });

        return $response;
    }

}
