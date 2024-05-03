<?php

declare(strict_types=1);

namespace venndev\restapi\controllers\dichvu;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Throwable;
use venndev\restapi\provider\database\ResultQuery;
use venndev\restapi\provider\Provider;
use venndev\restapi\utils\attributes\RequestMapping;
use venndev\restapi\utils\HttpMethod;

#[RequestMapping(["value" => "/tbdichvu"])]
final class DichVuController
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
        $result = Provider::getMySQL()->execute("SELECT * FROM `tbdichvu` ");
    
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

        Provider::getMySQL()->execute("SELECT * FROM `tbdichvu` WHERE `id` = ':a';",
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
            Provider::getMySQL()->execute("DELETE FROM `tbdichvu` WHERE `id` = ':a';",
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
        "value" => "/edit/{id}/{MaNhomDichVu}/{TenDichVu}/{DonViTinh}/{GiaVon}/{GiaBan}/{ThoiLuong}",
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
        $manhomdichvu = $args["MaNhomDichVu"];
        $tendichvu = $args["TenDichVu"];
        $donvitinh = $args["DonViTinh"];
        $giavon = $args["GiaVon"];
        $giaban = $args["GiaBan"];
        $thoiluong = $args["ThoiLuong"];

        if (!is_string($id)) {
            $response->getBody()->write(json_encode(["message" => base64_encode("id phải là chuỗi")]));
            return $response->withStatus(400);
        }
        
        Provider::getMySQL()->execute(
            "UPDATE `tbdichvu` SET `MaNhomDichVu` = ':b', `TenDichVu` = ':c', `DonViTinh` = ':d', `GiaVon` = :e, `GiaBan` = :f, `ThoiLuong` = :g WHERE `id` = ':a';", 
            [
                "a" => $id,
                "b" => $manhomdichvu,
                "c" => $tendichvu,
                "d" => $donvitinh,
                "e" => $giavon,
                "f" => $giaban,
                "g" => $thoiluong
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
        "value" => "/add/{id}/{MaNhomDichVu}/{TenDichVu}/{DonViTinh}/{GiaVon}/{GiaBan}/{ThoiLuong}",
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
        $manhomdichvu = $args["MaNhomDichVu"];
        $tendichvu = $args["TenDichVu"];
        $donvitinh = $args["DonViTinh"];
        $giavon = $args["GiaVon"];
        $giaban = $args["GiaBan"];
        $thoiluong = $args["ThoiLuong"];

        if (!is_string($id)) {
            $response->getBody()->write(json_encode(["message" => base64_encode("id phải là chuỗi")]));
            return $response->withStatus(400);
        }
        
        Provider::getMySQL()->execute(
            "INSERT INTO `tbdichvu` (id, MaNhomDichVu, TenDichVu, DonViTinh, GiaVon, GiaBan, ThoiLuong) VALUES (':a', ':b', ':c', ':d', :e, :f, :g)", 
            [
                "a" => $id,
                "b" => $manhomdichvu,
                "c" => $tendichvu,
                "d" => $donvitinh,
                "e" => $giavon,
                "f" => $giaban,
                "g" => $thoiluong
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
        $result = Provider::getMySQL()->execute("SELECT :a FROM `tbdichvu`",
            [
                "a" => $tencot
            ]);

        $result->then(function(ResultQuery $result) use ($response) {
            $response->getBody()->write(json_encode($result->getResult()));
        });

        return $response;
    }

}