<?php

declare(strict_types=1);

namespace venndev\restapi\controllers\chitietnoidungnhap;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Throwable;
use venndev\restapi\provider\database\ResultQuery;
use venndev\restapi\provider\Provider;
use venndev\restapi\utils\attributes\RequestMapping;
use venndev\restapi\utils\HttpMethod;

#[RequestMapping(["value" => "/chitietnoidungnhap"])]
final class TbChiTietNoiDungNhapController
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
        $result = Provider::getMySQL()->execute("SELECT * FROM `tbchitietnoidungnhap` ");
    
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

        Provider::getMySQL()->execute("SELECT * FROM `tbchitietnoidungnhap` WHERE `id` = ':a';",
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
            Provider::getMySQL()->execute("DELETE FROM `tbchitietnoidungnhap` WHERE `id` = ':a';",
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
        "value" => "/edit/{id}/{MaHangHoa}/{MaPhieuNhap}/{ThanhTien}/{SLNhap}/{GiamGia}/{createAt}/{updateAt}",
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
        $mahanghoa = $args["MaHangHoa"];
        $maphieunhap = $args["MaPhieuNhap"];
        $thanhtien = $args["ThanhTien"];
        $slnhap = $args["SLNhap"];
        $giamgia = $args["GiamGia"];
        $createat = $args["createAT"];
        $updateat = $args["updateAt"];

        if (!is_string($id)) {
            $response->getBody()->write(json_encode(["message" => base64_encode("id phải là chuỗi")]));
            return $response->withStatus(400);
        }
        
        Provider::getMySQL()->execute(
            "UPDATE `tbloaiphong` SET `MaHangHoa` = ':b', `MaPhieuNhap` = ':c', `ThanhTien` = :d, `SLNhap` = :e, `GiamGia` = :f, `createAT` = ':g', `updateAt` = ':h' WHERE `id` = ':a';", 
            [
                "a" => $id,
                "b" => $mahanghoa,
                "c" => $maphieunhap,
                "d" => $thanhtien,
                "e" => $slnhap,
                "f" => $giamgia,
                "g" => $createat,
                "h" => $updateat,
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
        $mahanghoa = $args["MaHangHoa"];
        $maphieunhap = $args["MaPhieuNhap"];
        $thanhtien = $args["ThanhTien"];
        $slnhap = $args["SLNhap"];
        $giamgia = $args["GiamGia"];
        $createat = $args["createAT"];
        $updateat = $args["updateAt"];
    
        if (!is_string($id)) {
            $response->getBody()->write(json_encode(["message" => base64_encode("id phải là chuỗi")]));
            return $response->withStatus(400);
        }
        
        Provider::getMySQL()->execute(
            "INSERT INTO `tbloaiphongphong` (id, MaHangHoa , MaPhieuNhap, ThanhTien, SLNhap, GiamGia, createAT, updateAt) VALUES (':a', ':b', ':c', :d, :e, :f, ':g', ':h')", 
            [
                "a" => $id,
                "b" => $mahanghoa,
                "c" => $maphieunhap,
                "d" => $thanhtien,
                "e" => $slnhap,
                "f" => $giamgia,
                "g" => $createat,
                "h" => $updateat,
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
        $result = Provider::getMySQL()->execute("SELECT :a FROM `tbchitietnoidungnhap`",
            [
                "a" => $tencot
            ]);

        $result->then(function(ResultQuery $result) use ($response) {
            $response->getBody()->write(json_encode($result->getResult()));
        });

        return $response;
    }

}
