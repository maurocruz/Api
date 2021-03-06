<?php
namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;
use Plinct\Tool\ArrayTool;
use Plinct\Tool\Image\Image;

class ImageObject extends Entity implements TypeInterface {
    protected $table = "imageObject";
    protected $type = "ImageObject";
    protected $properties = [ "*" ];
    protected $hasTypes = [ "author" => "Person" ];

    public function get(array $params): array {
        // vars
        $thumbnail = $params['thumbnail'] ?? null;
        $format = $params['format'] ?? null;
        if ($thumbnail == "on") $params['properties'] = "*";
        unset($params['thumbnail']);
        // GET
        $data = parent::get($params);
        // THUMBNAIL ON
        if ($thumbnail=='on') {
            $itemList = $data['itemListElement'] ?? $data;
            foreach ($itemList as $key => $value) {
                $item = $format ? $value['item'] : $value;
                if (!$item['thumbnail']) {
                    $image = new Image($item['contentUrl']);
                    $image->thumbnail("200");
                    $contentSize = $image->getFileSize();
                    $width = $image->getWidth();
                    $height = $image->getHeight();
                    $encodingFormat = $image->getEncodingFormat();
                    $thumbnailData = $image->getThumbSrc();
                    $data['itemListElement'][$key]['item']['contentSize'] = $contentSize;
                    $data['itemListElement'][$key]['item']['width'] = $width;
                    $data['itemListElement'][$key]['item']['height'] = $height;
                    $data['itemListElement'][$key]['item']['encodingFormat'] = $encodingFormat;
                    $data['itemListElement'][$key]['item']['thumbnail'] = $thumbnailData;
                    // save data
                    $id = ArrayTool::searchByValue($item['identifier'], "id")['value'];
                    $newParams = [ "id" => $id, "contentSize" => $contentSize, "width" => $width, "height" => $height, "encodingFormat" => $encodingFormat, "thumbnail" => $thumbnailData ];
                    parent::put($newParams);
                }
            }
        }
        return $data;
    }

    public function put(array $params): array {
        unset($params['contentUrl']);
        return parent::put($params);
    }

    public function createSqlTable($type = null): array {
        $message[] =  parent::createSqlTable("ImageObject");
        return $message;
    }    
    
    public static function getRepresentativeImageOfPage($data, $mode = "string") {
        if ($data) {
            foreach ($data as $valueImage) {
                if (isset($valueImage['representativeOfPage']) && $valueImage['representativeOfPage'] == true) {
                    return $mode == "string" ? $valueImage['contentUrl'] : $valueImage;
                }
            }
            return $mode == "string" ? $data[0]['contentUrl'] : $data[0];
        }
        return null;
    }
}
