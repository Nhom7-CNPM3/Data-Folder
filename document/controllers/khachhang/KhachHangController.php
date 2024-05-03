<?php

declare(strict_types=1);

namespace venndev\restapi\controllers\Khachhang;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Throwable;
use venndev\restapi\provider\database\ResultQuery;
use venndev\restapi\provider\Provider;
use venndev\restapi\utils\attributes\RequestMapping;
use venndev\restapi\utils\HttpMethod;

#[RequestMapping(["value" => "/khachhang"])]
final class KhachHangController
{

    private const TABLE_NAME = "tbkhachhang";

    private function idExist(Request $request, Response $response, array $args) : bool
    {
        if (!isset($args["id"])) {
            $response->getBody()->write(json_encode(["message" => "Id is required"]));
            return false;
        }

        return true;
    }

    /**
     * @throws Throwable
     */
    #[RequestMapping([
        "value" => "/get_table",
        "method" => HttpMethod::GET,
        "produces" => ["application/json"]
        
    ])]
    public function getTable(Request $request, Response $response, array $args) : Response
    {
        // if (!isset($args["id"])) {
        //     $response->getBody()->write(json_encode(["message" => "Id is required"]));
        //     return $response->withStatus(400);
        // }

        // $id = $args["id"];

        // if (!is_numeric($id)) {
        //     $response->getBody()->write(json_encode(["message" => "Id must be a number"]));
        //     return $response->withStatus(400);
        // }

        Provider::getMySQL()->execute("SELECT * FROM " . self::TABLE_NAME)
            ->then(function(ResultQuery $result) use ($response) {
            $result = [
                "status" => $result->getStatus(),
                "result" => $result->getResult()
            ];

            $response->getBody()->write(json_encode($result));
        });
        
        return $response;
    }

    /**
     * @throws Throwable
     */
    #[RequestMapping([
        "value" => "/search/{id}",
        "method" => HttpMethod::GET,
        "produces" => ["application/json"]
    ])]

    public function search(Request $request, Response $response, array $args, array $queryParams) : Response
    {
        
        $id = $args["id"];

        Provider::getMySQL()->execute("SELECT * FROM `tbKhachHang` WHERE `id` = ':a';",
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

    /**
     * @throws Throwable
     */
    #[RequestMapping([
        "value" => "/edit/{id}/{TenKH}/{SDTKH}/{EmailKH}/{TaiKhoanKH}/{MatKhauMaHoaKH}",
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
        $TenKH = $args["TenKH"];
        $SDTKH = $args["SDTKH"];
        $EmailKH = $args["EmailKH"];
        $TaiKhoanKH = $args["TaiKhoanKH"];
        $MatKhauMaHoaKH = $args["MatKhauMaHoaKH"];
        

        if (!is_string($id)) {
            $response->getBody()->write(json_encode(["message" => base64_encode("id phải là chuỗi")]));
            return $response->withStatus(400);
        }
        
        Provider::getMySQL()->execute(
            "UPDATE `id` SET `TenKH` = ':b', `SDTKH` = ':c', `EmailKH` = :d, `TaiKhoanKH` = :e, `MatKhauMaHoaKH` = :f WHERE `id` = ':a';", 
            [
                "a" => $id,
                "b" => $TenKH,
                "c" => $SDTKH,
                "d" => $EmailKH,
                "e" => $TaiKhoanKH,
                "f" => $MatKhauMaHoaKH
              
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

    /**
     * @throws Throwable
     */
    #[RequestMapping([
        "value" => "/add/{id}/{TenKH}/{SDTKH}/{EmailKH}/{TaiKhoanKH}/{MatKhauMaHoaKH}",
        "method" => HttpMethod::GET,
        "produces" => ["application/json"]
    ])]
    public function add(Request $request, Response $response, array $args) : Response
    {
        if (!isset($args["id"])) {
            $response->getBody()->write(json_encode(["message" => base64_encode("yêu cầu cần id")]));
            return $response->withStatus(400);
        }

        $id = $args["id"];
        $TenKH = $args["TenKH"];
        $SDTKH = $args["SDTKH"];
        $EmailKH = $args["EmailKH"];
        $TaiKhoanKH = $args["TaiKhoanKH"];
        $MatKhauMaHoaKH = $args["MatKhauMaHoaKH"];

        if (!is_string($id)) {
            $response->getBody()->write(json_encode(["message" => base64_encode("id phải là chuỗi")]));
            return $response->withStatus(400);
        }
        
        Provider::getMySQL()->execute(
            "INSERT INTO `tbKhachHang` (id, TenKH, SDTKH, EmailKH, TaiKhoanKH, MatKhauMaHoaKH) VALUES (':a', ':b', ':c', ':d', ':e', ':f', ':g')", 
            [
                "a" => $id,
                "b" => $TenKH,
                "c" => $SDTKH,
                "d" => $EmailKH,
                "e" => $TaiKhoanKH,
                "f" => $MatKhauMaHoaKH
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
        "value" => "/delete/{id}",
        "method" => HttpMethod::DELETE,
        "produces" => ["application/json"]
    ])]

    public function delete(Request $request, Response $response, array $args) : Response
    {
        
        $id= $args["id"];
        try
        {
            Provider::getMySQL()->execute("DELETE FROM `tbKhachHang` WHERE `id` = ':a';",
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
        "value" => "/select/{TenCot}",
        "method" => HttpMethod::POST,
        "produces" => ["application/json"]
    ])]
    public function select(Request $request, Response $response, array $args, array $queryParams) : Response
    {
        $tencot = $args["TenCot"];
        $result = Provider::getMySQL()->execute("SELECT :a FROM `tbKhachHang`",
            [
                "a" => $tencot
            ]);

        $result->then(function(ResultQuery $result) use ($response) {
            $response->getBody()->write(json_encode($result->getResult()));
        });

        return $response;
    }
    
}