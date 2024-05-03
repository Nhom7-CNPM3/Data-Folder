<?php

declare(strict_types=1);

namespace venndev\restapi\controllers\loaiphong;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Throwable;
use venndev\restapi\provider\database\ResultQuery;
use venndev\restapi\provider\Provider;
use venndev\restapi\utils\attributes\RequestMapping;
use venndev\restapi\utils\HttpMethod;

#[RequestMapping(["value" => "/loaiphong"])]
final class TbLoaiPhongController
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
        $result = Provider::getMySQL()->execute("SELECT * FROM `tbloaiphong` ");
    
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

        Provider::getMySQL()->execute("SELECT * FROM `tbloaiphong` WHERE `id` = ':a';",
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
            Provider::getMySQL()->execute("DELETE FROM `tbloaiphong` WHERE `id` = ':a';",
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
        "value" => "/edit/{id}/{MaKhachSan}/{TenLoaiPhong}/{GiaTheoGio}/{GiaTheoNgay}",
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
        $makhachsan = $args["MakhachSan"];
        $tenkhachsan = $args["TenKhachSan"];
        $giatheogio = $args["GiaTheoGio"];
        $giatheongay = $args["GiaTheoNgay"];

        if (!is_string($id)) {
            $response->getBody()->write(json_encode(["message" => base64_encode("id phải là chuỗi")]));
            return $response->withStatus(400);
        }
        
        Provider::getMySQL()->execute(
            "UPDATE `tbloaiphong` SET `MakhachSan` = ':b', `TenKhachSan` = ':c', `GiaTheoGio` = ':d', `GiaTheoNgay` = ':e' WHERE `id` = ':a';", 
            [
                "a" => $id,
                "b" => $makhachsan,
                "c" => $tenkhachsan,
                "d" => $giatheogio,
                "e" => $giatheongay,
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
        "value" => "/add/{id}/{MaKhachSan}/{TenLoaiPhong}/{GiaTheoGio}/{GiaTheoNgay}",
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
        $makhachsan = $args["MakhachSan"];
        $tenkhachsan = $args["TenKhachSan"];
        $giatheogio = $args["GiaTheoGio"];
        $giatheongay = $args["GiaTheoNgay"];
    
        if (!is_string($id)) {
            $response->getBody()->write(json_encode(["message" => base64_encode("id phải là chuỗi")]));
            return $response->withStatus(400);
        }
        
        Provider::getMySQL()->execute(
            "INSERT INTO `tbloaiphongphong` (id, MaKhachSan , TenKhachSan, GiaTheoGio, GiaTheoNgay) VALUES (':a', ':b', ':c', ':d', ':e')", 
            [
                "a" => $id,
                "b" => $makhachsan,
                "c" => $tenkhachsan,
                "d" => $giatheogio,
                "e" => $giatheongay,
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
        $result = Provider::getMySQL()->execute("SELECT :a FROM `tbloaiphong`",
            [
                "a" => $tencot
            ]);

        $result->then(function(ResultQuery $result) use ($response) {
            $response->getBody()->write(json_encode($result->getResult()));
        });

        return $response;
    }

}
