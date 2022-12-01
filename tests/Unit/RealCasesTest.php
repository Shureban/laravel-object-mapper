<?php

namespace Shureban\LaravelObjectMapper\Tests\Unit;

use Shureban\LaravelObjectMapper\ObjectMapper;
use Shureban\LaravelObjectMapper\Tests\Unit\Structs\RealCases\Exld001ArchiveClass;
use Tests\TestCase;

class RealCasesTest extends TestCase
{
    public function test_JsonObject()
    {
        $json = '{"data":{"id":"23e4036d-cccb-4289-84a2-45ac47a03606","bucket":{"id":"94223aaa-fb1f-427c-aed6-0bf6e8939252","size":8067726,"resources":18,"content_objects":0,"content_size":0,"video_objects":0,"video_size":0,"audio_objects":0,"audio_size":0,"archive_objects":18,"archive_size":8067726,"document_objects":0,"document_size":0,"name":"Hintz, Spinka and Shanahan","description":"Organization bucket: Hintz, Spinka and Shanahan","created_at":1668256624,"updated_at":1669915052},"size":448207,"width":null,"height":null,"duration":null,"content_type":"archive","object_type":"file","url":"https:\/\/cs002.ote.exldservice.com\/archives\/94223aaa-fb1f-427c-aed6-0bf6e8939252\/archive\/23e4036d-cccb-4289-84a2-45ac47a03606\/yLskNckJ1eZExYjBmVIMuXTvPXcL2DqbJsute8YI.zip","name":"phpR80fOK","ext":"zip","labels":null,"mime_type":"application\/zip","file_map":{"files":[{"name":"index.html","size":15990,"mime_type":"text\/html","extension":"html"}],"directories":{"images":{"files":[{"name":"favicon-16x16.png","size":757,"mime_type":"image\/png","extension":"png"},{"name":"favicon.ico","size":15406,"mime_type":"image\/vnd.microsoft.icon","extension":"ico"},{"name":".DS_Store","size":6148,"mime_type":"application\/octet-stream","extension":"DS_Store"},{"name":"android-chrome-192x192.png","size":46331,"mime_type":"image\/png","extension":"png"},{"name":"apple-touch-icon.png","size":41345,"mime_type":"image\/png","extension":"png"},{"name":"ring.png","size":13447,"mime_type":"image\/png","extension":"png"},{"name":"android-chrome-512x512.png","size":259752,"mime_type":"image\/png","extension":"png"},{"name":"favicon-32x32.png","size":1977,"mime_type":"image\/png","extension":"png"},{"name":"bg.png","size":72488,"mime_type":"image\/png","extension":"png"}],"directories":[]},"__MACOSX":{"files":[{"name":"._index.html","size":176,"mime_type":"application\/octet-stream","extension":"html"}],"directories":{"images":{"files":[{"name":"._favicon-16x16.png","size":212,"mime_type":"application\/octet-stream","extension":"png"},{"name":"._favicon.ico","size":212,"mime_type":"application\/octet-stream","extension":"ico"},{"name":"._.DS_Store","size":120,"mime_type":"application\/octet-stream","extension":"DS_Store"},{"name":"._android-chrome-192x192.png","size":212,"mime_type":"application\/octet-stream","extension":"png"},{"name":"._apple-touch-icon.png","size":212,"mime_type":"application\/octet-stream","extension":"png"},{"name":"._ring.png","size":331,"mime_type":"application\/octet-stream","extension":"png"},{"name":"._android-chrome-512x512.png","size":212,"mime_type":"application\/octet-stream","extension":"png"},{"name":"._favicon-32x32.png","size":212,"mime_type":"application\/octet-stream","extension":"png"}],"directories":[]}}}}},"created_at":1669915052,"updated_at":1669915052}}';
        $json = json_decode($json);

        $this->assertEquals("23e4036d-cccb-4289-84a2-45ac47a03606", (new ObjectMapper(new Exld001ArchiveClass()))->mapFromArray((array)$json->data)->id);
    }
}
